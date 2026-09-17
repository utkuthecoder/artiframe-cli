<?php
// Geçerli kullanıcının rolünü alalım (Eğer login değilse misafir varsayalım)
$userRole = $_SESSION['rol'] ?? 'misafir';

// Tüm olası menü ögeleri ve erişim izinleri (Kullanıcı dilediği gibi burayı genişletebilir)
$allMenuItems = [
    [
        'title' => 'Anasayfa',
        'url' => '/',
        'icon' => 'fa-solid fa-house',
        'roles' => ['all'],
        'position' => 'top',
        'show_in_mobile' => true
    ],
    [
        'title' => 'Soru Duvarı',
        'url' => '/soru-duvari',
        'icon' => 'fa-solid fa-clipboard-question',
        'roles' => ['all'],
        'position' => 'top',
        'show_in_mobile' => true
    ],
    [
        'title' => 'Gruplar',
        'url' => '/groups/list',
        'icon' => 'fa-solid fa-users',
        'roles' => ['all'],
        'position' => 'top',
        'show_in_mobile' => true
    ],
    [
        'title' => 'Mağaza',
        'url' => '/magaza/magaza',
        'icon' => 'fa-solid fa-store',
        'roles' => ['all'],
        'position' => 'bottom',
        'show_in_mobile' => true
    ],
    [
        'title' => 'Gelişim Takibi',
        'url' => '/gelisim-takibi',
        'icon' => 'fa-solid fa-chart-line',
        'roles' => [1, 0], // Öğrenci ve Admin
        'position' => 'top',
        'show_in_mobile' => true
    ],
    [
        'title' => 'Atama Analizi',
        'url' => '/atama-analizi',
        'icon' => 'fa-solid fa-ranking-star',
        'roles' => [1, 4, 0], // Öğrenci, Mezun ve Admin
        'position' => 'top',
        'show_in_mobile' => true
    ],
    [
        'title' => 'Moderasyon Paneli',
        'url' => '/admin/u/moderasyon',
        'icon' => 'fa-solid fa-shield-halved',
        'roles' => [0], // Admin
        'position' => 'bottom',
        'show_in_mobile' => true
    ],
    [
        'title' => 'Sepetim',
        'url' => '/magaza/sepet',
        'icon' => 'fa-solid fa-cart-shopping',
        'roles' => ['all'],
        'position' => 'bottom',
        'show_in_mobile' => true
    ],
    [
        'title' => 'Cüzdan & Envanter',
        'url' => '/user/envanter',
        'icon' => 'fa-solid fa-wallet',
        'roles' => ['all'],
        'position' => 'bottom',
        'show_in_mobile' => true
    ],
    [
        'title' => 'Ödeme Yöntemleri',
        'url' => '/magaza/odeme-yontemlerim',
        'icon' => 'fa-solid fa-credit-card',
        'roles' => ['all'],
        'position' => 'bottom',
        'show_in_mobile' => true
    ],
    [
        'title' => 'Aboneliklerim',
        'url' => '/magaza/aboneliklerim',
        'icon' => 'fa-solid fa-clock-rotate-left',
        'roles' => ['all'],
        'position' => 'bottom',
        'show_in_mobile' => true
    ],
    [
        'title' => 'Yardım',
        'url' => 'https://sinavortagim.com/yardim',
        'icon' => 'fa-regular fa-circle-question',
        'roles' => ['all'],
        'position' => 'footer',
        'show_in_mobile' => true,
        'target' => '_blank'
    ],
    [
        'title' => 'Politikalar',
        'url' => 'https://sinavortagim.com/politikalar',
        'icon' => 'fa-solid fa-scale-balanced',
        'roles' => ['all'],
        'position' => 'footer',
        'show_in_mobile' => true,
        'target' => '_blank'
    ]
];

