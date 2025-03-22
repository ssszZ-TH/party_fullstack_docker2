เข้าใจครับ! คุณต้องการให้ผมแปลง SQL CRUD script ทั้งหมดจากรูปแบบ `$1, $2, $3, ...` เป็น named placeholders เช่น `:fromdate, :thrudate, ...` เพื่อให้สะดวกต่อการใช้งานใน PHP (เช่น PDO) ผมจะแปลงโค้ดทั้งหมดจากที่ให้มาก่อนหน้านี้ให้เป็นรูปแบบนี้ โดยเน้นเฉพาะโค้ดตามที่คุณขอ

---

### 1. `classify_by_eeoc`

#### CREATE
```sql
-- Step 1: Insert into party_classification
INSERT INTO party_classification (fromdate, thrudate, party_id, party_type_id)
VALUES (:fromdate, :thrudate, :party_id, :party_type_id)
RETURNING id;

-- Step 2: Insert into person_classification
INSERT INTO person_classification (id)
VALUES (:id);

-- Step 3: Insert into classify_by_eeoc
INSERT INTO classify_by_eeoc (id, ethnicity_id)
VALUES (:id, :ethnicity_id);
```

#### READ (Get by ID)
```sql
SELECT party_cl.id, party_cl.fromdate, party_cl.thrudate, party_cl.party_id, party_cl.party_type_id, ce.ethnicity_id, e.name_en, e.name_th
FROM classify_by_eeoc ce
JOIN person_classification person_cl ON ce.id = person_cl.id
JOIN party_classification party_cl ON ce.id = party_cl.id
JOIN ethnicity e ON ce.ethnicity_id = e.id
WHERE ce.id = :id;
```

#### READ (Get all)
```sql
SELECT party_cl.id, party_cl.fromdate, party_cl.thrudate, party_cl.party_id, party_cl.party_type_id, ce.ethnicity_id, e.name_en, e.name_th
FROM classify_by_eeoc ce
JOIN person_classification person_cl ON ce.id = person_cl.id
JOIN party_classification party_cl ON ce.id = party_cl.id
JOIN ethnicity e ON ce.ethnicity_id = e.id;
```

#### UPDATE
```sql
-- Update party_classification
UPDATE party_classification
SET fromdate = :fromdate, thrudate = :thrudate, party_id = :party_id, party_type_id = :party_type_id
WHERE id = :id;

-- Update classify_by_eeoc
UPDATE classify_by_eeoc
SET ethnicity_id = :ethnicity_id
WHERE id = :id;
```

#### DELETE
```sql
-- Step 1: Delete from classify_by_eeoc
DELETE FROM classify_by_eeoc
WHERE id = :id;

-- Step 2: Delete from person_classification
DELETE FROM person_classification
WHERE id = :id;

-- Step 3: Delete from party_classification
DELETE FROM party_classification
WHERE id = :id;
```

---

### 2. `classify_by_income`

#### CREATE
```sql
-- Step 1: Insert into party_classification
INSERT INTO party_classification (fromdate, thrudate, party_id, party_type_id)
VALUES (:fromdate, :thrudate, :party_id, :party_type_id)
RETURNING id;

-- Step 2: Insert into person_classification
INSERT INTO person_classification (id)
VALUES (:id);

-- Step 3: Insert into classify_by_income
INSERT INTO classify_by_income (id, income_range_id)
VALUES (:id, :income_range_id);
```

#### READ (Get by ID)
```sql
SELECT party_cl.id, party_cl.fromdate, party_cl.thrudate, party_cl.party_id, party_cl.party_type_id, ci.income_range_id, ir.description
FROM classify_by_income ci
JOIN person_classification person_cl ON ci.id = person_cl.id
JOIN party_classification party_cl ON ci.id = party_cl.id
JOIN income_range ir ON ci.income_range_id = ir.id
WHERE ci.id = :id;
```

#### READ (Get all)
```sql
SELECT party_cl.id, party_cl.fromdate, party_cl.thrudate, party_cl.party_id, party_cl.party_type_id, ci.income_range_id, ir.description
FROM classify_by_income ci
JOIN person_classification person_cl ON ci.id = person_cl.id
JOIN party_classification party_cl ON ci.id = party_cl.id
JOIN income_range ir ON ci.income_range_id = ir.id;
```

