ผมจะช่วยปรับโค้ด `User` model ให้เชื่อมต่อกับ PostgreSQL จริง โดยใช้ข้อมูลจาก `docker-compose.yml` ที่คุณให้มา:
- **Database**: `myapp`
- **User**: `spa`
- **Password**: `spa`
- **Host**: `db`
- **Port**: `5432`

นอกจากนี้ ผมจะเพิ่มโค้ดสำหรับสร้างตาราง `users` ที่พร้อมใช้งาน และจัดเตรียมให้ครบสำหรับ CRUD operations

---

### โค้ดที่ปรับแล้ว: `src/Models/User.php`

```php
<?php
namespace App\Models;

use PDO;
use PDOException;

class User
{
    private static $pdo;

    // ฟังก์ชันเชื่อมต่อกับ PostgreSQL
    private static function getConnection()
    {
        if (!isset(self::$pdo)) {
            $dsn = "pgsql:host=db;port=5432;dbname=myapp";
            $username = "spa";
            $password = "spa";

            try {
                self::$pdo = new PDO($dsn, $username, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            } catch (PDOException $e) {
                throw new PDOException("Connection failed: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }

    // ดึงข้อมูลทั้งหมดจากตาราง users
    public static function all()
    {
        $pdo = self::getConnection();
        $stmt = $pdo->query("SELECT * FROM users");
        return $stmt->fetchAll();
    }

    // ดึงข้อมูลตาม ID
    public static function find($id)
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    // สร้างข้อมูลใหม่
    public static function create($data)
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare("INSERT INTO users (name) VALUES (:name) RETURNING id");
        $stmt->execute(['name' => $data['name']]);
        return $stmt->fetchColumn(); // คืนค่า ID ที่เพิ่งสร้าง
    }

    // อัปเดตข้อมูล
    public static function update($id, $data)
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare("UPDATE users SET name = :name WHERE id = :id");
        $stmt->execute([
            'id' => $id,
            'name' => $data['name']
        ]);
        return $stmt->rowCount(); // คืนค่าจำนวนแถวที่อัปเดต
    }

    // ลบข้อมูล
    public static function delete($id)
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount(); // คืนค่าจำนวนแถวที่ลบ
    }
}
```

---

### โค้ดสร้างตารางและข้อมูลเริ่มต้น
ผมจะสมมติว่าคุณต้องการตาราง `users` ที่มีคอลัมน์พื้นฐาน เช่น `id` และ `name` พร้อมข้อมูลตัวอย่าง และบันทึกเป็นไฟล์ SQL เพื่อให้ง่ายต่อการใช้งาน

#### 1. สร้างไฟล์ `staticData/init.sql`
ในโฟลเดอร์ `staticData/` (ตามที่ระบุใน volume):
```
project/
├── staticData/
│   └── init.sql
```

เนื้อหาของ `init.sql`:
```sql
-- สร้างตาราง users
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

-- เพิ่มข้อมูลตัวอย่าง
INSERT INTO users (name) VALUES
    ('John Doe'),
    ('Jane Smith')
ON CONFLICT DO NOTHING;
```

#### 2. ปรับ `docker-compose.yml` เพื่อโหลด `init.sql`
PostgreSQL จะโหลดไฟล์ `.sql` จากโฟลเดอร์ที่ mount ไปที่ `/docker-entrypoint-initdb.d/` อัตโนมัติเมื่อสร้าง container ครั้งแรก ดังนั้นให้เปลี่ยน volume mapping:
```yaml
version: '3.8'
services:
  backend:
    build:
      context: ./backend
      dockerfile: Dockerfile
    container_name: slim_app
    volumes:
      - ./backend:/var/www
    networks:
      - slim_network
    depends_on:
      - db

  nginx:
    image: nginx:alpine
    container_name: slim_nginx
    ports:
      - "8080:80"
    volumes:
      - ./backend:/var/www
      - ./nginx/conf.d:/etc/nginx/conf.d
    depends_on:
      - backend
    networks:
      - slim_network

  db:
    image: postgres:16
    container_name: postgres_db
    volumes:
      - ./db-data:/var/lib/postgresql/data
      - ./staticData:/docker-entrypoint-initdb.d  # เปลี่ยนจาก /staticData เป็น /docker-entrypoint-initdb.d
    environment:
      POSTGRES_DB: myapp
      POSTGRES_USER: spa
      POSTGRES_PASSWORD: spa
    networks:
      - slim_network
    ports:
      - "5432:5432"

networks:
  slim_network:
    driver: bridge
```

- **หมายเหตุ**: ถ้า volume `./db-data/` มีข้อมูลอยู่แล้ว (จากครั้งก่อน) PostgreSQL จะไม่โหลด `init.sql` อีก ดังนั้นถ้าต้องการรีเซ็ต:
  ```bash
  docker-compose down -v  # ลบ volumes ด้วย
  ```

#### 3. รันโปรเจกต์
```bash
docker-compose down -v  # ลบ volume เพื่อเริ่มใหม่
docker-compose up -d --build
```

---

### ตรวจสอบว่าทำงานหรือไม่
1. **ดู logs ของ database**:
   ```bash
   docker-compose logs db
   ```
   - ควรเห็นข้อความเกี่ยวกับการสร้างตารางและเพิ่มข้อมูล

2. **ทดสอบ endpoint**:
   - `GET http://localhost:8080/users` -> ควรได้:
     ```json
     [
         {"id": 1, "name": "John Doe"},
         {"id": 2, "name": "Jane Smith"}
     ]
     ```
   - `GET http://localhost:8080/users/1` -> ควรได้:
     ```json
     {"id": 1, "name": "John Doe"}
     ```

3. **ตรวจสอบใน PostgreSQL** (ถ้าต้องการ):
   ```bash
   docker exec -it postgres_db psql -U spa -d myapp
   ```
   - รัน: `SELECT * FROM users;` -> ควรเห็นข้อมูล

---

### ถ้าต้องการปรับแต่งเพิ่ม
1. **เพิ่มคอลัมน์ในตาราง**:
   - เช่น ถ้าต้องการ `email` หรือ `created_at`:
     ```sql
     CREATE TABLE IF NOT EXISTS users (
         id SERIAL PRIMARY KEY,
         name VARCHAR(255) NOT NULL,
         email VARCHAR(255) UNIQUE,
         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
     );

     INSERT INTO users (name, email) VALUES
         ('John Doe', 'john@example.com'),
         ('Jane Smith', 'jane@example.com')
     ON CONFLICT DO NOTHING;
     ```
   - แล้วปรับ method ใน `User.php` ให้รองรับคอลัมน์ใหม่ เช่น:
     ```php
     public static function create($data)
     {
         $pdo = self::getConnection();
         $stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (:name, :email) RETURNING id");
         $stmt->execute([
             'name' => $data['name'],
             'email' => $data['email']
         ]);
         return $stmt->fetchColumn();
     }
     ```

2. **ใช้ `.env`**:
   - ถ้าต้องการให้ config ยืดหยุ่นขึ้น บอกมาได้เลย ผมจะช่วยเพิ่ม phpdotenv

---

### ผลลัพธ์
- `User` model จะเชื่อมต่อกับ PostgreSQL (`myapp` database) จริง
- ตาราง `users` จะถูกสร้างพร้อมข้อมูลเริ่มต้น
- CRUD operations จะทำงานกับฐานข้อมูลแทน mock data

ถ้ามีปัญหาหรือต้องการปรับอะไรเพิ่ม แจ้งมาได้เลยครับ!