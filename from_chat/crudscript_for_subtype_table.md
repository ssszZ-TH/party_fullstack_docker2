ผมจะช่วยเขียนสคริปต์ SQL สำหรับการดำเนินการ **CRUD** (Create, Read, Update, Delete) สำหรับตาราง `party` (supertype) และ `person` (subtype) โดยคำนึงถึงความสัมพันธ์แบบ supertype-subtype และการที่ `person.id` เป็น foreign key ที่อ้างอิง `party.id` สคริปต์นี้จะเป็น **native SQL** ที่คุณสามารถนำไปใช้ใน API ได้ (เช่น ผ่าน JDBC, Spring Data JPA Native Query, หรือเครื่องมืออื่นๆ) ผมจะอธิบายแต่ละส่วนให้ชัดเจนด้วยครับ

---

### ความสัมพันธ์ระหว่าง `party` และ `person`
- **`party`** เป็น supertype มี `id` เป็น primary key
- **`person`** เป็น subtype ที่สืบทอด `id` จาก `party` โดย `person.id` เป็นทั้ง primary key และ foreign key ที่อ้างอิง `party.id`
- ในการดำเนินการ CRUD:
  - **Insert**: ต้องเพิ่มข้อมูลใน `party` ก่อน แล้วค่อยเพิ่มใน `person` โดยใช้ `id` เดียวกัน
  - **Read**: อาจต้อง join `party` และ `person` เพื่อดึงข้อมูลทั้งหมด
  - **Update**: อัปเดตได้ทั้ง `party` และ `person` แยกกัน แต่ต้องระวัง `id`
  - **Delete**: ลบ `person` ก่อน แล้วค่อยลบ `party` เนื่องจาก foreign key constraint

---

### 1. CREATE (Insert)
#### Insert ข้อมูลใหม่ (party และ person)
เนื่องจาก `person` เป็น subtype ของ `party` เราต้อง:
1. แทรกข้อมูลลงใน `party` ก่อน
2. ใช้ `id` ที่ได้จาก `party` ไปแทรกใน `person`

```sql
-- Step 1: Insert into party (supertype)
INSERT INTO public.party (id) 
VALUES (DEFAULT) 
RETURNING id;

-- Step 2: Insert into person (subtype) using the returned id
INSERT INTO public.person (
    id, 
    socialsecuritynumber, 
    birthdate, 
    mothermaidenname, 
    totalyearworkexperience, 
    comment
) VALUES (
    :party_id,           -- ใช้ id ที่ได้จาก party
    :socialsecuritynumber, 
    :birthdate, 
    :mothermaidenname, 
    :totalyearworkexperience, 
    :comment
);
```

#### อธิบาย
- `:party_id`, `:socialsecuritynumber`, ฯลฯ เป็น placeholder ที่คุณจะแทนด้วยค่าจริงใน API (เช่น ผ่าน PreparedStatement ใน Java)
- `RETURNING id` คืนค่า `id` ที่สร้างใน `party` เพื่อนำไปใช้ใน `person`
- ใน API คุณอาจต้อง:
  1. รันคำสั่งแรกเพื่อได้ `id`
  2. รันคำสั่งที่สองโดยส่ง `id` ที่ได้ไป

#### ตัวอย่างการรวมเป็น transaction (ถ้า API รองรับ)
```sql
DO $$
DECLARE
    new_id INTEGER;
BEGIN
    -- Insert into party and get the id
    INSERT INTO public.party (id) 
    VALUES (DEFAULT) 
    RETURNING id INTO new_id;

    -- Insert into person with the same id
    INSERT INTO public.person (
        id, 
        socialsecuritynumber, 
        birthdate, 
        mothermaidenname, 
        totalyearworkexperience, 
        comment
    ) VALUES (
        new_id,
        '123-45-6789',
        '1990-05-15',
        'Smith',
        10,
        'Software Engineer'
    );
END $$;
```

