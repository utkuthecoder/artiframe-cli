<?php
/**
 * ArtiFrame Core Engine
 *
 * @package     ArtiFrame
 * @author      Artilingo
 * @license     AGPLv3 (Attribution-ShareAlike Required)
 * @link        https://artiframe.artilingo.com
 *
 * NOTICE: This file is part of the ArtiFrame ecosystem.
 * Any derivative works or patches MUST retain this original copyright notice
 * and remain open-source under the AGPLv3 license.
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

            // .env henüz yüklenmediyse tam ve temiz yoldan otomatik yükle
            if (empty($_ENV['DB_HOST'])) {
                // src/ katmanından bir üst klasöre (htdocs köküne) direkt geçiş:
                $envPath = dirname(__DIR__) . '/.env';
                DotEnv::load($envPath);
            }

            $host   = $_ENV['DB_HOST'] ?? 'localhost';
            $dbname = $_ENV['DB_NAME'] ?? 'portfobase';
            $user   = $_ENV['DB_USER'] ?? 'root';
            $pass   = $_ENV['DB_PASS'] ?? '';

            $dsn = sprintf("mysql:host=%s;dbname=%s;charset=utf8mb4", $host, $dbname);

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                $isDev = (defined('APP_ENV') && APP_ENV == 1);

                $msg = !$isDev
                    ? 'Veritabanı bağlantı hatası. Sistem yöneticisi ile görüşün.'
                    : 'Veritabanı Hatası (Dev Mod): ' . $e->getMessage();

                header('Content-Type: application/json; charset=utf-8');
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
        }

        return self::$instance;
    }
}