#### UPDATE
```sql
-- Update party_classification
UPDATE party_classification
SET fromdate = :fromdate, thrudate = :thrudate, party_id = :party_id, party_type_id = :party_type_id
WHERE id = :id;

-- Update classify_by_income
UPDATE classify_by_income
SET income_range_id = :income_range_id
WHERE id = :id;
```

#### DELETE
```sql
-- Step 1: Delete from classify_by_income
DELETE FROM classify_by_income
WHERE id = :id;

-- Step 2: Delete from person_classification
DELETE FROM person_classification
WHERE id = :id;

-- Step 3: Delete from party_classification
DELETE FROM party_classification
WHERE id = :id;
```

---

### 3. `classify_by_industry`

#### CREATE
```sql
-- Step 1: Insert into party_classification
INSERT INTO party_classification (fromdate, thrudate, party_id, party_type_id)
VALUES (:fromdate, :thrudate, :party_id, :party_type_id)
RETURNING id;

-- Step 2: Insert into organization_classification
INSERT INTO organization_classification (id)
VALUES (:id);

-- Step 3: Insert into classify_by_industry
INSERT INTO classify_by_industry (id, industry_type_id)
VALUES (:id, :industry_type_id);
```

#### READ (Get by ID)
```sql
SELECT party_cl.id, party_cl.fromdate, party_cl.thrudate, party_cl.party_id, party_cl.party_type_id, ci.industry_type_id, it.naics_code, it.description
FROM classify_by_industry ci
JOIN organization_classification org_cl ON ci.id = org_cl.id
JOIN party_classification party_cl ON ci.id = party_cl.id
JOIN industry_type it ON ci.industry_type_id = it.id
WHERE ci.id = :id;
```

#### READ (Get all)
```sql
SELECT party_cl.id, party_cl.fromdate, party_cl.thrudate, party_cl.party_id, party_cl.party_type_id, ci.industry_type_id, it.naics_code, it.description
FROM classify_by_industry ci
JOIN organization_classification org_cl ON ci.id = org_cl.id
JOIN party_classification party_cl ON ci.id = party_cl.id
JOIN industry_type it ON ci.industry_type_id = it.id;
```

#### UPDATE
```sql
-- Update party_classification
UPDATE party_classification
SET fromdate = :fromdate, thrudate = :thrudate, party_id = :party_id, party_type_id = :party_type_id
WHERE id = :id;

-- Update classify_by_industry
UPDATE classify_by_industry
SET industry_type_id = :industry_type_id
WHERE id = :id;
```

#### DELETE
```sql
-- Step 1: Delete from classify_by_industry
DELETE FROM classify_by_industry
WHERE id = :id;

-- Step 2: Delete from organization_classification
DELETE FROM organization_classification
WHERE id = :id;

-- Step 3: Delete from party_classification
DELETE FROM party_classification
WHERE id = :id;
```

---

### 4. `classify_by_size`

#### CREATE
```sql
-- Step 1: Insert into party_classification
INSERT INTO party_classification (fromdate, thrudate, party_id, party_type_id)
VALUES (:fromdate, :thrudate, :party_id, :party_type_id)
RETURNING id;

-- Step 2: Insert into organization_classification
INSERT INTO organization_classification (id)
VALUES (:id);

-- Step 3: Insert into classify_by_size
INSERT INTO classify_by_size (id, employee_count_range_id)
VALUES (:id, :employee_count_range_id);
```

#### READ (Get by ID)
```sql
SELECT party_cl.id, party_cl.fromdate, party_cl.thrudate, party_cl.party_id, party_cl.party_type_id, cs.employee_count_range_id, ecr.description
FROM classify_by_size cs
JOIN organization_classification org_cl ON cs.id = org_cl.id
JOIN party_classification party_cl ON cs.id = party_cl.id
JOIN employee_count_range ecr ON cs.employee_count_range_id = ecr.id
WHERE cs.id = :id;
```

