<?php
namespace App\Models;

use PDO;
use PDOException;

class Informal_organization
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
        $sql = "SELECT 
            p.id AS party_id,
            o.name_en,
            o.name_th
            FROM public.informal_organization io
            JOIN public.organization o ON io.id = o.id
            JOIN public.party p ON o.id = p.id";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    }

    // ดึงข้อมูลตาม ID
    public static function find($id)
    {
        $pdo = self::getConnection();
        $sql = "SELECT 
            p.id AS party_id,
            o.name_en,
            o.name_th
            FROM public.informal_organization io
            JOIN public.organization o ON io.id = o.id
            JOIN public.party p ON o.id = p.id
            WHERE io.id = :id;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    // สร้างข้อมูลใหม่
    public static function create($data)
    {
        $pdo = self::getConnection();

        try {
            $pdo->beginTransaction();
            
            $sql1 = "INSERT INTO public.party (id) 
                VALUES (DEFAULT) 
                RETURNING id;";
            $stmt1 = $pdo->prepare($sql1);
            $stmt1->execute();
            $party_id = $stmt1->fetchColumn();

            $sql2 = "INSERT INTO public.organization (id, name_en, name_th)
                VALUES (
                    :party_id,           -- id จาก party
                    :name_en,            -- ชื่อภาษาอังกฤษ
                    :name_th             -- ชื่อภาษาไทย
                ) 
                RETURNING id;";
            $stmt2 = $pdo->prepare($sql2);
            $stmt2->execute([
                'party_id' => $party_id,
                'name_en' => $data['name_en'],
                'name_th' => $data['name_th']
            ]);
            $organization_id = $stmt2->fetchColumn();

            $sql3 = "INSERT INTO public.informal_organization (id)
                        VALUES (
                            :organization_id     -- id จาก organization
                        )
                        RETURNING id;";

            $stmt3 = $pdo->prepare($sql3);
            $stmt3->execute([
                'organization_id' => $organization_id
            ]);
            $informal_organization_id = $stmt3->fetchColumn();

            $pdo->commit(); // ทำผ่านทั้งหมด ไม่ต้อง roll back
            return $informal_organization_id; // คืนค่า id
        } catch (PDOException $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    // อัปเดตข้อมูล
    public static function update($id, $data)
    {
        $pdo = self::getConnection();
        // อัปเดตข้อมูล informal_organization (เฉพาะ organization เพราะ informal_organization มีแค่ id)
        try {
            $pdo->beginTransaction();
            $sql = "UPDATE public.organization o
                SET 
                name_en = :name_en,
                name_th = :name_th
                FROM public.informal_organization io
                WHERE o.id = io.id AND io.id = :id;";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'id' => $id,
                'name_en' => $data['name_en'],
                'name_th' => $data['name_th']
            ]);
            $pdo->commit();
            return $stmt->rowCount();
        }
        catch (PDOException $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    // ลบข้อมูล
    public static function delete($id)
    {
        $pdo = self::getConnection();
        $sql1 = "DELETE FROM public.informal_organization WHERE id = :id;";
        $sql2 = "DELETE FROM public.organization WHERE id = :id;";
        $sql3 = "DELETE FROM public.party WHERE id = :id;";

        try {
            $pdo->beginTransaction();
            $stmt1 = $pdo->prepare($sql1);
            $stmt1->execute(['id' => $id]);
            $stmt2 = $pdo->prepare($sql2);
            $stmt2->execute(['id' => $id]);
            $stmt3 = $pdo->prepare($sql3);
            $stmt3->execute(['id' => $id]);
            $pdo->commit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            throw $e;
        }
        return $stmt1->rowCount();
    }
}