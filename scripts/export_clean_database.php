<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$outputFile = __DIR__ . '/../beta/database.sql';

$header = <<<SQL
-- Clean Production Database Dump for B Cafe (بی کافه)
-- Generated: 2026-10-11
-- Server version: MySQL 8.0+ / MariaDB 10.4+ compatible
-- All demo products, categories, subscriptions, coupons, orders and test customers removed.

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

-- Dump completed. Clean raw database ready for production cPanel import.
SQL;

// Tables that should retain default data
$tablesWithData = [
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
    'migrations'
];

$allTables = DB::select('SHOW TABLES');
$dbName = config('database.connections.mysql.database');
$propName = 'Tables_in_' . $dbName;

$sql = $header . "\n";

foreach ($allTables as $tableObj) {
    $tableName = $tableObj->$propName;

    // Table structure
    $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
    $createSql = $createTable[0]->{'Create Table'};

    $sql .= "--\n-- Table structure for table `{$tableName}`\n--\n\n";
    $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
    $sql .= "/*!40101 SET @saved_cs_client     = @@character_set_client */;\n";
    $sql .= "/*!50503 SET character_set_client = utf8mb4 */;\n";
    $sql .= $createSql . ";\n";
    $sql .= "/*!40101 SET character_set_client = @saved_cs_client */;\n\n";

    // Table data
    if (in_array($tableName, $tablesWithData)) {
        $rows = DB::table($tableName)->get();
        if ($rows->count() > 0) {
            $sql .= "--\n-- Dumping data for table `{$tableName}`\n--\n\n";
            $sql .= "LOCK TABLES `{$tableName}` WRITE;\n";
            $sql .= "/*!40000 ALTER TABLE `{$tableName}` DISABLE KEYS */;\n";

            $columns = array_keys((array)$rows[0]);
            $colList = implode('`, `', $columns);

            $valuesList = [];
            foreach ($rows as $row) {
                $vals = [];
                foreach ((array)$row as $val) {
                    if (is_null($val)) {
                        $vals[] = 'NULL';
                    } elseif (is_numeric($val) && !is_string($val)) {
                        $vals[] = $val;
                    } else {
                        $escaped = addslashes((string)$val);
                        $escaped = str_replace(["\r", "\n"], ["\\r", "\\n"], $escaped);
                        $vals[] = "'" . $escaped . "'";
                    }
                }
                $valuesList[] = '(' . implode(', ', $vals) . ')';
            }

            $sql .= "INSERT INTO `{$tableName}` (`{$colList}`) VALUES \n" . implode(",\n", $valuesList) . ";\n";
            $sql .= "/*!40000 ALTER TABLE `{$tableName}` ENABLE KEYS */;\n";
            $sql .= "UNLOCK TABLES;\n\n";
        }
    }
}

$sql .= $footer;

file_put_contents($outputFile, $sql);
echo "SUCCESS: Clean database dumped to {$outputFile} (" . strlen($sql) . " bytes)\n";
