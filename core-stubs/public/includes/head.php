<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow, noarchive, nosnippet">

<!-- Open Graph / Sosyal Medya Paylaşım (Zorunlu SEO/SMO Metaları) -->
<meta property="og:title" content="<?= defined('APP_NAME') ? APP_NAME : 'ArtiFrame' ?>">
<meta property="og:description" content="ArtiFrame tabanlı web uygulaması.">
<meta property="og:type" content="website">
<meta property="og:url" content="<?= defined('APP_URL') ? APP_URL : 'http://localhost' ?><?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? '') ?>">
<meta property="og:image" content="<?= defined('APP_URL') ? APP_URL : '' ?>/assets/images/social-cover.jpg">
<meta property="og:site_name" content="<?= defined('APP_NAME') ? APP_NAME : 'ArtiFrame' ?>">

<!-- Twitter Cards -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= defined('APP_NAME') ? APP_NAME : 'ArtiFrame' ?>">
<meta name="twitter:description" content="ArtiFrame tabanlı web uygulaması.">
<meta name="twitter:image" content="<?= defined('APP_URL') ? APP_URL : '' ?>/assets/images/social-cover.jpg">

<!-- CSRF Token (AJAX/Fetch işlemleri için global erişim sağlar) -->
<?php
$shouldGenerateCsrf = true;
if (empty($_SESSION['kullaniciId']) && defined('ALLOW_PUBLIC_VIEW') && ALLOW_PUBLIC_VIEW === true) {
    $shouldGenerateCsrf = false;
}
?>
<?php if ($shouldGenerateCsrf): ?>
    <meta name="csrf-token" content="<?= \Bin\SystemMethod::generateCsrf() ?>">
<?php endif; ?>

<!-- FOUC (Tema göz kırpmasını) Engellemek için Inline Tema Yükleyici -->
<script>
    const savedTheme = localStorage.getItem('artiframe_theme_mode') || localStorage.getItem('app-mode') || 'dark';
    document.documentElement.setAttribute('data-mode', savedTheme);
</script>

<!-- Favicons -->
<link rel="icon" type="image/png" href="/assets/images/favicon/favicon-96x96.png" sizes="96x96" />
<link rel="icon" type="image/svg+xml" href="/assets/images/favicon/favicon.svg" />
<link rel="shortcut icon" href="/assets/images/favicon/favicon.ico" />
<link rel="apple-touch-icon" sizes="180x180" href="/assets/images/favicon/apple-touch-icon.png" />
<meta name="apple-mobile-web-app-title" content="<?= defined('APP_NAME') ? APP_NAME : 'ArtiFrame' ?>" />
<link rel="manifest" href="/site.webmanifest" />

<!-- FontAwesome 6 Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<!-- Global Assets -->
<!-- Viewer.js (Mobile-First Image Viewer) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.11.6/viewer.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.11.6/viewer.min.js" defer></script>

<link rel="stylesheet" href="/assets/css/root/app.css?v=<?= defined('APP_VERSION') ? APP_VERSION : '1.0.0' ?>">
<script src="/assets/js/root/app.js?v=<?= defined('APP_VERSION') ? APP_VERSION : '1.0.0' ?>" defer></script>

<!-- Component Assets (Bileşen CSS/JS Dosyaları) -->
<link rel="stylesheet" href="/assets/css/components/header.css?v=<?= defined('APP_VERSION') ? APP_VERSION : '1.0.0' ?>">
<link rel="stylesheet" href="/assets/css/components/footer.css?v=<?= defined('APP_VERSION') ? APP_VERSION : '1.0.0' ?>">
<link rel="stylesheet" href="/assets/css/components/sidebar.css?v=<?= defined('APP_VERSION') ? APP_VERSION : '1.0.0' ?>">
<link rel="stylesheet" href="/assets/css/components/mobilenav.css?v=<?= defined('APP_VERSION') ? APP_VERSION : '1.0.0' ?>">
<link rel="stylesheet" href="/assets/css/components/theme-modal.css?v=<?= defined('APP_VERSION') ? APP_VERSION : '1.0.0' ?>">

<script src="/assets/js/components/header.js?v=<?= defined('APP_VERSION') ? APP_VERSION : '1.0.0' ?>" defer></script>
<script src="/assets/js/components/sidebar.js?v=<?= defined('APP_VERSION') ? APP_VERSION : '1.0.0' ?>" defer></script>
<script src="/assets/js/components/mobilenav.js?v=<?= defined('APP_VERSION') ? APP_VERSION : '1.0.0' ?>" defer></script>
<script src="/assets/js/components/theme-modal.js?v=<?= defined('APP_VERSION') ? APP_VERSION : '1.0.0' ?>" defer></script>
