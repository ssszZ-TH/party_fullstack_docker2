<?php
namespace App\Models;

use PDO;
use PDOException;

class Legalorganization
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
            o.name_th,
            lo.federal_tax_id_number
            FROM public.legal_organization lo
            JOIN public.organization o ON lo.id = o.id
            JOIN public.party p ON o.id = p.id
            order by party_id asc;";
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
            o.name_th,
            lo.federal_tax_id_number
            FROM public.legal_organization lo
            JOIN public.organization o ON lo.id = o.id
            JOIN public.party p ON o.id = p.id
            WHERE lo.id = :id;";
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

            // 1. Insert into party
            $stmt1 = $pdo->prepare("INSERT INTO public.party (id) VALUES (DEFAULT) RETURNING id");
            $stmt1->execute();
            $party_id = $stmt1->fetchColumn();

            // 2. Insert into organization
            $stmt2 = $pdo->prepare("
            INSERT INTO public.organization (id, name_en, name_th)
            VALUES (:id, :name_en, :name_th)
            RETURNING id
        ");
            $stmt2->execute([
                'id' => $party_id,
                'name_en' => $data['name_en'],
                'name_th' => $data['name_th']
            ]);
            $org_id = $stmt2->fetchColumn();

            // 3. Insert into legal_organization
            $stmt3 = $pdo->prepare("
            INSERT INTO public.legal_organization (id, federal_tax_id_number)
            VALUES (:id, :federal_tax_id_number)
        ");
            $stmt3->execute([
                'id' => $org_id,
                'federal_tax_id_number' => $data['federal_tax_id_number']
            ]);

            $pdo->commit();
            return $party_id; // คืนค่า id
        } catch (PDOException $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    // อัปเดตข้อมูล
    public static function update($id, $data)
    {
        $pdo = self::getConnection();
        
        try {
            $pdo->beginTransaction();

            // 1. Update legal_organization
            $sql = "UPDATE public.organization o
                SET 
                name_en = :name_en,
                name_th = :name_th
                FROM public.legal_organization lo
                WHERE o.id = lo.id AND lo.id = :id;";
            $stmt1 = $pdo->prepare($sql);
            $stmt1->execute([
                'id' => $id,
                'name_en' => $data['name_en'],
                'name_th' => $data['name_th']
            ]);
            $sql2 = "UPDATE public.legal_organization
                SET 
                federal_tax_id_number = :federal_tax_id_number
                WHERE id = :id;";
            $stmt2 = $pdo->prepare($sql2);
            $stmt2->execute([
                'id' => $id,
                'federal_tax_id_number' => $data['federal_tax_id_number']
            ]);
            $pdo->commit();
            return $stmt2->rowCount();
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
        $sql1 = "DELETE FROM public.legal_organization WHERE id = :id;";
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