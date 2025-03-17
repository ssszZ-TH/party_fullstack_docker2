<?php
namespace App\Models;

use PDO;
use PDOException;

class Passport
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
        $stmt = $pdo->query("SELECT * FROM public.passport order by id asc;");
        return $stmt->fetchAll();
    }

    // ดึงข้อมูลตาม ID
    public static function find($id)
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM public.passport WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    // สร้างข้อมูลใหม่
    public static function create($data)
    {
        $pdo = self::getConnection();
        $sql = "INSERT INTO public.passport(passportnumber, fromdate, thrudate, citizenship_id) 
        VALUES (:passportnumber, :fromdate, :thrudate, :citizenship_id) RETURNING id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'passportnumber' => $data['passportnumber'],
            'fromdate' => $data['fromdate'],
            'thrudate' => $data['thrudate'],
            'citizenship_id' => $data['citizenship_id']
        ]);
        return $stmt->fetchColumn(); // คืนค่า ID ที่เพิ่งสร้าง
    }

    // อัปเดตข้อมูล
    public static function update($id, $data)
    {
        $pdo = self::getConnection();
        $sql = "UPDATE public.passport 
            SET passportnumber = :passportnumber,
            fromdate = :fromdate,
            thrudate = :thrudate, 
            citizenship_id = :citizenship_id 
            WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'passportnumber' => $data['passportnumber'],
            'fromdate' => $data['fromdate'],
            'thrudate' => $data['thrudate'],
            'citizenship_id' => $data['citizenship_id']
        ]);
        return $stmt->rowCount(); // คืนค่าจำนวนแถวที่อัปเดต
    }

    // ลบข้อมูล
    public static function delete($id)
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare("DELETE FROM public.passport WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount(); // คืนค่าจำนวนแถวที่ลบ
    }
}