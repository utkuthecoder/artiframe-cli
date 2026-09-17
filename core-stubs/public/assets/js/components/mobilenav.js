document.addEventListener('DOMContentLoaded', () => {
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mobileBottomSheet = document.getElementById('mobileBottomSheet');
    const sheetOverlay = document.getElementById('sheetOverlay');

    if (mobileMenuBtn && mobileBottomSheet) {
        const sheetContent = mobileBottomSheet.querySelector('.sheet-content');
        
        let scrollPosition = 0;

        const closeMenu = () => {
            mobileBottomSheet.classList.remove('is-open');
            document.documentElement.style.overflow = '';
            document.body.style.overflow = '';
            document.body.style.overscrollBehaviorY = '';
            document.body.style.position = '';
            document.body.style.top = '';
            document.body.style.width = '';
            window.scrollTo(0, scrollPosition);
        };

        const openMenu = () => {
            scrollPosition = window.scrollY;
            mobileBottomSheet.classList.add('is-open');
            document.documentElement.style.overflow = 'hidden';
            document.body.style.overflow = 'hidden';
            document.body.style.overscrollBehaviorY = 'none';
            document.body.style.position = 'fixed';
            document.body.style.top = `-${scrollPosition}px`;
            document.body.style.width = '100%';
        };

        // Menüyü Aç / Kapa (Toggle)
        mobileMenuBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (mobileBottomSheet.classList.contains('is-open')) {
                closeMenu();
            } else {
                openMenu();
            }
        });

        // Overlay'e tıklanınca Menüyü Kapat
        if (sheetOverlay) {
            sheetOverlay.addEventListener('click', closeMenu);
            sheetOverlay.addEventListener('touchmove', (e) => e.preventDefault(), { passive: false });
        }

        // Aşağı Kaydırarak (Swipe) Kapatma
        if (sheetContent) {
            let startY = 0;
            let currentY = 0;
            let isDragging = false;
            let canCloseOnSwipe = false;

            sheetContent.addEventListener('touchstart', (e) => {
                startY = e.touches[0].clientY;
                isDragging = true;
                sheetContent.style.transition = 'none'; 
                
                // Kaydırma hareketinin BAŞLANGICINDA en üstte miyiz kontrol et
                const sheetBody = sheetContent.querySelector('.sheet-body');
                if (sheetBody && sheetBody.contains(e.target)) {
                    canCloseOnSwipe = sheetBody.scrollTop <= 0;
                } else {
                    canCloseOnSwipe = true;
                }
            }, { passive: true });

            sheetContent.addEventListener('touchmove', (e) => {
                if (!isDragging) return;
                currentY = e.touches[0].clientY;
                const diff = currentY - startY;
                
                // Sadece hareket ilk başladığında en üstteysek menüyü kapatmaya izin ver
                if (diff > 0 && canCloseOnSwipe) {
                    if (e.cancelable) e.preventDefault(); 
                    sheetContent.style.transform = `translateY(${diff}px)`;
                }
            }, { passive: false }); 

            sheetContent.addEventListener('touchend', (e) => {
                if (!isDragging) return;
                isDragging = false;
                sheetContent.style.transition = ''; 
                
                const diff = currentY - startY;
                if (diff > 100 && canCloseOnSwipe) {
                    sheetContent.style.transform = '';
                    closeMenu();
                } else {
                    sheetContent.style.transform = '';
                }
            });
        }
    }
});
