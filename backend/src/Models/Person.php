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

        try {
            // เริ่ม transaction เพื่อให้แน่ใจว่า insert ทั้งสองสำเร็จพร้อมกัน
            $pdo->beginTransaction();

            // 1. Insert into party และดึง id
            $sql1 = "
                INSERT INTO public.party (id) 
                VALUES (DEFAULT) 
                RETURNING id
            ";
            $stmt1 = $pdo->prepare($sql1);
            $stmt1->execute();
            $new_id = $stmt1->fetchColumn(); // ดึง id ที่สร้าง

            // 2. Insert into person ด้วย id ที่ได้
            $sql2 = "
                INSERT INTO public.person (
                    id, 
                    socialsecuritynumber, 
                    birthdate, 
                    mothermaidenname, 
                    totalyearworkexperience, 
                    comment
                ) VALUES (
                    :id,
                    :socialsecuritynumber,
                    :birthdate,
                    :mothermaidenname,
                    :totalyearworkexperience,
                    :comment
                )
            ";
            $stmt2 = $pdo->prepare($sql2);
            $stmt2->execute([
                'id' => $new_id,
                'socialsecuritynumber' => $data['socialsecuritynumber'],
                'birthdate' => $data['birthdate'],
                'mothermaidenname' => $data['mothermaidenname'],
                'totalyearworkexperience' => $data['totalyearworkexperience'],
                'comment' => $data['comment']
            ]);

            // Commit transaction
            $pdo->commit();

            return $new_id; // คืนค่า id ที่สร้าง
        } catch (PDOException $e) {
            // Rollback ถ้ามี error
            $pdo->rollBack();
            throw $e; // โยน error ต่อไปให้ controller จัดการ
        }
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