---

### 2. READ (Select)
#### อ่านข้อมูลทั้งหมดของ person (รวมข้อมูลจาก party)
เนื่องจาก `person` เป็น subtype เราจะ join กับ `party` เพื่อให้ได้ข้อมูลครบถ้วน

```sql
-- Read all persons with their party info
SELECT 
    p.id,
    p.socialsecuritynumber,
    p.birthdate,
    p.mothermaidenname,
    p.totalyearworkexperience,
    p.comment
FROM public.person p
JOIN public.party pt ON p.id = pt.id;
```

#### อ่านข้อมูลเฉพาะ ID
```sql
-- Read a specific person by id
SELECT 
    p.id,
    p.socialsecuritynumber,
    p.birthdate,
    p.mothermaidenname,
    p.totalyearworkexperience,
    p.comment
FROM public.person p
JOIN public.party pt ON p.id = pt.id
WHERE p.id = :id;
```

#### อธิบาย
- `JOIN public.party` จำเป็นเพื่อให้แน่ใจว่า `id` มีอยู่ใน `party` (ถึงแม้ในกรณีนี้ `party` ไม่มีคอลัมน์เพิ่มเติม แต่ถ้ามีในอนาคตจะได้ครอบคลุม)
- `:id` เป็น placeholder สำหรับ API

---

### 3. UPDATE
#### อัปเดตข้อมูล person
เนื่องจาก `party` ในที่นี้มีแค่ `id` และไม่มีข้อมูลอื่นให้อัปเดต เราจะอัปเดตเฉพาะ `person` ได้เลย

```sql
-- Update person data
UPDATE public.person 
SET 
    socialsecuritynumber = :socialsecuritynumber,
    birthdate = :birthdate,
    mothermaidenname = :mothermaidenname,
    totalyearworkexperience = :totalyearworkexperience,
    comment = :comment
WHERE id = :id;
```

#### อธิบาย
- อัปเดตเฉพาะคอลัมน์ใน `person` เพราะ `id` เป็น primary key ที่ไม่เปลี่ยนแปลง
- ถ้า `party` มีคอลัมน์เพิ่มเติม (เช่น `partytype_id`) ในอนาคต คุณต้องอัปเดต `party` แยกด้วย

#### ตัวอย่างถ้ามีการอัปเดตทั้ง party และ person
สมมติว่า `party` มี `partytype_id` (ตามสคริปต์ก่อนหน้า):
```sql
-- Update party (if it has additional fields)
UPDATE public.party 
SET 
    partytype_id = :partytype_id
WHERE id = :id;

-- Update person
UPDATE public.person 
SET 
    socialsecuritynumber = :socialsecuritynumber,
    birthdate = :birthdate,
    mothermaidenname = :mothermaidenname,
    totalyearworkexperience = :totalyearworkexperience,
    comment = :comment
WHERE id = :id;
```

---

### 4. DELETE
#### ลบข้อมูล person และ party
เนื่องจาก `person.id` อ้างอิง `party.id` เราต้องลบ `person` ก่อน แล้วค่อยลบ `party`

```sql
-- Step 1: Delete from person (subtype)
DELETE FROM public.person 
WHERE id = :id;

-- Step 2: Delete from party (supertype)
DELETE FROM public.party 
WHERE id = :id;
```

#### อธิบาย
- ลบ `person` ก่อนเพื่อไม่ให้违反 foreign key constraint
- ถ้ามี subtype อื่นของ `party` (เช่น `organization`) คุณต้องตรวจสอบและลบ subtype เหล่านั้นก่อนด้วย

#### ตัวอย่างการรวมเป็น transaction
```sql
DO $$
BEGIN
    -- Delete from person first
    DELETE FROM public.person 
    WHERE id = :id;

    -- Then delete from party
    DELETE FROM public.party 
    WHERE id = :id;
END $$;
```

---

