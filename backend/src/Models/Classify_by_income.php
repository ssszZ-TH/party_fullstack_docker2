<?php
namespace App\Models;

use PDO;
use PDOException;

class Classify_by_income
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
        $sql = "SELECT party_cl.id, party_cl.fromdate, party_cl.thrudate, party_cl.party_id, party_cl.party_type_id, ci.income_range_id, ir.description
                FROM classify_by_income ci
                JOIN person_classification person_cl ON ci.id = person_cl.id
                JOIN party_classification party_cl ON ci.id = party_cl.id
                JOIN income_range ir ON ci.income_range_id = ir.id;";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    }

    // ดึงข้อมูลตาม ID
    public static function find($id)
    {
        $pdo = self::getConnection();
        $sql = "SELECT party_cl.id, party_cl.fromdate, party_cl.thrudate, party_cl.party_id, party_cl.party_type_id, ci.income_range_id, ir.description
                FROM classify_by_income ci
                JOIN person_classification person_cl ON ci.id = person_cl.id
                JOIN party_classification party_cl ON ci.id = party_cl.id
                JOIN income_range ir ON ci.income_range_id = ir.id
                WHERE ci.id = :id;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    // สร้างข้อมูลใหม่
    public static function create($data)
    {
        $pdo = self::getConnection();
        $sql1 = "INSERT INTO party_classification (fromdate, thrudate, party_id, party_type_id)
                VALUES (:fromdate, :thrudate, :party_id, :party_type_id)
                RETURNING id;";

        $sql2 = "INSERT INTO person_classification (id)
                VALUES (:id);";

        $sql3 = "INSERT INTO classify_by_income (id, income_range_id)
                VALUES (:id, :income_range_id);";
        try {
            // ทำ snap shot ของ db เตรียมไว้ ถ้า exec เเล้ว error ก็จะ roll back
            $pdo->beginTransaction();
            $stmt1 = $pdo->prepare($sql1);
            $stmt1->execute([
                'fromdate' => $data['fromdate'],
                'thrudate' => $data['thrudate'],
                'party_id' => $data['party_id'],
                'party_type_id' => $data['party_type_id']
            ]);
            $new_id = $stmt1->fetchColumn();
            $stmt2 = $pdo->prepare($sql2);
            $stmt2->execute([
                'id' => $new_id,
            ]);
            $stmt3 = $pdo->prepare($sql3);
            $stmt3->execute([
                'id' => $new_id,
                'income_range_id' => $data['income_range_id']
            ]);
            $pdo->commit(); // ทำผ่านทั้งหมด ไม่ต้อง roll back
            return $new_id; // คืนค่า id
        } catch (PDOException $e) {
            // ถ้ามีข้อผิดพลาดใดๆ ให้ roll back ยกเลิกการทำทั้งหมด
            $pdo->rollBack();
            throw $e;
        }
    }

    // อัปเดตข้อมูล
    public static function update($id, $data)
    {
        $pdo = self::getConnection();
        $sql1 = "UPDATE party_classification
                SET fromdate = :fromdate, thrudate = :thrudate, 
                party_id = :party_id, 
                party_type_id = :party_type_id
                WHERE id = :id;";

        $sql2 = "UPDATE classify_by_income
                SET income_range_id = :income_range_id
                WHERE id = :id;";
        try {
            // ทำ snap shot ของ db เตรียมไว้ ถ้า exec เเล้ว error ก็จะ roll back
            $pdo->beginTransaction();
            $stmt1 = $pdo->prepare($sql1);
            $stmt1->execute([
                'id' => $id,
                'fromdate' => $data['fromdate'],
                'thrudate' => $data['thrudate'],
                'party_id' => $data['party_id'],
                'party_type_id' => $data['party_type_id']
            ]);
            $stmt2 = $pdo->prepare($sql2);
            $stmt2->execute([
                'id' => $id,
                'income_range_id' => $data['income_range_id']
            ]);
            $pdo->commit();
            return $stmt2->rowCount();
        } catch (PDOException $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    // ลบข้อมูล
    public static function delete($id)
    {
        $pdo = self::getConnection();
        $sql1 = "DELETE FROM classify_by_income
                WHERE id = :id;";
        $sql2 = "DELETE FROM person_classification
                WHERE id = :id;";
        $sql3 = "DELETE FROM party_classification
                WHERE id = :id;";

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