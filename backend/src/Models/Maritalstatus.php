<?php
namespace App\Models;

use PDO;
use PDOException;

class Maritalstatus
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
        $stmt = $pdo->query("SELECT * FROM public.maritalstatus order by id asc;");
        return $stmt->fetchAll();
    }

    // ดึงข้อมูลตาม ID
    public static function find($id)
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM public.maritalstatus WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    // สร้างข้อมูลใหม่
    public static function create($data)
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare("INSERT INTO public.maritalstatus(fromdate, thrudate, person_id, maritalstatustype_id) VALUES (:fromdate, :thrudate, :person_id, :maritalstatustype_id) RETURNING id");
        $stmt->execute([
            'fromdate' => $data['fromdate'],
            'thrudate' => $data['thrudate'],
            'person_id' => $data['person_id'],
            'maritalstatustype_id' => $data['maritalstatustype_id']
        ]);
        return $stmt->fetchColumn(); // คืนค่า ID ที่เพิ่งสร้าง
    }

    // อัปเดตข้อมูล
    public static function update($id, $data)
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare("UPDATE public.maritalstatus SET fromdate = :fromdate, thrudate = :thrudate, person_id = :person_id, maritalstatustype_id = :maritalstatustype_id WHERE id = :id");
        $stmt->execute([
            'id' => $id,
            'fromdate' => $data['fromdate'],
            'thrudate' => $data['thrudate'],
            'person_id' => $data['person_id'],
            'maritalstatustype_id' => $data['maritalstatustype_id']
        ]);
        return $stmt->rowCount(); // คืนค่าจำนวนแถวที่อัปเดต
    }

    // ลบข้อมูล
    public static function delete($id)
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare("DELETE FROM public.maritalstatus WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount(); // คืนค่าจำนวนแถวที่ลบ
    }
}