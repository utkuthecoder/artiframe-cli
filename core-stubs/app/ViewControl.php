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

// 3. Sadece View'a özgü metodları yükle
require_once __DIR__ . '/../bin/ViewMethod.php';

// 4. Oturum (Session) Başlatma
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

/*
// -------------------------------------------------------------------------
// 5. Auth Guard (Oturum Kalkanı)
// -------------------------------------------------------------------------
// Tüm sistemi varsayılan olarak "sadece giriş yapmış üyelere açık" hale 
// getirmek isterseniz aşağıdaki bloğun yorumunu kaldırın. 
// Dışarıya açık olmasını istediğiniz sayfaların (örn: landing.php) 
// en tepesine define('ALLOW_PUBLIC_VIEW', true); ekleyerek kalkanı delebilirsiniz.

if (!defined('ALLOW_PUBLIC_VIEW') || ALLOW_PUBLIC_VIEW !== true) {
    if (empty($_SESSION['kullaniciId']) && empty($_SESSION['kullanici_id'])) {
        header("Location: /auth/giris");
        exit;
    }
}
*/

/*
// -------------------------------------------------------------------------
// 6. RBAC (Rol Bazlı Erişim Kontrolü) Örneği
// -------------------------------------------------------------------------
// Kullanıcıların yetkilerine göre sayfalara erişimini denetlemek isterseniz:
// Varsayalım ki roller: 1 = Admin, 2 = Moderatör, 3 = Standart Kullanıcı

// Bu sayfanın sadece belirli rollere açık olduğunu belirtmek için sayfanın 
// en başına $allowedRoles = [1, 2]; tanımlayabilirsiniz.

if (isset($allowedRoles) && is_array($allowedRoles)) {
    $userRole = $_SESSION['rol'] ?? 3; // Session'da rol yoksa standart kabul et
    
    if (!in_array($userRole, $allowedRoles)) {
        // Yetkisiz erişim! 403 sayfasına veya ana sayfaya yönlendir:
        header("Location: /yetkisiz-erisim");
        exit;
    }
}
*/
