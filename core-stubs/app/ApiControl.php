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

// 4. Oturum (Session) Konfigürasyonları
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Beni Hatırla Kontrolü
if (empty($_SESSION['kullaniciId']) && !empty($_COOKIE['remember_me'])) {
    $rememberedIdHex = \Bin\SystemMethod::validateRememberMeCookie();
    if ($rememberedIdHex) {
        $_SESSION['kullaniciId'] = hex2bin($rememberedIdHex);
    }
}

// 5. API Konfigürasyonları (JSON Response Header vs.)
header('Content-Type: application/json; charset=utf-8');

// 6. İzin Verilen HTTP Metodlarının Kontrolü
$allowed = isset($allowedMethods) && is_array($allowedMethods) ? $allowedMethods : ['POST'];
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN';

if ($requestMethod === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if (!in_array($requestMethod, $allowed)) {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => "Geçersiz istek metodu. Sadece " . implode(', ', $allowed) . " kabul edilir."]);
    exit;
}

// 7. Global CSRF Duvarı
if (in_array($requestMethod, ['POST', 'PUT', 'DELETE'])) {
    // 1. Header'dan kontrol et (Fetch/Axios SPA uyumu)
    $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

    // 2. Eğer Header'da yoksa, JSON Body'den kontrol et
    if (empty($token)) {
        $input = json_decode(file_get_contents('php://input'), true);
        if (is_array($input) && !empty($input['csrf_token'])) {
            $token = $input['csrf_token'];
        }
    }

    // 3. Eğer JSON'da da yoksa standart POST datasından kontrol et (Eski usül form)
    if (empty($token)) {
        $token = $_POST['csrf_token'] ?? '';
    }

    if (!\Bin\SystemMethod::verifyCsrf($token)) {
        http_response_code(403);
        echo json_encode([
            'status' => 'error',
            'message' => 'CSRF doğrulaması başarısız. Lütfen sayfayı yenileyip tekrar deneyin.'
        ]);
        exit;
    }
}

// 8. CORS (Cross-Origin Resource Sharing) Kuralları
// Geliştirme aşamasında her yerden gelen isteklere izin ver
if ($_ENV['APP_ENV'] === 'development') {
    header('Access-Control-Allow-Origin: *');
} else {
    // Canlı ortamda sadece belirlenen adreslere izin ver
    header('Access-Control-Allow-Origin: https://senindomainin.com');
}

/*
// -------------------------------------------------------------------------
// 8. Auth Guard (Oturum Kalkanı - API İçin)
// -------------------------------------------------------------------------
// Eğer bu API uç noktasının (endpoint) ZORUNLU olarak giriş yapmış üyelere 
// açık olmasını istiyorsanız aşağıdaki bloğu kullanabilirsiniz.
// Not: Auth. işlem yapmayan tüm özel (private) API'ler için önerilir.

if (empty($_SESSION['kullaniciId']) && empty($_SESSION['kullanici_id'])) {
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'message' => 'Bu işlemi yapabilmek için giriş yapmalısınız.'
    ]);
    exit;
}
*/

/*
// -------------------------------------------------------------------------
// 9. RBAC (Rol Bazlı Erişim Kontrolü - API İçin)
// -------------------------------------------------------------------------
// Belirli bir API işleminin (örn: ürün silme) sadece Admin veya yetkili 
// roller tarafından yapılmasını sağlamak için.
// API dosyanızın en tepesinde $allowedRoles = [1]; şeklinde tanımlayabilirsiniz.

if (isset($allowedRoles) && is_array($allowedRoles)) {
    $userRole = $_SESSION['rol'] ?? 3; // Örn: 1=Admin, 2=Mod, 3=User
    
    if (!in_array($userRole, $allowedRoles)) {
        http_response_code(403);
        echo json_encode([
            'status' => 'error',
            'message' => 'Bu işlem için yeterli yetkiniz bulunmuyor.'
        ]);
        exit;
    }
}
*/
