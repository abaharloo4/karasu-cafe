<?php
/**
 * Merge local DB + h411118_caf.sql data and export for bcafeshop.ir
 * Tables from h411118: customers, subscription_plans, subscription_plan_products, subscriptions, subscription_usages
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$hSql = file_get_contents('C:/Users/Amir/Downloads/h411118_caf.sql');
$outputFile = __DIR__ . '/../beta/database.sql';

echo "=== Generating merged database.sql ===\n";

$header = <<<SQL
-- Merged Production Database for B Cafe (بی کافه)
-- Domain: bcafeshop.ir
-- Generated: 2026-10-22
-- Server version: MySQL 8.0+ / MariaDB 10.4+ compatible

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

SQL;

$footer = <<<SQL

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump complete.
SQL;

// Tables to retain data from LOCAL DB
$localDataTables = [
    'cafes',
    'users',
    'customer_tiers',
    'sms_settings',
    'loyalty_settings',
    'receipt_settings',
    'pwa_settings',
    'fulfillment_settings',
    'payment_gateways',
    'site_content_settings',
    'categories',
    'products',
    'delivery_zones',
    'migrations',
];

// Tables to get data from h411118 SQL file
$hTables = [
    'customers',
    'subscription_plans',
    'subscription_plan_products',
    'subscriptions',
    'subscription_usages',
];

function escapeValue($val) {
    if (is_null($val)) return 'NULL';
    if (is_numeric($val) && !is_string($val)) return $val;
    $escaped = addslashes((string)$val);
    $escaped = str_replace(["\r", "\n"], ["\\r", "\\n"], $escaped);
    return "'" . $escaped . "'";
}

function tableToSql($tableName, $rows) {
    if (empty($rows)) return '';
    $columns = array_keys((array)$rows[0]);
    $colList = implode('`, `', $columns);
    $valuesList = [];
    foreach ($rows as $row) {
        $vals = array_map('escapeValue', array_values((array)$row));
        $valuesList[] = '(' . implode(', ', $vals) . ')';
    }
    $sql = "LOCK TABLES `{$tableName}` WRITE;\n";
    $sql .= "/*!40000 ALTER TABLE `{$tableName}` DISABLE KEYS */;\n";
    $sql .= "INSERT INTO `{$tableName}` (`{$colList}`) VALUES\n" . implode(",\n", $valuesList) . ";\n";
    $sql .= "/*!40000 ALTER TABLE `{$tableName}` ENABLE KEYS */;\n";
    $sql .= "UNLOCK TABLES;\n\n";
    return $sql;
}

// Grab schema from h411118 for these tables (uses newer local schema instead)
function extractH411118Insert($hSql, $table) {
    if (!preg_match('/INSERT INTO `' . $table . '`\s*\(([^)]+)\)\s*VALUES\s*(.+?);/s', $hSql, $matches)) {
        return null;
    }
    return $matches[0] . ";\n";
}

$allTables = DB::select('SHOW TABLES');
$dbName = config('database.connections.mysql.database');
$propName = 'Tables_in_' . $dbName;

$sql = $header;

