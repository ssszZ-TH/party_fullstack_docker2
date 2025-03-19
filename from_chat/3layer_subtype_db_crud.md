ผมจะช่วยสร้าง SQL CRUD (Create, Read, Update, Delete) script สำหรับตาราง `legal_organization` และ `informal_organization` ใน PostgreSQL โดยคำนึงถึงโครงสร้าง supertype/subtype ที่คุณให้มา ซึ่งมีลำดับชั้นดังนี้:
- **Supertype L1**: `party`
- **Supertype L2**: `organization`
- **Subtypes**: `informal_organization` และ `legal_organization`

เนื่องจากทั้งสองตารางมี foreign key อ้างถึง `organization` และ `party` เราจะต้องจัดการการ insert/update/delete ให้ครบทั้งสามตารางในลำดับชั้น

---

### โครงสร้างตาราง (สรุป)
- **`party`**: มี `id` (primary key)
- **`organization`**: มี `id` (foreign key อ้างถึง `party`), `name_en`, `name_th`
- **`informal_organization`**: มี `id` (foreign key อ้างถึง `organization`)
- **`legal_organization`**: มี `id` (foreign key อ้างถึง `organization`), `federal_tax_id_number`

---

### CRUD Script สำหรับ `legal_organization`

#### **CREATE**
```sql
-- เพิ่มข้อมูลใหม่ใน legal_organization (ต้อง insert เข้า party และ organization ก่อน)
INSERT INTO public.party (id) 
VALUES (DEFAULT) 
RETURNING id;

-- เก็บ id จาก party แล้ว insert เข้า organization
INSERT INTO public.organization (id, name_en, name_th)
VALUES (
    :party_id,           -- id จาก party
    :name_en,            -- ชื่อภาษาอังกฤษ
    :name_th             -- ชื่อภาษาไทย
) 
RETURNING id;

-- ใช้ id จาก organization เพื่อ insert เข้า legal_organization
INSERT INTO public.legal_organization (id, federal_tax_id_number)
VALUES (
    :organization_id,    -- id จาก organization
    :federal_tax_id_number  -- หมายเลขภาษี
)
RETURNING id;
```

#### **READ**
```sql
-- อ่านข้อมูล legal_organization เดียวตาม id
SELECT 
    p.id AS party_id,
    o.name_en,
    o.name_th,
    lo.federal_tax_id_number
FROM public.legal_organization lo
JOIN public.organization o ON lo.id = o.id
JOIN public.party p ON o.id = p.id
WHERE lo.id = :id;

-- อ่านข้อมูล legal_organization ทั้งหมด
SELECT 
    p.id AS party_id,
    o.name_en,
    o.name_th,
    lo.federal_tax_id_number
FROM public.legal_organization lo
JOIN public.organization o ON lo.id = o.id
JOIN public.party p ON o.id = p.id;
```

#### **UPDATE**
```sql
-- อัปเดตข้อมูล legal_organization (รวมถึง organization)
UPDATE public.organization o
SET 
    name_en = :name_en,
    name_th = :name_th
FROM public.legal_organization lo
WHERE o.id = lo.id AND lo.id = :id;

UPDATE public.legal_organization
SET 
    federal_tax_id_number = :federal_tax_id_number
WHERE id = :id;
```

#### **DELETE**
```sql
-- ลบข้อมูล legal_organization (ต้องลบจากล่างขึ้นบน)
DELETE FROM public.legal_organization WHERE id = :id;
DELETE FROM public.organization WHERE id = :id;
DELETE FROM public.party WHERE id = :id;
```

---

### CRUD Script สำหรับ `informal_organization`

#### **CREATE**
```sql
-- เพิ่มข้อมูลใหม่ใน informal_organization (ต้อง insert เข้า party และ organization ก่อน)
INSERT INTO public.party (id) 
VALUES (DEFAULT) 
RETURNING id;

-- เก็บ id จาก party แล้ว insert เข้า organization
INSERT INTO public.organization (id, name_en, name_th)
VALUES (
    :party_id,           -- id จาก party
    :name_en,            -- ชื่อภาษาอังกฤษ
    :name_th             -- ชื่อภาษาไทย
) 
RETURNING id;

-- ใช้ id จาก organization เพื่อ insert เข้า informal_organization
INSERT INTO public.informal_organization (id)
VALUES (
    :organization_id     -- id จาก organization
)
RETURNING id;
```

