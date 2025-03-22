# code นี้ ผิด ติด bug person classification กับ party classification ตั้งชื่อ PC เหมือนกัน

เข้าใจครับ! คุณต้องการให้การสร้าง (CREATE) เริ่มจาก supertype (`party_classification`) ก่อน แล้วค่อยลงไป subtype (`person_classification` หรือ `organization_classification`) และสุดท้ายถึง subtype ใน Layer 3 (`classify_by_eeoc`, `classify_by_income`, etc.) และสำหรับ DELETE ก็ไม่ใช้ ON CASCADE แต่ต้องจัดการลบจากล่างขึ้นบน (bottom-up) ผมจะเขียน CRUD SQL ใหม่ทั้งหมดตามลำดับนี้ โดยเน้นเฉพาะโค้ดอย่างเดียวตามที่คุณขอ และจะ save ไว้ให้คุณพัฒนาต่อ

---

### 1. `classify_by_eeoc`

#### CREATE
```sql
-- Step 1: Insert into party_classification
INSERT INTO party_classification (fromdate, thrudate, party_id, party_type_id)
VALUES ($1, $2, $3, $4)
RETURNING id;

-- Step 2: Insert into person_classification
INSERT INTO person_classification (id)
VALUES ($5);

-- Step 3: Insert into classify_by_eeoc
INSERT INTO classify_by_eeoc (id, ethnicity_id)
VALUES ($5, $6);
```

#### READ (Get by ID)
```sql
SELECT pc.fromdate, pc.thrudate, pc.party_id, pc.party_type_id, ce.ethnicity_id, e.name_en, e.name_th
FROM classify_by_eeoc ce
JOIN person_classification pc ON ce.id = pc.id
JOIN party_classification pc ON ce.id = pc.id
JOIN ethnicity e ON ce.ethnicity_id = e.id
WHERE ce.id = $1;
```

#### READ (Get all)
```sql
SELECT pc.fromdate, pc.thrudate, pc.party_id, pc.party_type_id, ce.ethnicity_id, e.name_en, e.name_th
FROM classify_by_eeoc ce
JOIN person_classification pc ON ce.id = pc.id
JOIN party_classification pc ON ce.id = pc.id
JOIN ethnicity e ON ce.ethnicity_id = e.id;
```

#### UPDATE
```sql
-- Update party_classification
UPDATE party_classification
SET fromdate = $2, thrudate = $3, party_id = $4, party_type_id = $5
WHERE id = $1;

-- Update classify_by_eeoc
UPDATE classify_by_eeoc
SET ethnicity_id = $6
WHERE id = $1;
```

#### DELETE
```sql
-- Step 1: Delete from classify_by_eeoc
DELETE FROM classify_by_eeoc
WHERE id = $1;

-- Step 2: Delete from person_classification
DELETE FROM person_classification
WHERE id = $1;

-- Step 3: Delete from party_classification
DELETE FROM party_classification
WHERE id = $1;
```

---

### 2. `classify_by_income`

#### CREATE
```sql
-- Step 1: Insert into party_classification
INSERT INTO party_classification (fromdate, thrudate, party_id, party_type_id)
VALUES ($1, $2, $3, $4)
RETURNING id;

-- Step 2: Insert into person_classification
INSERT INTO person_classification (id)
VALUES ($5);

-- Step 3: Insert into classify_by_income
INSERT INTO classify_by_income (id, income_range_id)
VALUES ($5, $6);
```

#### READ (Get by ID)
```sql
SELECT pc.fromdate, pc.thrudate, pc.party_id, pc.party_type_id, ci.income_range_id, ir.description
FROM classify_by_income ci
JOIN person_classification pc ON ci.id = pc.id
JOIN party_classification pc ON ci.id = pc.id
JOIN income_range ir ON ci.income_range_id = ir.id
WHERE ci.id = $1;
```

#### READ (Get all)
```sql
SELECT pc.fromdate, pc.thrudate, pc.party_id, pc.party_type_id, ci.income_range_id, ir.description
FROM classify_by_income ci
JOIN person_classification pc ON ci.id = pc.id
JOIN party_classification pc ON ci.id = pc.id
JOIN income_range ir ON ci.income_range_id = ir.id;
```

#### UPDATE
```sql
-- Update party_classification
UPDATE party_classification
SET fromdate = $2, thrudate = $3, party_id = $4, party_type_id = $5
WHERE id = $1;

-- Update classify_by_income
UPDATE classify_by_income
SET income_range_id = $6
WHERE id = $1;
```

#### DELETE
```sql
-- Step 1: Delete from classify_by_income
DELETE FROM classify_by_income
WHERE id = $1;

-- Step 2: Delete from person_classification
DELETE FROM person_classification
WHERE id = $1;

-- Step 3: Delete from party_classification
DELETE FROM party_classification
WHERE id = $1;
```

---

### 3. `classify_by_industry`

#### CREATE
```sql
-- Step 1: Insert into party_classification
INSERT INTO party_classification (fromdate, thrudate, party_id, party_type_id)
VALUES ($1, $2, $3, $4)
RETURNING id;

-- Step 2: Insert into organization_classification
INSERT INTO organization_classification (id)
VALUES ($5);

-- Step 3: Insert into classify_by_industry
INSERT INTO classify_by_industry (id, industry_type_id)
VALUES ($5, $6);
```

#### READ (Get by ID)
```sql
SELECT pc.fromdate, pc.thrudate, pc.party_id, pc.party_type_id, ci.industry_type_id, it.naics_code, it.description
FROM classify_by_industry ci
JOIN organization_classification oc ON ci.id = oc.id
JOIN party_classification pc ON ci.id = pc.id
JOIN industry_type it ON ci.industry_type_id = it.id
WHERE ci.id = $1;
```

