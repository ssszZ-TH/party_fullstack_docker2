<?php
namespace App\Models;

use PDO;
use PDOException;

class Physicalcharacteristic
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
        $stmt = $pdo->query("SELECT * FROM public.physicalcharacteristic order by id asc;");
        return $stmt->fetchAll();
    }

    // ดึงข้อมูลตาม ID
    public static function find($id)
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM public.physicalcharacteristic WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    // สร้างข้อมูลใหม่
    public static function create($data)
    {
        $pdo = self::getConnection();

        // เขียน SQL ให้ชัดเจน ไม่มี whitespace เกิน
        $sql = "INSERT INTO public.physicalcharacteristic (fromdate, thrudate, person_id, physicalcharacteristictype_id, val) 
        VALUES (:fromdate, :thrudate, :person_id, :physicalcharacteristictype_id, :val) 
        RETURNING id";
        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            'fromdate' => $data['fromdate'],
            'thrudate' => $data['thrudate'],
            'person_id' => $data['person_id'],
            'physicalcharacteristictype_id' => $data['physicalcharacteristictype_id'],
            'val' => $data['val']
        ]);

        return $stmt->fetchColumn(); // คืนค่า ID
    }

    // อัปเดตข้อมูล
    public static function update($id, $data)
    {
        $pdo = self::getConnection();
        $sql = "UPDATE public.physicalcharacteristic 
        SET fromdate = :fromdate, 
        thrudate = :thrudate, 
        person_id = :person_id, 
        physicalcharacteristictype_id = :physicalcharacteristictype_id, 
        val = :val 
        WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'fromdate' => $data['fromdate'],
            'thrudate' => $data['thrudate'],
            'person_id' => $data['person_id'],
            'physicalcharacteristictype_id' => $data['physicalcharacteristictype_id'],
            'val' => $data['val']
        ]);
        return $stmt->rowCount(); // คืนค่าจำนวนแถวที่อัปเดต
    }

    // ลบข้อมูล
    public static function delete($id)
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare("DELETE FROM public.physicalcharacteristic WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount(); // คืนค่าจำนวนแถวที่ลบ
    }
}