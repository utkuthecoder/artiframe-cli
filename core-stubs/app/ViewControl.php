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

// 4. Oturum (Session) ve View Konfigürasyonları
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