foreach ($allTables as $tableObj) {
    $tableName = $tableObj->$propName;

    // Get local CREATE TABLE
    $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
    $createSql = $createTable[0]->{'Create Table'};

    $sql .= "--\n-- Table: `{$tableName}`\n--\n\n";
    $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
    $sql .= "/*!40101 SET @saved_cs_client     = @@character_set_client */;\n";
    $sql .= "/*!50503 SET character_set_client = utf8mb4 */;\n";
    $sql .= $createSql . ";\n";
    $sql .= "/*!40101 SET character_set_client = @saved_cs_client */;\n\n";

    if (in_array($tableName, $localDataTables)) {
        // Use LOCAL data
        $rows = DB::table($tableName)->get();
        if ($rows->count() > 0) {
            $sql .= "--\n-- Data for `{$tableName}` (from local DB)\n--\n\n";
            $sql .= tableToSql($tableName, $rows->toArray());
        }
        echo "  [$tableName] local: {$rows->count()} rows\n";
    } elseif (in_array($tableName, $hTables)) {
        // Use h411118 data - but with schema-safe INSERT (adjust columns)
        // Extract INSERT from h411118 and convert to use local schema columns
        if ($tableName === 'customers') {
            // h411118 customers lacks: birthdate, tier_id, total_spent, wallet_balance
            // We need to build inserts manually
            if (preg_match('/INSERT INTO `customers`\s*\(([^)]+)\)\s*VALUES\s*(.+?);/s', $hSql, $hMatch)) {
                $hCols = array_map(function($c) { return trim(str_replace('`', '', $c)); }, explode(',', $hMatch[1]));
                // Parse value tuples
                $valuesStr = trim($hMatch[2]);
                preg_match_all('/\(([^)]+)\)/', $valuesStr, $tuples);
                
                $rows = [];
                foreach ($tuples[1] as $tpl) {
                    $parts = str_getcsv($tpl, ',', "'");
                    $row = [];
                    foreach ($hCols as $i => $col) {
                        $row[$col] = trim($parts[$i] ?? 'NULL', " \t\n\r\0\x0B");
                        if ($row[$col] === 'NULL') $row[$col] = null;
                        else $row[$col] = trim($row[$col], "'");
                    }
                    // Add missing columns with defaults
                    $row['birthdate'] = null;
                    $row['tier_id'] = null;
                    $row['total_spent'] = '0.00';
                    $row['wallet_balance'] = '0.00';
                    $rows[] = $row;
                }
                
                // Build INSERT with all local columns
                $localCols = ['id','cafe_id','name','phone','email','birthdate','tier_id','total_spent','wallet_balance','address','created_at','updated_at','remember_token'];
                $sql .= "--\n-- Data for `customers` (from h411118_caf.sql, 20 real customers)\n--\n\n";
                $sql .= "LOCK TABLES `customers` WRITE;\n";
                $sql .= "/*!40000 ALTER TABLE `customers` DISABLE KEYS */;\n";
                $valuesList = [];
                foreach ($rows as $r) {
                    $vals = [];
                    foreach ($localCols as $col) {
                        $vals[] = escapeValue($r[$col] ?? null);
                    }
                    $valuesList[] = '(' . implode(', ', $vals) . ')';
                }
                $sql .= "INSERT INTO `customers` (`" . implode("`, `", $localCols) . "`) VALUES\n" . implode(",\n", $valuesList) . ";\n";
                $sql .= "/*!40000 ALTER TABLE `customers` ENABLE KEYS */;\n";
                $sql .= "UNLOCK TABLES;\n\n";
                echo "  [customers] h411118: " . count($rows) . " rows\n";
            }
        } else {
            // Direct INSERT reuse from h411118 (schema matches)
            if (preg_match('/INSERT INTO `' . $tableName . '`\s*\([^)]+\)\s*VALUES\s*.+?;/s', $hSql, $hMatch)) {
                $sql .= "--\n-- Data for `{$tableName}` (from h411118_caf.sql)\n--\n\n";
                $sql .= "LOCK TABLES `{$tableName}` WRITE;\n";
                $sql .= "/*!40000 ALTER TABLE `{$tableName}` DISABLE KEYS */;\n";
                $sql .= $hMatch[0] . "\n";
                $sql .= "/*!40000 ALTER TABLE `{$tableName}` ENABLE KEYS */;\n";
                $sql .= "UNLOCK TABLES;\n\n";
                preg_match_all('/\)\s*,\s*\(/', $hMatch[0], $cnt);
                echo "  [$tableName] h411118: " . (count($cnt[0])+1) . " rows\n";
            } else {
                echo "  [$tableName] h411118: no data\n";
            }
        }
    } else {
        echo "  [$tableName] skipped (no seed data)\n";
    }
}

$sql .= $footer;

file_put_contents($outputFile, $sql);
$size = round(filesize($outputFile) / 1024, 1);
echo "\n=== SUCCESS: database.sql written ({$size} KB) ===\n";
