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
     * Standart API yanıtı (JSON) oluşturur.
     * APP_ENV = 1 (Debug) ise Exception detaylarını mesaja yazar.
     * APP_ENV = 0 (Prod) ise kullanıcının girdiği temiz mesajı gösterir.
     */
    public static function apiResponse(string $status, string $message, array $data = [], \Throwable $exception = null): void
    {
        // Debug modundaysak ve bir exception geldiyse, mesajı ezip sistem hatasını basıyoruz
        if (defined('APP_ENV') && APP_ENV === 1 && $exception !== null) {
            $message = $exception->getMessage() . ' (Dosya: ' . $exception->getFile() . ' Satır: ' . $exception->getLine() . ')';
        }

        $response = [
            'status'  => $status,
            'message' => $message,
            'data'    => $data
        ];

        $statusCode = ($status === 'success') ? 200 : 400;
        self::jsonResponse($response, $statusCode);
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
     * Şifreyi bcrypt algoritmasıyla güvenli bir şekilde hash'ler.
     * Boş veya null gelirse güvenliği sağlamak adına exception fırlatır.
     */
    public static function hashPassword(?string $password): string
    {
        if (empty($password)) {
            throw new \Exception('Password cannot be empty or null.');
        }
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * Düz metin şifreyi veritabanındaki hash ile karşılaştırır.
     * Herhangi biri null veya boşsa her zaman false döner.
     */
    public static function verifyPassword(?string $password, ?string $hash): bool
    {
        if (empty($password) || empty($hash)) {
            return false;
        }
        return password_verify($password, $hash);
    }


    /**
     * E-posta adresini zararlı karakterlerden temizler.
     */
    public static function sanitizeEmail(string $email): string
    {
        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }

    /**
     * Gelen veriyi güvenli bir ondalıklı (float) sayıya dönüştürür.
     * Fiyat ve oran hesaplamalarında kullanılır.
     */
    public static function sanitizeFloat($value): float
    {
        return (float) filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }

    /**
     * Bir diziden sadece belirtilen anahtarlara (keys) sahip olanları seçer.
     * Güvenli mass-assignment (toplu atama) için kullanılır.
     */
    public static function arrayOnly(array $source, array $keys): array
    {
        return array_intersect_key($source, array_flip($keys));
    }

    /**
     * Hızlı hata ayıklama aracı. (Dump & Die)
     */
    public static function dd(...$vars): void
    {
        echo '<pre style="background:#1d232a;color:#a6adbb;padding:16px;border-radius:8px;font-size:14px;overflow-x:auto;z-index:9999;position:relative;">';
        foreach ($vars as $var) {
            var_dump($var);
        }
        echo '</pre>';
        exit;
    }

    /**
     * Metni URL dostu bir slug'a çevirir (Örn: "Utku", "Egemen" -> "utku-egemen")
     * (SystemMethod için global kullanılabilir versiyon)
     */
    public static function slugify(string ...$texts): string
    {
        $text = implode('-', $texts);
        $text = mb_strtolower($text, 'UTF-8');
        
        $search = [
            'ç', 'ğ', 'ı', 'ö', 'ş', 'ü', // TR
            'ä', 'ß',                     // DE
            'ñ',                          // ES
            'é', 'è', 'ê', 'ë', 'à', 'á', 'â', 'î', 'ï', 'í', 'ô', 'ó', 'ú', 'û', 'ù', 'æ', 'œ' // FR & ES
        ];
        $replace = [
            'c', 'g', 'i', 'o', 's', 'u',
            'ae', 'ss',
            'n',
            'e', 'e', 'e', 'e', 'a', 'a', 'a', 'i', 'i', 'i', 'o', 'o', 'u', 'u', 'u', 'ae', 'oe'
        ];
        
        $text = str_replace($search, $replace, $text);
        $text = preg_replace('/[^a-z0-9\-]/', '-', $text);
        $text = preg_replace('/-+/', '-', $text);
        return trim($text, '-');
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

if (!function_exists('apiResponse')) {
    function apiResponse(string $status, string $message, array $data = [], \Throwable $exception = null): void {
        \Bin\SystemMethod::apiResponse($status, $message, $data, $exception);
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

if (!function_exists('sanitizeFloat')) {
    function sanitizeFloat($value): float {
        return \Bin\SystemMethod::sanitizeFloat($value);
    }
}

if (!function_exists('arrayOnly')) {
    function arrayOnly(array $source, array $keys): array {
        return \Bin\SystemMethod::arrayOnly($source, $keys);
    }
}

if (!function_exists('dd')) {
    function dd(...$vars): void {
        \Bin\SystemMethod::dd(...$vars);
    }
}

if (!function_exists('slugify')) {
    function slugify(string ...$texts): string {
        return \Bin\SystemMethod::slugify(...$texts);
    }
}