#### READ (Get all)
```sql
SELECT party_cl.id, party_cl.fromdate, party_cl.thrudate, party_cl.party_id, party_cl.party_type_id, cs.employee_count_range_id, ecr.description
FROM classify_by_size cs
JOIN organization_classification org_cl ON cs.id = org_cl.id
JOIN party_classification party_cl ON cs.id = party_cl.id
JOIN employee_count_range ecr ON cs.employee_count_range_id = ecr.id;
```

#### UPDATE
```sql
-- Update party_classification
UPDATE party_classification
SET fromdate = :fromdate, thrudate = :thrudate, party_id = :party_id, party_type_id = :party_type_id
WHERE id = :id;

-- Update classify_by_size
UPDATE classify_by_size
SET employee_count_range_id = :employee_count_range_id
WHERE id = :id;
```

#### DELETE
```sql
-- Step 1: Delete from classify_by_size
DELETE FROM classify_by_size
WHERE id = :id;

-- Step 2: Delete from organization_classification
DELETE FROM organization_classification
WHERE id = :id;

-- Step 3: Delete from party_classification
DELETE FROM party_classification
WHERE id = :id;
```

---

### 5. `classify_by_minority`

#### CREATE
```sql
-- Step 1: Insert into party_classification
INSERT INTO party_classification (fromdate, thrudate, party_id, party_type_id)
VALUES (:fromdate, :thrudate, :party_id, :party_type_id)
RETURNING id;

-- Step 2: Insert into organization_classification
INSERT INTO organization_classification (id)
VALUES (:id);

-- Step 3: Insert into classify_by_minority
INSERT INTO classify_by_minority (id, minority_type_id)
VALUES (:id, :minority_type_id);
```

#### READ (Get by ID)
```sql
SELECT party_cl.id, party_cl.fromdate, party_cl.thrudate, party_cl.party_id, party_cl.party_type_id, cm.minority_type_id, mt.name_en, mt.name_th
FROM classify_by_minority cm
JOIN organization_classification org_cl ON cm.id = org_cl.id
JOIN party_classification party_cl ON cm.id = party_cl.id
JOIN minority_type mt ON cm.minority_type_id = mt.id
WHERE cm.id = :id;
```

#### READ (Get all)
```sql
SELECT party_cl.id, party_cl.fromdate, party_cl.thrudate, party_cl.party_id, party_cl.party_type_id, cm.minority_type_id, mt.name_en, mt.name_th
FROM classify_by_minority cm
JOIN organization_classification org_cl ON cm.id = org_cl.id
JOIN party_classification party_cl ON cm.id = party_cl.id
JOIN minority_type mt ON cm.minority_type_id = mt.id;
```

#### UPDATE
```sql
-- Update party_classification
UPDATE party_classification
SET fromdate = :fromdate, thrudate = :thrudate, party_id = :party_id, party_type_id = :party_type_id
WHERE id = :id;

-- Update classify_by_minority
UPDATE classify_by_minority
SET minority_type_id = :minority_type_id
WHERE id = :id;
```

#### DELETE
```sql
-- Step 1: Delete from classify_by_minority
DELETE FROM classify_by_minority
WHERE id = :id;

-- Step 2: Delete from organization_classification
DELETE FROM organization_classification
WHERE id = :id;

-- Step 3: Delete from party_classification
DELETE FROM party_classification
WHERE id = :id;
```

---

### หมายเหตุ
- ผมใช้ named placeholders ที่สอดคล้องกับชื่อคอลัมน์ เช่น `:fromdate`, `:party_id`, `:ethnicity_id` เพื่อให้อ่านง่ายและนำไป bind ใน PHP ได้สะดวก
- ใน `CREATE` ผมสมมติว่า `:id` ใน Step 2 และ Step 3 จะได้จาก `RETURNING id` ของ Step 1 ซึ่งคุณต้องจัดการในโค้ด PHP เพื่อส่งค่า `id` ที่ได้กลับมา

เก็บโค้ดนี้ไว้พัฒนาต่อได้เลยครับ! ถ้ามีอะไรต้องปรับหรืออยากให้ช่วยเพิ่ม เช่น ตัวอย่างการใช้ใน Slim PHP บอกมาได้เลยนะครับ