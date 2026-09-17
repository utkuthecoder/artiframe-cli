document.addEventListener('DOMContentLoaded', () => {
    const htmlElement = document.documentElement;

    // Tüm tema destekli resimleri güncelleyen fonksiyon
    const updateThemeImages = () => {
        const currentMode = htmlElement.getAttribute('data-mode');
        document.querySelectorAll('img.theme-image').forEach(img => {
            const newSrc = currentMode === 'dark' ? img.getAttribute('data-dark-src') : img.getAttribute('data-light-src');
            if (newSrc) {
                // Sadece son dosya ismini (veya tam yolu) kontrol ederek gereksiz src değişimini önle
                if (!img.src.endsWith(newSrc) && img.getAttribute('src') !== newSrc) {
                    img.src = newSrc;
                }
            }
        });
    };

    // Sayfa yüklendiğinde resimleri ayarla
    updateThemeImages();

    // Tema değişikliğini dinle (HTML tag'indeki data-mode attribute'u değiştiğinde)
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.type === 'attributes' && mutation.attributeName === 'data-mode') {
                updateThemeImages();
            }
        });
    });
    observer.observe(htmlElement, { attributes: true });

    const themeToggleBtn = document.getElementById('themeToggleBtn');

    if (themeToggleBtn) {
        // İkonu güncel moda göre ayarla
        const updateIcon = () => {
            const currentMode = htmlElement.getAttribute('data-mode');
            const icon = themeToggleBtn.querySelector('i');
            
            if (icon) {
                if (currentMode === 'dark') {
                    icon.className = 'fa-solid fa-sun';
                } else {
                    icon.className = 'fa-solid fa-moon';
                }
            }
        };

        // Sayfa yüklendiğinde doğru ikonu göster
        updateIcon();

        // observer ile butonu senkronize tut
        const btnObserver = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'attributes' && mutation.attributeName === 'data-mode') {
                    updateIcon();
                }
            });
        });
        btnObserver.observe(htmlElement, { attributes: true });

        // Butona tıklandığında temayı değiştir
        themeToggleBtn.addEventListener('click', () => {
            const currentMode = htmlElement.getAttribute('data-mode');
            const newMode = currentMode === 'dark' ? 'light' : 'dark';
            
            // HTML etiketine data-mode olarak yaz
            htmlElement.setAttribute('data-mode', newMode);
            
            // Kullanıcının tercihini localStorage'a kaydet (Bir sonraki girişte hatırlanır)
            localStorage.setItem('app-mode', newMode);
            localStorage.setItem('artiframe_theme_mode', newMode); // root app.js ile senkronize
        });
    }
});