### การนำไปใช้ใน API (Native Query)
สมมติคุณใช้ **Spring Boot** กับ **JPA** และ native query:

#### Repository (Java)
```java
@Repository
public interface PartyRepository extends JpaRepository<Party, Integer> {
    @Query(value = "INSERT INTO party (id) VALUES (DEFAULT) RETURNING id", nativeQuery = true)
    Integer createParty();

    @Query(value = "INSERT INTO person (id, socialsecuritynumber, birthdate, mothermaidenname, totalyearworkexperience, comment) " +
                   "VALUES (:id, :ssn, :birthdate, :mothermaidenname, :experience, :comment)", nativeQuery = true)
    void createPerson(@Param("id") Integer id, 
                      @Param("ssn") String socialsecuritynumber, 
                      @Param("birthdate") Date birthdate, 
                      @Param("mothermaidenname") String mothermaidenname, 
                      @Param("experience") Integer totalyearworkexperience, 
                      @Param("comment") String comment);

    @Query(value = "SELECT p.id, p.socialsecuritynumber, p.birthdate, p.mothermaidenname, p.totalyearworkexperience, p.comment " +
                   "FROM person p JOIN party pt ON p.id = pt.id WHERE p.id = :id", nativeQuery = true)
    Map<String, Object> findPersonById(@Param("id") Integer id);

    @Modifying
    @Query(value = "UPDATE person SET socialsecuritynumber = :ssn, birthdate = :birthdate, mothermaidenname = :mothermaidenname, " +
                   "totalyearworkexperience = :experience, comment = :comment WHERE id = :id", nativeQuery = true)
    void updatePerson(@Param("id") Integer id, 
                      @Param("ssn") String socialsecuritynumber, 
                      @Param("birthdate") Date birthdate, 
                      @Param("mothermaidenname") String mothermaidenname, 
                      @Param("experience") Integer totalyearworkexperience, 
                      @Param("comment") String comment);

    @Modifying
    @Query(value = "DELETE FROM person WHERE id = :id", nativeQuery = true)
    void deletePerson(@Param("id") Integer id);

    @Modifying
    @Query(value = "DELETE FROM party WHERE id = :id", nativeQuery = true)
    void deleteParty(@Param("id") Integer id);
}
```

#### Service (Java)
```java
@Service
public class PartyService {
    @Autowired
    private PartyRepository partyRepository;

    @Transactional
    public Integer createPerson(PersonDTO dto) {
        Integer partyId = partyRepository.createParty();
        partyRepository.createPerson(partyId, dto.getSocialSecurityNumber(), dto.getBirthdate(), 
                                     dto.getMotherMaidenName(), dto.getTotalYearWorkExperience(), dto.getComment());
        return partyId;
    }

    public Map<String, Object> getPerson(Integer id) {
        return partyRepository.findPersonById(id);
    }

    @Transactional
    public void updatePerson(Integer id, PersonDTO dto) {
        partyRepository.updatePerson(id, dto.getSocialSecurityNumber(), dto.getBirthdate(), 
                                     dto.getMotherMaidenName(), dto.getTotalYearWorkExperience(), dto.getComment());
    }

    @Transactional
    public void deletePerson(Integer id) {
        partyRepository.deletePerson(id);
        partyRepository.deleteParty(id);
    }
}
```

---

### สรุป
- **Insert**: แทรก `party` ก่อน แล้วใช้ `id` ใน `person`
- **Read**: Join `party` และ `person` เพื่อดึงข้อมูล
- **Update**: อัปเดต `person` ได้โดยตรง (ถ้ามีข้อมูลใน `party` ให้อัปเดตแยก)
- **Delete**: ลบ `person` ก่อน แล้วค่อยลบ `party`

ถ้าคุณต้องการปรับแต่งเพิ่มเติม (เช่น เพิ่มเงื่อนไข, error handling) หรือใช้ภาษา/เฟรมเวิร์กอื่น บอกมาได้เลยครับ!