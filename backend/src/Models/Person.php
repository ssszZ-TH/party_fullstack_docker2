<?php
namespace App\Models;

use PDO;
use PDOException;

class Person
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
        $stmt = $pdo->query("SELECT * FROM public.person order by id asc;");
        return $stmt->fetchAll();
    }

    // ดึงข้อมูลตาม ID
    public static function find($id)
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM public.person WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    // สร้างข้อมูลใหม่
    public static function create($data)
    {
        $pdo = self::getConnection();
        $sql = "DO $$
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
        :socialsecuritynumber,
        :birthdate,
        :mothermaidenname,
        :totalyearworkexperience,
        :comment
    );
END $$;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'socialsecuritynumber' => $data['socialsecuritynumber'],
            'birthdate' => $data['birthdate'],
            'mothermaidenname' => $data['mothermaidenname'],
            'totalyearworkexperience' => $data['totalyearworkexperience'],
            'comment' => $data['comment']
        ]);
        return $stmt->fetchColumn(); // คืนค่า ID ที่เพิ่งสร้าง
    }

    // อัปเดตข้อมูล
    public static function update($id, $data)
    {
        $pdo = self::getConnection();
        $sql = "UPDATE public.person 
SET 
    socialsecuritynumber = :socialsecuritynumber,
    birthdate = :birthdate,
    mothermaidenname = :mothermaidenname,
    totalyearworkexperience = :totalyearworkexperience,
    comment = :comment
WHERE id = :id;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'socialsecuritynumber' => $data['socialsecuritynumber'],
            'birthdate' => $data['birthdate'],
            'mothermaidenname' => $data['mothermaidenname'],
            'totalyearworkexperience' => $data['totalyearworkexperience'],
            'comment' => $data['comment']
        ]);
        return $stmt->rowCount(); // คืนค่าจำนวนแถวที่อัปเดต
    }

    // ลบข้อมูล
    public static function delete($id)
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare("DELETE FROM public.person WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount(); // คืนค่าจำนวนแถวที่ลบ
    }
}