// RBAC Filtreleme
$allowedMenuItems = array_filter($allMenuItems, function($item) use ($userRole) {
    return in_array('all', $item['roles']) || in_array($userRole, $item['roles']);
});
?>
<!-- Sidebar Component Starts Here -->
<aside class="app-sidebar">
    <div class="sidebar-logo">
        <img src="/assets/images/icon.svg" data-light-src="/assets/images/icon.svg" data-dark-src="/assets/images/icon-dark.svg" alt="Sınav Ortağım İkon" class="logo-icon theme-image">
        <img src="/assets/images/sinavOrtagim.svg" data-light-src="/assets/images/sinavOrtagim.svg" data-dark-src="/assets/images/sinavOrtagim-dark.svg" alt="Sınav Ortağım Logo" class="logo-full theme-image">
    </div>

    <nav class="sidebar-nav">
        <?php foreach ($allowedMenuItems as $item): ?>
            <?php if ($item['position'] === 'top'): ?>
                <a href="<?= htmlspecialchars($item['url']) ?>" <?= isset($item['target']) ? 'target="' . $item['target'] . '"' : '' ?> class="nav-item <?= ($_SERVER['REQUEST_URI'] == $item['url']) ? 'active' : '' ?>">
                    <div class="nav-icon" style="<?php echo isset($item['icon_img']) ? 'width:100%; margin-right:0;' : ''; ?>">
                        <?php if (isset($item['icon_img'])): ?>
                            <img src="<?= htmlspecialchars($item['icon_img']) ?>" class="menu-svg-icon" alt="<?= htmlspecialchars($item['title']) ?>">
                        <?php else: ?>
                            <i class="<?= htmlspecialchars($item['icon']) ?>"></i>
                        <?php endif; ?>
                    </div>
                    <?php if (!isset($item['icon_img'])): ?>
                        <span class="nav-label"><?= htmlspecialchars($item['title']) ?></span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </nav>

    <div class="sidebar-footer">
        <?php 
        $hasBottom = false;
        foreach ($allowedMenuItems as $item): 
            if ($item['position'] === 'bottom'): 
                $hasBottom = true;
        ?>
                <a href="<?= htmlspecialchars($item['url']) ?>" <?= isset($item['target']) ? 'target="' . $item['target'] . '"' : '' ?> class="nav-item <?= ($_SERVER['REQUEST_URI'] == $item['url']) ? 'active' : '' ?>">
                    <div class="nav-icon" style="<?php echo isset($item['icon_img']) ? 'width:100%; margin-right:0;' : ''; ?>">
                        <?php if (isset($item['icon_img'])): ?>
                            <img src="<?= htmlspecialchars($item['icon_img']) ?>" class="menu-svg-icon" alt="<?= htmlspecialchars($item['title']) ?>">
                        <?php else: ?>
                            <i class="<?= htmlspecialchars($item['icon']) ?>"></i>
                        <?php endif; ?>
                    </div>
                    <?php if (!isset($item['icon_img'])): ?>
                        <span class="nav-label"><?= htmlspecialchars($item['title']) ?></span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>

        <?php if ($hasBottom): ?>
            <hr style="border-color: rgba(255,255,255,0.1); margin: 0.5rem 1rem; border-width: 1px; opacity: 1;">
        <?php endif; ?>

        <?php foreach ($allowedMenuItems as $item): ?>
            <?php if ($item['position'] === 'footer'): ?>
                <a href="<?= htmlspecialchars($item['url']) ?>" <?= isset($item['target']) ? 'target="' . $item['target'] . '"' : '' ?> class="nav-item <?= ($_SERVER['REQUEST_URI'] == $item['url']) ? 'active' : '' ?>">
                    <div class="nav-icon" style="<?php echo isset($item['icon_img']) ? 'width:100%; margin-right:0;' : ''; ?>">
                        <?php if (isset($item['icon_img'])): ?>
                            <img src="<?= htmlspecialchars($item['icon_img']) ?>" class="menu-svg-icon" alt="<?= htmlspecialchars($item['title']) ?>">
                        <?php else: ?>
                            <i class="<?= htmlspecialchars($item['icon']) ?>"></i>
                        <?php endif; ?>
                    </div>
                    <?php if (!isset($item['icon_img'])): ?>
                        <span class="nav-label"><?= htmlspecialchars($item['title']) ?></span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</aside>
<!-- Sidebar Component Ends Here -->
