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
// API uç noktalarında tanımlanan $allowedMethods dizisini baz alır. Tanımsızsa sadece POST kabul eder.
$allowed = isset($allowedMethods) && is_array($allowedMethods) ? $allowedMethods : ['POST'];
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN';

// Preflight istekleri (OPTIONS) CORS için hayati öneme sahiptir, her halükarda 200 dönüp çıkış yapmasını sağlarız.
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

/* 
// -------------------------------------------------------------------------
// 6. CORS (Cross-Origin Resource Sharing) Kuralları
// -------------------------------------------------------------------------
// Başka domainlerden veya mobil uygulamalardan gelen API isteklerini kabul 
// etmek isterseniz aşağıdaki satırları aktifleştirin. "*" yerine kendi 
// domaininizi (örn: "https://artilingo.com") yazarak güvenliği artırabilirsiniz.

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
*/

/*
// -------------------------------------------------------------------------
// 7. Rate Limiting (Basit IP Tabanlı Hız Sınırlandırma - Redis Örneği)
// -------------------------------------------------------------------------
// Kötü niyetli kişilerin veya botların API'nizi flood etmesini engeller.
// Redis servisinizi aktif ettiyseniz bu bloğu kullanabilirsiniz.
// Aşağıdaki örnekte, aynı IP adresinin 1 dakikada 60'tan fazla istek yapması engellenir.

$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$rateKey = 'rate_limit:' . $ip;
$limit = 60; // 1 dakikadaki maksimum istek sayısı
$redis = \Src\Service\RedisService::getInstance();

$currentCount = $redis->get($rateKey);
if ($currentCount !== null && $currentCount >= $limit) {
    http_response_code(429); // 429 Too Many Requests
    echo json_encode(['status' => 'error', 'message' => 'Çok fazla istek gönderdiniz. Lütfen daha sonra tekrar deneyin.']);
    exit;
}

$redis->incr($rateKey);
if ($currentCount === null) {
    $redis->expire($rateKey, 60); // Sayacı 60 saniye sonra sıfırla
}
*/
