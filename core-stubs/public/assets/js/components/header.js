document.addEventListener('DOMContentLoaded', () => {
    // Mobil Profil Açılır Menü Mantığı
    const profileBtn = document.getElementById('profileDropdownBtn');
    const profileMenu = document.getElementById('profileDropdownMenu');

    if (profileBtn && profileMenu) {
        // Tıklama ile menüyü aç/kapat
        profileBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            profileMenu.classList.toggle('show');
        });

        // Menü dışına tıklanınca kapat
        document.addEventListener('click', (e) => {
            if (!profileBtn.contains(e.target) && !profileMenu.contains(e.target)) {
                profileMenu.classList.remove('show');
            }
        });
    }

    // Bildirim Menüsü Mantığı
    const notifBtn = document.getElementById('notificationBtn');
    const notifMenu = document.getElementById('notificationDropdownMenu');
    const notifBadge = document.getElementById('notificationBadge');
    const notifSubscriptions = document.getElementById('notif-subscriptions');
    const notifGeneral = document.getElementById('notif-general');
    const notifSpecial = document.getElementById('notif-special');
    const notifSystem = document.getElementById('notif-system');
    const markReadBtn = document.getElementById('notificationMarkReadText');
    const notifTabs = document.querySelectorAll('.notif-tab');
    
    let unreadIds = [];

    const createItemHTML = (n) => {
        const item = document.createElement('a');
        item.className = 'dropdown-item notif-item';
        item.dataset.id = n.bildirimId;
        item.style.display = 'flex';
        item.style.flexDirection = 'column';
        item.style.alignItems = 'flex-start';
        item.style.padding = '12px 15px';
        item.style.whiteSpace = 'normal';
        item.style.borderBottom = '1px solid var(--border)';
        
        if (n.okundu === 0) {
            item.style.background = 'rgba(139, 92, 246, 0.08)';
            item.style.borderLeft = '3px solid var(--brand-primary)';
        } else {
            item.style.borderLeft = '3px solid transparent';
            item.style.opacity = '0.7';
        }
        
        // Dinamik Yönlendirme (Deep-link) - Backend Notification.php ile uyumlu
        let link = 'javascript:void(0)';
        if (n.hedefTuru === 1) link = n.gonderenAdi ? `/user/profil/${n.gonderenAdi}` : `javascript:void(0)`; // PROFIL
        else if (n.hedefTuru === 2) link = `/soru/${n.hedefId}`; // SORU
        else if (n.hedefTuru === 3) link = `/soru/${n.hedefId}`; // COZUM -> Soru detayına git
        else if (n.hedefTuru === 4) link = `/gonderi/${n.hedefId}`; // GLOBAL GONDERI
        else if (n.hedefTuru === 6) link = `/groups/grup/${n.hedefId}`; // GRUP (hedefId = slug)
        else if (n.hedefTuru === 8) link = `/groups/gonderi/${n.hedefId}`; // GRUP GÖNDERİSİ
        
        item.href = link;
        
        // Avatar Tasarımı
        let avatarHtml = '';
        if (n.gonderenAdi) {
            let avatarUrl = "https://sinavortagim.artilingo.com/system/default-avatar.png";
            if (n.profilFotografi) {
                avatarUrl = n.profilFotografi.startsWith('http') ? n.profilFotografi : '/uploads/profiles/' + n.profilFotografi;
            }
            avatarHtml = `<img data-src="${avatarUrl}" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1 1'%3E%3C/svg%3E" style="width:36px; height:36px; border-radius:50%; object-fit:cover;" class="lazy-notif-avatar" />`;
        } else {
            avatarHtml = `<div style="width:36px; height:36px; border-radius:50%; background:var(--brand-primary); display:flex; align-items:center; justify-content:center; color:#fff;"><i class="fa-solid fa-bell"></i></div>`;
        }
        
        item.innerHTML = `
            <div style="display:flex; gap:12px; align-items:flex-start; width:100%;">
                <div style="flex-shrink:0; margin-top:3px;">
                    ${avatarHtml}
                </div>
                <div style="flex-grow:1; display:flex; flex-direction:column; overflow:hidden;">
                    <strong style="font-size: 0.9rem; color: var(--text-main); margin-bottom: 3px;">
                        ${n.okundu === 0 ? '<span style="color: var(--brand-primary); font-size: 1.2rem; line-height: 0.5;">•</span> ' : ''}${n.baslik}
                    </strong>
                    <span style="font-size: 0.8rem; color: var(--text-muted); line-height: 1.4; word-break: break-word;">${n.mesaj}</span>
                    <small style="font-size: 0.7rem; color: var(--brand-primary); margin-top: 5px;">
                        ${new Date(n.olusturulmaTarihi * 1000).toLocaleString('tr-TR')}
                    </small>
                </div>
            </div>
        `;
        return item;
    };

    const fetchNotifications = async () => {
        try {
            const formData = new FormData();
            if (document.querySelector('meta[name="csrf-token"]')) formData.append('csrf_token', document.querySelector('meta[name="csrf-token"]').content);
            formData.append('action', 'getUnread');
            const response = await fetch('/api/switch-case/notification', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            if (result.status === 'success') {
                const subsData = result.data.subscriptions || [];
                const specialData = result.data.special || [];
                const generalData = result.data.general || [];
                const systemData = result.data.system || [];
                
                unreadIds = [
                    ...subsData.filter(n => n.okundu === 0).map(n => n.bildirimId),
                    ...specialData.filter(n => n.okundu === 0).map(n => n.bildirimId),
                    ...generalData.filter(n => n.okundu === 0).map(n => n.bildirimId),
                    ...systemData.filter(n => n.okundu === 0).map(n => n.bildirimId)
                ];
                
                if (unreadIds.length > 0) {
                    notifBadge.innerText = '';
                    notifBadge.style.width = '10px';
                    notifBadge.style.height = '10px';
                    notifBadge.style.padding = '0';
                    notifBadge.style.minWidth = 'unset';
                    notifBadge.style.borderRadius = '50%';
                    notifBadge.style.top = '2px';
                    notifBadge.style.right = '4px';
                    notifBadge.style.display = 'block';
                    markReadBtn.style.display = 'block';
                } else {
                    notifBadge.style.display = 'none';
                    markReadBtn.style.display = 'none';
                }
                
                const renderTab = (pane, data, tabSelector) => {
                    pane.innerHTML = '';
                    
                    const unreadCount = data.filter(n => n.okundu === 0).length;
                    const tabBtn = document.querySelector(tabSelector);
                    if (tabBtn) {
                        const baseText = tabBtn.innerHTML.split(' <')[0].trim();
                        if (unreadCount > 0) {
                            tabBtn.innerHTML = `${baseText} <span class="notif-dot" style="display:inline-block; width:6px; height:6px; background:var(--brand-primary); border-radius:50%; margin-left:4px; vertical-align:middle; box-shadow: 0 0 5px var(--brand-primary);"></span>`;
                        } else {
                            tabBtn.innerHTML = baseText;
                        }
                    }

                    if (data.length > 0) {
                        data.forEach(n => pane.appendChild(createItemHTML(n)));
                    } else {
                        pane.innerHTML = '<div style="padding: 20px; text-align: center; color: var(--text-muted); font-size: 0.85rem;">Yeni bildiriminiz yok.</div>';
                    }
                };

                renderTab(notifSubscriptions, subsData, '[data-target="notif-subscriptions"]');
                renderTab(notifGeneral, generalData, '[data-target="notif-general"]');
                renderTab(notifSpecial, specialData, '[data-target="notif-special"]');
                renderTab(notifSystem, systemData, '[data-target="notif-system"]');
            }
        } catch (e) {
            console.error('Bildirimler alınamadı', e);
        }
    };

    const markAsRead = async (targetPaneId = null) => {
        if (unreadIds.length === 0) return;
        
        let pane = document;
        let idsToMark = unreadIds;
        
        if (targetPaneId !== 'all') {
            if (!targetPaneId) {
                const activeTab = document.querySelector('.notif-tab.active');
                if (activeTab) targetPaneId = activeTab.dataset.target;
            }
            if (targetPaneId) {
                pane = document.getElementById(targetPaneId);
                if (!pane) return;
                
                const unreadItems = Array.from(pane.querySelectorAll('.notif-item')).filter(item => 
                    item.style.background.includes('rgba') || item.style.background.includes('var')
                );
                idsToMark = unreadItems.map(item => item.dataset.id).filter(id => id);
            }
        }
        
        if (idsToMark.length === 0) return;
        
        try {
            const formData = new FormData();
            if (document.querySelector('meta[name="csrf-token"]')) formData.append('csrf_token', document.querySelector('meta[name="csrf-token"]').content);
            formData.append('action', 'markAsRead');
            idsToMark.forEach(id => formData.append('ids[]', id));
            
            await fetch('/api/switch-case/notification', {
                method: 'POST',
                body: formData
            });
            
            unreadIds = unreadIds.filter(id => !idsToMark.includes(id));
            
            if (unreadIds.length === 0) {
                notifBadge.style.display = 'none';
                if (markReadBtn) markReadBtn.style.display = 'none';
            }
            
            // Sadece hedeflenen bildirimlerin DOM stilini temizle
            idsToMark.forEach(id => {
                const item = document.querySelector(`.notif-item[data-id="${id}"]`);
                if (item) {
                    item.style.background = 'transparent';
                    item.style.borderLeft = '3px solid transparent';
                    item.style.opacity = '0.7';
                    
                    const titleEl = item.querySelector('strong');
                    if (titleEl && titleEl.innerHTML.includes('•')) {
                        titleEl.innerHTML = titleEl.innerHTML.replace(/<span.*?•<\/span>\s*/, '');
                    }
                }
            });
            
            // İlgili sekmelerin kırmızı noktasını temizle
            if (targetPaneId && targetPaneId !== 'all') {
                const tabBtn = document.querySelector(`.notif-tab[data-target="${targetPaneId}"]`);
                if (tabBtn) {
                    const dot = tabBtn.querySelector('.notif-dot');
                    if (dot) dot.remove();
                }
            } else if (targetPaneId === 'all') {
                document.querySelectorAll('.notif-tab .notif-dot').forEach(dot => dot.remove());
            }
            
        } catch (e) {
            console.error('Bildirimler işaretlenemedi', e);
        }
    };

    if (notifBtn && notifMenu) {
        fetchNotifications(); // Sayfa yüklendiğinde çek
        
        // Tab geçiş mantığı
        notifTabs.forEach(tab => {
            tab.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                notifTabs.forEach(t => {
                    t.classList.remove('active');
                    t.style.color = 'var(--text-muted)';
                    t.style.borderBottom = '2px solid transparent';
                });
                
                tab.classList.add('active');
                tab.style.color = 'var(--brand-primary)';
                tab.style.borderBottom = '2px solid var(--brand-primary)';
                
                document.querySelectorAll('.notif-content-pane').forEach(pane => {
                    pane.style.display = 'none';
                });
                document.getElementById(tab.dataset.target).style.display = 'flex';
                
                // Sekme değiştiğinde sadece o sekmeyi okundu yap
                if (unreadIds.length > 0) {
                    markAsRead(tab.dataset.target);
                }
            });
        });

        notifBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const isShowing = notifMenu.classList.contains('show');
            
            // Diğer menüyü kapat
            if (profileMenu && profileMenu.classList.contains('show')) {
                profileMenu.classList.remove('show');
            }
            
            notifMenu.classList.toggle('show');
            
            // Menü açıldığında okundu işaretle ve lazy-load resimleri yükle
            if (!isShowing) {
                document.querySelectorAll('.lazy-notif-avatar[data-src]').forEach(img => {
                    img.src = img.getAttribute('data-src');
                    img.removeAttribute('data-src');
                });
                
                if (unreadIds.length > 0) {
                    markAsRead();
                }
            }
        });
        
        document.addEventListener('click', (e) => {
            if (!notifBtn.contains(e.target) && !notifMenu.contains(e.target)) {
                notifMenu.classList.remove('show');
            }
        });
        
        if (markReadBtn) {
            markReadBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                markAsRead('all');
            });
        }
    }

    // WebSocket Real-Time Notifications
    if (window.WS_USER_ID && window.WS_TOKEN) {
        let wsProto = window.location.protocol === 'https:' ? 'wss://' : 'ws://';
        let wsUrl = wsProto + window.location.hostname + '/ws/';
        
        let ws = new WebSocket(wsUrl);
        
        // Bildirim sesini sayfaya ilk girişte R2'den çekip önbelleğe alıyoruz.
        let notifSound = new Audio('https://sinavortagim.artilingo.com/system/notification.mp3');
        notifSound.preload = 'auto';
        notifSound.load(); 
        
        // Tarayıcı otomatik çalma engelini aşmak (Audio Unlock) için:
        // Kullanıcının sayfadaki ilk etkileşiminde (tıklama vb.) sesi sessizce çalıp yetki alırız.
        const unlockAudio = () => {
            if (notifSound) {
                notifSound.muted = true;
                notifSound.play().then(() => {
                    notifSound.pause();
                    notifSound.currentTime = 0;
                    notifSound.muted = false;
                }).catch(e => {});
            }
            document.removeEventListener('click', unlockAudio);
            document.removeEventListener('keydown', unlockAudio);
        };
        document.addEventListener('click', unlockAudio);
        document.addEventListener('keydown', unlockAudio);
        
        ws.onopen = () => {
            ws.send(JSON.stringify({
                type: 'auth',
                userIdHex: window.WS_USER_ID,
                hash: window.WS_TOKEN
            }));
        };
        
        ws.onmessage = (event) => {
            try {
                let res = JSON.parse(event.data);
                if (res.type === 'new_notification') {
                    // 1. Sesi çal (Tarayıcı engelini aşmak için try-catch ile)
                    if (notifSound) {
                        notifSound.currentTime = 0;
                        notifSound.play().catch(e => { 
                            console.warn("Bildirim sesi otomatik oynatılamadı. Tarayıcı etkileşim istiyor olabilir:", e.message); 
                        });
                    }

                    // 2. Zile Parlama/Sallanma animasyonu ver
                    const bellIcon = document.querySelector('#notificationBtn i');
                    if (bellIcon) {
                        bellIcon.classList.remove('fa-regular');
                        bellIcon.classList.add('fa-solid', 'fa-shake');
                        bellIcon.style.color = 'var(--brand-primary)';
                        
                        setTimeout(() => {
                            bellIcon.classList.remove('fa-solid', 'fa-shake');
                            bellIcon.classList.add('fa-regular');
                            bellIcon.style.color = '';
                        }, 2000);
                    }
                    
                    // Sunucudan (PHP API) profil fotoğrafları ve tam bildirim ID'leri ile en güncel listeyi sessizce çek
                    if (typeof fetchNotifications === 'function') {
                        fetchNotifications();
                    }
                }
            } catch (e) {
                console.error('WS Error:', e);
            }
        };
    }
});