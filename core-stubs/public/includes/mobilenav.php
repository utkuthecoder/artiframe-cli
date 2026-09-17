<?php
// $allowedMenuItems sidebar.php üzerinden oluşturulup buraya kadar ulaştı. 
// Eğer doğrudan mobilenav yüklenirse hata vermemesi için varsayılan atayalım:
if (!isset($allowedMenuItems)) {
    $allowedMenuItems = [];
}

// Sadece mobilde gösterilecek öğeleri filtrele
$mobileAllowedItems = array_filter($allowedMenuItems, function($item) {
    return !isset($item['show_in_mobile']) || $item['show_in_mobile'] === true;
});
$mobileAllowedItems = array_values($mobileAllowedItems);

$primaryItems = array_slice($mobileAllowedItems, 0, 4);
$secondaryItems = array_slice($mobileAllowedItems, 4);

$currentUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
?>
<!-- Mobile Navigation Component Starts Here -->
<div class="app-mobile-nav d-lg-none">
    <div class="mobile-bottom-bar">
        <?php foreach ($primaryItems as $item): ?>
            <?php 
                // Anasayfa ise tam eşleşme, değilse başlangıç eşleşmesi arar
                if ($item['url'] === '/') {
                    $isActive = ($currentUrl === '/');
                } else {
                    $isActive = (strpos($currentUrl, $item['url']) === 0);
                }
            ?>
            <a href="<?= htmlspecialchars($item['url']) ?>" class="bottom-nav-item <?= $isActive ? 'active' : '' ?>" title="<?= htmlspecialchars($item['title']) ?>">
                <?php if (isset($item['icon_img'])): ?>
                    <img src="<?= htmlspecialchars($item['icon_img']) ?>" class="menu-svg-icon" alt="<?= htmlspecialchars($item['title']) ?>">
                <?php else: ?>
                    <i class="<?= htmlspecialchars($item['icon']) ?>"></i>
                    <span class="nav-label"><?= htmlspecialchars($item['title']) ?></span>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
        
        <?php if (!empty($secondaryItems)): ?>
            <button class="bottom-nav-item mobile-menu-toggle" id="mobileMenuBtn" aria-label="Menüyü Aç">
                <i class="fa-solid fa-border-all"></i>
                <span class="nav-label">Menü</span>
            </button>
        <?php endif; ?>
    </div>

    <?php if (!empty($secondaryItems)): ?>
    <div class="mobile-bottom-sheet" id="mobileBottomSheet">
        <div class="sheet-overlay" id="sheetOverlay"></div>
        <div class="sheet-content">
            <div class="sheet-header">
                <div class="drag-handle"></div>
                <h3 class="sheet-title">Diğer İşlemler</h3>
            </div>
            <div class="sheet-body">
                <?php foreach ($secondaryItems as $item): ?>
                    <a href="<?= htmlspecialchars($item['url']) ?>" class="sheet-item" <?= !empty($item['target']) ? 'target="'.htmlspecialchars($item['target']).'"' : '' ?>>
                        <div class="sheet-icon-wrapper" style="<?php echo isset($item['icon_img']) ? 'width:auto; margin-right:0; margin-left:12px; background:none;' : ''; ?>">
                            <?php if (isset($item['icon_img'])): ?>
                                <img src="<?= htmlspecialchars($item['icon_img']) ?>" class="menu-svg-icon" alt="<?= htmlspecialchars($item['title']) ?>">
                            <?php else: ?>
                                <i class="<?= htmlspecialchars($item['icon']) ?>"></i>
                            <?php endif; ?>
                        </div>
                        <?php if (!isset($item['icon_img'])): ?>
                            <span class="sheet-label"><?= htmlspecialchars($item['title']) ?></span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<!-- Mobile Navigation Component Ends Here -->
