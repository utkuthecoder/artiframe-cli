<?php
$headerAvatarUrl = "https://sinavortagim.artilingo.com/system/default-avatar.png";
if (!empty($_SESSION['kullaniciAdi'])) {
    $headerUser = \Src\User::getProfileByUsername($_SESSION['kullaniciAdi']);
    if ($headerUser && !empty($headerUser['profilFotografi'])) {
        $headerAvatarUrl = str_starts_with($headerUser['profilFotografi'], 'http') 
            ? $headerUser['profilFotografi'] 
            : "/uploads/profiles/" . $headerUser['profilFotografi'];
    }
}
?>
<!-- Header Component Starts Here -->
<?php
$isGuestHeader = empty($_SESSION['kullaniciId']);
$wsToken = '';
$wsUserIdHex = '';
if (!$isGuestHeader) {
    $wsUserIdHex = bin2hex($_SESSION['kullaniciId']);
    $wsToken = hash_hmac('sha256', $wsUserIdHex, 'S1nav0rtagim_WS_SecReT');
}
?>
<script>
    window.WS_USER_ID = "<?= $wsUserIdHex ?>";
    window.WS_TOKEN = "<?= $wsToken ?>";
</script>
<header class="app-header" <?= $isGuestHeader ? 'style="justify-content: center; position: relative;"' : '' ?>>
    
    <?php if ($isGuestHeader): ?>
        <a href="/" style="display: flex; align-items: center; justify-content: center; height: 100%;">
            <img src="/assets/images/sinavOrtagim.svg" data-light-src="/assets/images/sinavOrtagim.svg" data-dark-src="/assets/images/sinavOrtagim-dark.svg" alt="Sınav Ortağım" class="theme-image" style="height: 36px; width: auto;">
        </a>
        <button class="header-action theme-toggle-btn" id="themeToggleBtn" title="Tema Değiştir" style="position: absolute; right: 20px;">
            <i class="fa-solid fa-moon"></i>
        </button>
    <?php else: ?>
        <!-- Tema Değiştirici -->
        <button class="header-action theme-toggle-btn" id="themeToggleBtn" title="Tema Değiştir" style="margin-right: auto;">
            <i class="fa-solid fa-moon"></i>
        </button>
        <!-- Arama -->
        <a href="/ara" class="header-action" title="Ara" style="display: flex; align-items: center; justify-content: center; text-decoration: none;">
            <i class="fa-solid fa-magnifying-glass"></i>
        </a>

        <!-- Sepet -->
        <?php
        $headerCartCount = 0;
        if (!empty($_SESSION['kullaniciId'])) {
            if (!class_exists('\Src\Magaza')) {
                require_once $_SERVER['DOCUMENT_ROOT'] . '/../src/Magaza.php';
            }
            $headerMagaza = new \Src\Magaza();
            $headerCartCount = count($headerMagaza->getCartItems($_SESSION['kullaniciId']));
        }
        ?>
        <a href="/magaza/sepet" class="header-action" id="headerCartBtn" title="Sepetim" style="display: flex; align-items: center; justify-content: center; text-decoration: none; position: relative;">
            <i class="fa-solid fa-cart-shopping"></i>
            <span class="badge" id="cartBadge" style="<?= $headerCartCount > 0 ? 'display: flex;' : 'display: none;' ?> background: var(--brand-primary); color: white; position: absolute; top: -5px; right: -5px; font-size: 0.7rem; border-radius: 50%; width: 16px; height: 16px; align-items: center; justify-content: center;"><?= $headerCartCount ?></span>
        </a>

        <!-- Bildirimler -->
        <div class="header-avatar-container" id="notificationDropdownContainer" title="Bildirimler">
            <button class="header-action" id="notificationBtn">
                <i class="fa-regular fa-bell"></i>
                <span class="badge" id="notificationBadge" style="display: none;">0</span>
            </button>
            
            <div class="profile-dropdown-menu" id="notificationDropdownMenu" style="width: 320px; padding: 0; display: flex; flex-direction: column;">
                <div style="display:flex; border-bottom:1px solid var(--border); position: sticky; top: 0; background: var(--card-bg); z-index: 10; border-radius: 16px 16px 0 0; overflow: hidden;">
                    <button class="notif-tab active" data-target="notif-subscriptions" style="flex:1; padding:12px; background:none; border:none; color:var(--brand-primary); font-weight:600; cursor:pointer; border-bottom:2px solid var(--brand-primary); font-size: 0.8rem; transition: all 0.2s;">Abonelikler</button>
                    <button class="notif-tab" data-target="notif-general" style="flex:1; padding:12px; background:none; border:none; color:var(--text-muted); font-weight:600; cursor:pointer; border-bottom:2px solid transparent; font-size: 0.8rem; transition: all 0.2s;">Genel</button>
                    <button class="notif-tab" data-target="notif-special" style="flex:1; padding:12px; background:none; border:none; color:var(--text-muted); font-weight:600; cursor:pointer; border-bottom:2px solid transparent; font-size: 0.8rem; transition: all 0.2s;">Özel</button>
                    <button class="notif-tab" data-target="notif-system" style="flex:1; padding:12px; background:none; border:none; color:var(--text-muted); font-weight:600; cursor:pointer; border-bottom:2px solid transparent; font-size: 0.8rem; transition: all 0.2s;">Sistem</button>
                </div>
                
                <div id="notif-subscriptions" class="notif-content-pane" style="display:flex; flex-direction:column; max-height: 400px; overflow-y: auto;">
                    <div style="padding: 20px; text-align: center; color: var(--text-muted); font-size: 0.85rem;">Yükleniyor...</div>
                </div>
                
                <div id="notif-general" class="notif-content-pane" style="display:none; flex-direction:column; max-height: 400px; overflow-y: auto;">
                    <div style="padding: 20px; text-align: center; color: var(--text-muted); font-size: 0.85rem;">Yükleniyor...</div>
                </div>

                <div id="notif-special" class="notif-content-pane" style="display:none; flex-direction:column; max-height: 400px; overflow-y: auto;">
                    <div style="padding: 20px; text-align: center; color: var(--text-muted); font-size: 0.85rem;">Yükleniyor...</div>
                </div>

                <div id="notif-system" class="notif-content-pane" style="display:none; flex-direction:column; max-height: 400px; overflow-y: auto;">
                    <div style="padding: 20px; text-align: center; color: var(--text-muted); font-size: 0.85rem;">Yükleniyor...</div>
                </div>

                <div style="padding: 10px; border-top: 1px solid var(--border); text-align: center; background: var(--card-bg); border-radius: 0 0 16px 16px;">
                    <span id="notificationMarkReadText" style="font-size: 0.85rem; font-weight: 500; color: var(--brand-primary); cursor: pointer; display: none;">Tümünü Okundu İşaretle</span>
                </div>
            </div>
        </div>

        <!-- Kullanıcı Avatarı & Açılır Menü -->
        <div class="header-avatar-container" id="profileDropdownContainer" title="Profil İşlemleri">
            <div class="header-avatar" id="profileDropdownBtn">
                <img src="<?= htmlspecialchars($headerAvatarUrl) ?>" alt="Profil" style="object-fit: cover;">
            </div>
            
            <div class="profile-dropdown-menu" id="profileDropdownMenu">
                <a class="dropdown-item" href="/user/profil<?= !empty($_SESSION['kullaniciAdi']) ? '/' . htmlspecialchars($_SESSION['kullaniciAdi']) : '' ?>">
                    <i class="fa-regular fa-user"></i> 
                    <span>Profilim</span>
                </a>
                <a class="dropdown-item" href="/user/basvuru">
                    <i class="fa-solid fa-shield-halved"></i> 
                    <span>Onaylı Hesap</span>
                </a>
                <a class="dropdown-item" href="/kaydedilenler">
                    <i class="fa-regular fa-bookmark"></i> 
                    <span>Kaydedilenler</span>
                </a>
                <a class="dropdown-item" href="/arsiv">
                    <i class="fa-solid fa-box-archive"></i> 
                    <span>Arşiv</span>
                </a>
                <a class="dropdown-item" href="/user/engellediklerim">
                    <i class="fa-solid fa-user-slash"></i> 
                    <span>Engellenenler</span>
                </a>
                <a class="dropdown-item" href="/user/ayarlar">
                    <i class="fa-solid fa-gear"></i> 
                    <span>Ayarlar</span>
                </a>
                <hr class="dropdown-divider">
                <a class="dropdown-item text-danger" href="/logout">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> 
                    <span>Çıkış Yap</span>
                </a>
            </div>
        </div>
    <?php endif; ?>

</header>
<!-- Header Component Ends Here -->
