<?php
/**
 * ArtiFrame Auth Engine (Guest Only)
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

// 5. Ziyaretçi (Guest) Kontrolü
// Kullanıcı zaten giriş yapmışsa Auth sayfalarına (kayıt, giriş) giremez, yönlendirilir.
if (isset($_SESSION['kullaniciId']) || isset($_SESSION['kullanici_id'])) {
    header("Location: /");
    exit;
}
