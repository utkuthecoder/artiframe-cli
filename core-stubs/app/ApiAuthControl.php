<?php
/**
 * ArtiFrame Core Engine
 *
 * @package     ArtiFrame
 * @author      Artilingo
 * @license     AGPLv3 (Attribution-ShareAlike Required)
 */

require_once __DIR__ . '/../vendor/autoload.php';

// 1. Ortam değişkenlerini yükle (.env)
\App\DotEnv::load(__DIR__ . '/../.env');

// 2. Uygulama sabitlerini yükle (APP_ENV, APP_VERSION)
require_once __DIR__ . '/../config/app-version.php';

// 3. Sadece API'ye özgü metodları yükle
require_once __DIR__ . '/../bin/SystemMethod.php';

// 4. API Konfigürasyonları (JSON Response Header vs.)
header('Content-Type: application/json; charset=utf-8');

// 5. İzin Verilen HTTP Metodlarının Kontrolü
$allowed = isset($allowedMethods) && is_array($allowedMethods) ? $allowedMethods : ['POST'];
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN';

if ($requestMethod === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if (!in_array($requestMethod, $allowed)) {
    http_response_code(405);
    echo json_encode([
        'status' => 'error', 
        'message' => "Geçersiz istek metodu. Sadece " . implode(', ', $allowed) . " kabul edilir."
    ]);
    exit;
}

// NOT: Bu dosya (ApiAuthControl), sadece Auth (Giriş/Kayıt) işlemleri içindir.
// İleride ApiControl.php dosyasına RBAC veya Oturum kontrolleri eklense bile,
// buraya giriş yapmamış (guest) kullanıcıların istek atabilmesi için 
// oturum engeli KOYULMAMALIDIR. 

// 6. Rate Limiting (Kaba Kuvvet ve Spam Koruması)
// Aynı IP'den saniyede maksimum 1 istek atılmasına izin verilir.
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$rateKey = 'auth_rate_limit:' . $ip;
$limit = 1; 

try {
    $redis = \Src\Service\RedisService::getInstance();
    $currentCount = $redis->get($rateKey);

    if ($currentCount !== false && $currentCount >= $limit) {
        http_response_code(429); // Too Many Requests
        echo json_encode([
            'status' => 'error', 
            'message' => 'Lütfen yavaşlayın. Çok sık istek gönderiyorsunuz.'
        ]);
        exit;
    }

    $redis->incr($rateKey);
    
    // Eğer anahtar ilk defa oluşturuluyorsa ömrünü 1 saniye olarak belirle
    if ($currentCount === false || $currentCount == 0) {
        $redis->expire($rateKey, 1);
    }
} catch (\Exception $e) {
    // Redis'e ulaşılamazsa (sunucu çökmesi vs.) kayıt olma akışını tamamen durdurmak yerine 
    // loglanabilir veya sessizce pas geçilebilir.
    error_log("Redis Bağlantı Hatası (Rate Limit Aşıldı): " . $e->getMessage());
}
