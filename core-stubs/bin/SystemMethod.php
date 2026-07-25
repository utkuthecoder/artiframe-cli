<?php
/**
 * ArtiFrame Core Engine
 *
 * @package     ArtiFrame
 * @author      Artilingo
 * @license     AGPLv3 (Attribution-ShareAlike Required)
 * @link        https://artiframe.org
 *
 * NOTICE: This file is part of the ArtiFrame ecosystem.
 * Any derivative works or patches MUST retain this original copyright notice
 * and remain open-source under the AGPLv3 license.
 */

namespace Bin;

class SystemMethod
{
    /**
     * E-posta adresinin format olarak geçerli olup olmadığını doğrular.
     * 
     * @param string $email Doğrulanacak E-Posta
     * @return bool
     */
    public static function verifyEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Gelen veriyi güvenli bir Integer (tam sayı) değerine dönüştürür.
     * Harfleri siler ve sayısal karakter bırakır.
     * 
     * @param mixed $value
     * @return int
     */
    public static function sanitizeInt($value): int
    {
        return (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Gelen verideki HTML etiketlerini ve zararlı kodları temizler.
     * Veritabanına String kaydetmeden önce mutlaka kullanılmalıdır.
     * 
     * @param string $value
     * @return string
     */
    public static function sanitizeString(string $value): string
    {
        return strip_tags(trim($value));
    }

    /**
     * Sayfaya gelen isteğin bir form gönderimi (POST) olup olmadığını kontrol eder.
     * 
     * @return bool
     */
    public static function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Oturum (Session) tabanlı CSRF (Cross-Site Request Forgery) güvenlik token'ı üretir.
     */
    public static function generateCsrf(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Formdan gelen CSRF token'ın doğruluğunu onaylar. (Form güvenlik kalkanı)
     */
    public static function verifyCsrf(?string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * İsteğin AJAX üzerinden gelip gelmediğini kontrol eder. (Fetch / Axios destekli)
     */
    public static function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * API'ler için hızlı ve standart JSON yanıt (response) oluşturur.
     * Kullanıldığı anda işlemi sonlandırır (exit).
     * 
     * @param array $data JSON'a çevrilecek dizi (array)
     * @param int $statusCode HTTP Durum Kodu (200 OK, 404 Not Found vb.)
     */
    public static function jsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
    /**
     * Sayfaya gelen isteğin GET olup olmadığını kontrol eder.
     */
    public static function isGet(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    /**
     * Sayfaya gelen isteğin PUT olup olmadığını kontrol eder.
     */
    public static function isPut(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'PUT';
    }

    /**
     * Sayfaya gelen isteğin DELETE olup olmadığını kontrol eder.
     */
    public static function isDelete(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'DELETE';
    }

    /**
     * Kullanıcının gerçek IP adresini tespit eder (Cloudflare ve Proxy destekli).
     */
    public static function getClientIp(): string
    {
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return $_SERVER['HTTP_CF_CONNECTING_IP'];
        }
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ipList[0]);
        }
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * İstenilen sayfaya yönlendirme yapar ve çalışmayı durdurur.
     */
    public static function redirect(string $url): void
    {
        header("Location: " . $url);
        exit;
    }

    /**
     * Kriptografik olarak güvenli, rastgele bir token üretir (Örn: Parola sıfırlama, API Key).
     */
    public static function generateToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * E-posta adresini zararlı karakterlerden temizler.
     */
    public static function sanitizeEmail(string $email): string
    {
        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }
}

// Global helper functions for backend methods
if (!function_exists('verifyEmail')) {
    function verifyEmail(string $email): bool {
        return \Bin\SystemMethod::verifyEmail($email);
    }
}

if (!function_exists('sanitizeInt')) {
    function sanitizeInt($value): int {
        return \Bin\SystemMethod::sanitizeInt($value);
    }
}

if (!function_exists('sanitizeString')) {
    function sanitizeString(string $value): string {
        return \Bin\SystemMethod::sanitizeString($value);
    }
}

if (!function_exists('isPost')) {
    function isPost(): bool {
        return \Bin\SystemMethod::isPost();
    }
}

if (!function_exists('jsonResponse')) {
    function jsonResponse(array $data, int $statusCode = 200): void {
        \Bin\SystemMethod::jsonResponse($data, $statusCode);
    }
}

if (!function_exists('generateCsrf')) {
    function generateCsrf(): string {
        return \Bin\SystemMethod::generateCsrf();
    }
}

if (!function_exists('verifyCsrf')) {
    function verifyCsrf(?string $token): bool {
        return \Bin\SystemMethod::verifyCsrf($token);
    }
}

if (!function_exists('isAjax')) {
    function isAjax(): bool {
        return \Bin\SystemMethod::isAjax();
    }
}

if (!function_exists('isGet')) {
    function isGet(): bool {
        return \Bin\SystemMethod::isGet();
    }
}

if (!function_exists('isPut')) {
    function isPut(): bool {
        return \Bin\SystemMethod::isPut();
    }
}

if (!function_exists('isDelete')) {
    function isDelete(): bool {
        return \Bin\SystemMethod::isDelete();
    }
}

if (!function_exists('getClientIp')) {
    function getClientIp(): string {
        return \Bin\SystemMethod::getClientIp();
    }
}

if (!function_exists('redirect')) {
    function redirect(string $url): void {
        \Bin\SystemMethod::redirect($url);
    }
}

if (!function_exists('generateToken')) {
    function generateToken(int $length = 32): string {
        return \Bin\SystemMethod::generateToken($length);
    }
}

if (!function_exists('sanitizeEmail')) {
    function sanitizeEmail(string $email): string {
        return \Bin\SystemMethod::sanitizeEmail($email);
    }
}