#### **READ**
```sql
-- อ่านข้อมูล informal_organization เดียวตาม id
SELECT 
    p.id AS party_id,
    o.name_en,
    o.name_th
FROM public.informal_organization io
JOIN public.organization o ON io.id = o.id
JOIN public.party p ON o.id = p.id
WHERE io.id = :id;

-- อ่านข้อมูล informal_organization ทั้งหมด
SELECT 
    p.id AS party_id,
    o.name_en,
    o.name_th
FROM public.informal_organization io
JOIN public.organization o ON io.id = o.id
JOIN public.party p ON o.id = p.id;
```

#### **UPDATE**
```sql
-- อัปเดตข้อมูล informal_organization (เฉพาะ organization เพราะ informal_organization มีแค่ id)
UPDATE public.organization o
SET 
    name_en = :name_en,
    name_th = :name_th
FROM public.informal_organization io
WHERE o.id = io.id AND io.id = :id;
```

#### **DELETE**
```sql
-- ลบข้อมูล informal_organization (ต้องลบจากล่างขึ้นบน)
DELETE FROM public.informal_organization WHERE id = :id;
DELETE FROM public.organization WHERE id = :id;
DELETE FROM public.party WHERE id = :id;
```

---

### การใช้งานกับ PHP/PDO
ถ้าคุณใช้ PHP กับ PDO (เหมือนโค้ดก่อนหน้านี้) ผมจะแปลง `legal_organization` CREATE เป็นตัวอย่างให้ดู:

#### PHP CREATE สำหรับ `legal_organization`
```php
public static function create($data)
{
    $pdo = self::getConnection();

    try {
        $pdo->beginTransaction();

        // 1. Insert into party
        $stmt1 = $pdo->prepare("INSERT INTO public.party (id) VALUES (DEFAULT) RETURNING id");
        $stmt1->execute();
        $party_id = $stmt1->fetchColumn();

        // 2. Insert into organization
        $stmt2 = $pdo->prepare("
            INSERT INTO public.organization (id, name_en, name_th)
            VALUES (:id, :name_en, :name_th)
            RETURNING id
        ");
        $stmt2->execute([
            'id' => $party_id,
            'name_en' => $data['name_en'],
            'name_th' => $data['name_th']
        ]);
        $org_id = $stmt2->fetchColumn();

        // 3. Insert into legal_organization
        $stmt3 = $pdo->prepare("
            INSERT INTO public.legal_organization (id, federal_tax_id_number)
            VALUES (:id, :federal_tax_id_number)
        ");
        $stmt3->execute([
            'id' => $org_id,
            'federal_tax_id_number' => $data['federal_tax_id_number']
        ]);

        $pdo->commit();
        return $party_id; // คืนค่า id
    } catch (PDOException $e) {
        $pdo->rollBack();
        throw $e;
    }
}
```

#### Input ตัวอย่าง
```json
{
    "name_en": "Example Corp",
    "name_th": "บริษัทตัวอย่าง",
    "federal_tax_id_number": "123-45-6789"
}
```

---

### ข้อควรระวัง
1. **Sequence**: แต่ละตารางมี sequence แยก (`party_id_seq`, `organization_id_seq`, ฯลฯ) ถ้า `id` ต้องต่อเนื่องกัน ต้องจัดการ sequence เอง
2. **Foreign Key**: การลบต้องลบจาก subtype ก่อน (เช่น `legal_organization`) แล้วค่อยลบ `organization` และ `party`
3. **Transaction**: ใช้ transaction เพื่อให้แน่ใจว่าทุก insert/update/delete สำเร็จพร้อมกัน

ถ้าต้องการ CRUD อื่นใน PHP หรือมีคำถามเพิ่ม บอกมาได้เลยครับ!