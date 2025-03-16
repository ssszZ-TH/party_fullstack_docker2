<?php
namespace App\Models;

use PDO;
use PDOException;

class Country
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
        $stmt = $pdo->query("SELECT * FROM public.country order by id asc;");
        return $stmt->fetchAll();
    }

    // ดึงข้อมูลตาม ID
    public static function find($id)
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM public.country WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    // สร้างข้อมูลใหม่
    public static function create($data)
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare("INSERT INTO public.country (isocode, name_en, name_th) VALUES (:isocode, :name_en, :name_th) RETURNING id");
        $stmt->execute([
            'isocode' => $data['isocode'],
            'name_en' => $data['name_en'],
            'name_th' => $data['name_th']
        ]);
        return $stmt->fetchColumn(); // คืนค่า ID ที่เพิ่งสร้าง
    }

    // อัปเดตข้อมูล
    public static function update($id, $data)
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare("UPDATE public.country SET isocode = :isocode, name_en = :name_en, name_th = :name_th WHERE id = :id");
        $stmt->execute([
            'id' => $id,
            'isocode' => $data['isocode'],
            'name_en' => $data['name_en'],
            'name_th' => $data['name_th']
        ]);
        return $stmt->rowCount(); // คืนค่าจำนวนแถวที่อัปเดต
    }

    // ลบข้อมูล
    public static function delete($id)
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare("DELETE FROM public.country WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount(); // คืนค่าจำนวนแถวที่ลบ
    }
}