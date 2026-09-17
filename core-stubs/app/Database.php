<?php
/**
 * ArtiFrame Core Engine
 *
 * @package     ArtiFrame
 * @author      Artilingo
 * @license     AGPLv3 (Attribution-ShareAlike Required)
 */

namespace App;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    private function __construct() {}

    private function __clone() {}

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
            $name = $_ENV['DB_NAME'] ?? 'artiframe';
            $user = $_ENV['DB_USER'] ?? 'root';
            $pass = $_ENV['DB_PASSWORD'] ?? '';
            $port = $_ENV['DB_PORT'] ?? 3306;
            
            // Eğer veritabanı adı veya kullanıcı boşsa, veritabanı henüz kurulmamış olabilir.
            if (empty($name) || empty($user)) {
                die("Veritabanı bağlantı bilgileri eksik. Lütfen .env dosyasını kontrol edin.");
            }

            try {
                $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
                self::$instance = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                die("Veritabanı bağlantı hatası: " . $e->getMessage());
            }
        }

        return self::$instance;
    }

    /**
     * Long-running worker'lar için veritabanı bağlantısının 
     * düşüp düşmediğini kontrol eder, düşmüşse yeniden bağlanır.
     */
    public static function ping(): void
    {
        if (self::$instance !== null) {
            try {
                self::$instance->query('SELECT 1');
            } catch (PDOException $e) {
                $msg = strtolower($e->getMessage());
                if (strpos($msg, 'gone away') !== false || strpos($msg, 'lost connection') !== false) {
                    self::$instance = null;
                    self::getInstance();
                }
            }
        }
    }
}