#### READ (Get all)
```sql
SELECT pc.fromdate, pc.thrudate, pc.party_id, pc.party_type_id, ci.industry_type_id, it.naics_code, it.description
FROM classify_by_industry ci
JOIN organization_classification oc ON ci.id = oc.id
JOIN party_classification pc ON ci.id = pc.id
JOIN industry_type it ON ci.industry_type_id = it.id;
```

#### UPDATE
```sql
-- Update party_classification
UPDATE party_classification
SET fromdate = $2, thrudate = $3, party_id = $4, party_type_id = $5
WHERE id = $1;

-- Update classify_by_industry
UPDATE classify_by_industry
SET industry_type_id = $6
WHERE id = $1;
```

#### DELETE
```sql
-- Step 1: Delete from classify_by_industry
DELETE FROM classify_by_industry
WHERE id = $1;

-- Step 2: Delete from organization_classification
DELETE FROM organization_classification
WHERE id = $1;

-- Step 3: Delete from party_classification
DELETE FROM party_classification
WHERE id = $1;
```

---

### 4. `classify_by_size`

#### CREATE
```sql
-- Step 1: Insert into party_classification
INSERT INTO party_classification (fromdate, thrudate, party_id, party_type_id)
VALUES ($1, $2, $3, $4)
RETURNING id;

-- Step 2: Insert into organization_classification
INSERT INTO organization_classification (id)
VALUES ($5);

-- Step 3: Insert into classify_by_size
INSERT INTO classify_by_size (id, employee_count_range_id)
VALUES ($5, $6);
```

#### READ (Get by ID)
```sql
SELECT pc.fromdate, pc.thrudate, pc.party_id, pc.party_type_id, cs.employee_count_range_id, ecr.description
FROM classify_by_size cs
JOIN organization_classification oc ON cs.id = oc.id
JOIN party_classification pc ON cs.id = pc.id
JOIN employee_count_range ecr ON cs.employee_count_range_id = ecr.id
WHERE cs.id = $1;
```

#### READ (Get all)
```sql
SELECT pc.fromdate, pc.thrudate, pc.party_id, pc.party_type_id, cs.employee_count_range_id, ecr.description
FROM classify_by_size cs
JOIN organization_classification oc ON cs.id = oc.id
JOIN party_classification pc ON cs.id = pc.id
JOIN employee_count_range ecr ON cs.employee_count_range_id = ecr.id;
```

#### UPDATE
```sql
-- Update party_classification
UPDATE party_classification
SET fromdate = $2, thrudate = $3, party_id = $4, party_type_id = $5
WHERE id = $1;

-- Update classify_by_size
UPDATE classify_by_size
SET employee_count_range_id = $6
WHERE id = $1;
```

#### DELETE
```sql
-- Step 1: Delete from classify_by_size
DELETE FROM classify_by_size
WHERE id = $1;

-- Step 2: Delete from organization_classification
DELETE FROM organization_classification
WHERE id = $1;

-- Step 3: Delete from party_classification
DELETE FROM party_classification
WHERE id = $1;
```

---

### 5. `classify_by_minority`

#### CREATE
```sql
-- Step 1: Insert into party_classification
INSERT INTO party_classification (fromdate, thrudate, party_id, party_type安置)
VALUES ($1, $2, $3, $4)
RETURNING id;

-- Step 2: Insert into organization_classification
INSERT INTO organization_classification (id)
VALUES ($5);

-- Step 3: Insert into classify_by_minority
INSERT INTO classify_by_minority (id, minority_type_id)
VALUES ($5, $6);
```

#### READ (Get by ID)
```sql
SELECT pc.fromdate, pc.thrudate, pc.party_id, pc.party_type_id, cm.minority_type_id, mt.name_en, mt.name_th
FROM classify_by_minority cm
JOIN organization_classification oc ON cm.id = oc.id
JOIN party_classification pc ON cm.id = pc.id
JOIN minority_type mt ON cm.minority_type_id = mt.id
WHERE cm.id = $1;
```

#### READ (Get all)
```sql
SELECT pc.fromdate, pc.thrudate, pc.party_id, pc.party_type_id, cm.minority_type_id, mt.name_en, mt.name_th
FROM classify_by_minority cm
JOIN organization_classification oc ON cm.id = oc.id
JOIN party_classification pc ON cm.id = pc.id
JOIN minority_type mt ON cm.minority_type_id = mt.id;
```

#### UPDATE
```sql
-- Update party_classification
UPDATE party_classification
SET fromdate = $2, thrudate = $3, party_id = $4, party_type_id = $5
WHERE id = $1;

-- Update classify_by_minority
UPDATE classify_by_minority
SET minority_type_id = $6
WHERE id = $1;
```

#### DELETE
```sql
-- Step 1: Delete from classify_by_minority
DELETE FROM classify_by_minority
WHERE id = $1;

-- Step 2: Delete from organization_classification
DELETE FROM organization_classification
WHERE id = $1;

-- Step 3: Delete from party_classification
DELETE FROM party_classification
WHERE id = $1;
```

---

เก็บโค้ดนี้ไว้พัฒนาต่อได้เลยครับ! ถ้ามีอะไรต้องปรับหรืออยากให้ช่วยเพิ่มส่วนอื่น (เช่น Slim PHP endpoint หรือ transaction management) บอกมาได้เลยนะครับ