const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

/**
 * ArtiFrame Global JS
 * Kural: Event'ler (Click, Submit vb.) asla id veya class üzerinden bağlanmaz!
 * Tüm etkileşimli elemanlar `data-js` özniteliği üzerinden yönetilir.
 */

/* ---- Global Yardımcı Fonksiyonlar ---- */

/**
 * HTML özel karakterlerini escape et (XSS koruması).
 */
window.escHtml = function(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
};

/**
 * Toast bildirimi göster.
 */
window.showToast = function(msg, type = 'success') {
    const tc = document.querySelector('.toast-container');
    if (!tc) return;
    const t = document.createElement('div');
    t.className = `toast ${type}`;
    t.innerHTML = `<span class="toast-message">${window.escHtml(msg)}</span>`;
    tc.appendChild(t);
    requestAnimationFrame(() => t.classList.add('show'));
    setTimeout(() => { t.classList.remove('show'); setTimeout(() => t.remove(), 500); }, 3500);
};

/**
 * CSRF korumalı FormData POST isteği atar.
 * CSRF token'i window.SQ_CONFIG veya window.SD_CONFIG üzerinden alır.
 */
window.apiPost = async function(url, body) {
    const cfg = window.SQ_CONFIG || window.SD_CONFIG || {};
    const metaCsrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const csrfToken = cfg.csrfToken || metaCsrf || '';
    
    const fd = new FormData();
    fd.append('csrf_token', csrfToken);
    for (const [k, v] of Object.entries(body)) fd.append(k, v);
    const res = await fetch(url, { method: 'POST', body: fd });
    return res.json();
};

document.addEventListener('DOMContentLoaded', () => {
    // 1. Tema Değiştirme (Theme Toggle) Mantığı
    // Sayfanın herhangi bir yerinde <button data-js="theme-toggle"> tıklandığında çalışır.
    document.addEventListener('click', (e) => {
        const toggleBtn = e.target.closest('[data-js="theme-toggle"]');
        if (!toggleBtn) return;
        
        e.preventDefault();
        const html = document.documentElement; // <html> etiketi
        const currentMode = html.getAttribute('data-mode');
        const newMode = currentMode === 'light' ? 'dark' : 'light';
        
        html.setAttribute('data-mode', newMode);
        
        // Kullanıcının tercihini tarayıcıya kaydet
        localStorage.setItem('artiframe_theme_mode', newMode);
    });

    // Sayfa Yüklendiğinde: Kullanıcının daha önce seçtiği bir tema varsa uygula
    const savedMode = localStorage.getItem('artiframe_theme_mode');
    if (savedMode) {
        document.documentElement.setAttribute('data-mode', savedMode);
    }
});

/**
 * Global Toaster (Bildirim) Mekanizması
 * Kullanımı: window.showToast('İşlem başarılı!', 'success');
 * type: 'success', 'error', 'warning', 'info'
 */
window.showToast = function(message, type = 'success', duration = 3500) {
    // 1. Container kontrolü (Yoksa oluştur)
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }

    // 2. Toast elementi oluştur
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    
    // Mesaj içeriği
    const textNode = document.createElement('div');
    textNode.className = 'toast-message';
    textNode.textContent = message;
    
    toast.appendChild(textNode);
    container.appendChild(toast);

    // 3. Animasyonla ekranda göster
    requestAnimationFrame(() => {
        toast.classList.add('show');
    });

    // 4. Süre bitiminde kaldır
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 500); // Transition süresi kadar bekle
    }, duration);
};

/**
 * Global Image Viewer (Viewer.js Native Bind)
 * Soru duvarı, Soru detay ve Profil gibi tüm galeri kapsayıcılarına otomatik uygulanır.
 */
window.initImageViewer = function(container = document) {
    if (typeof Viewer !== "undefined") {
        container.querySelectorAll('.post-gallery, .sq-gorsel-grid, .sd-gorsel-grid').forEach(gallery => {
            if (gallery.dataset.viewerInit) return;
            gallery.dataset.viewerInit = '1';
            
            new Viewer(gallery, {
                toolbar: false,      // Alttaki zoom, rotate butonlarını gizle
                navbar: false,       // Alttaki küçük resimleri (thumbnails) gizle
                title: false,        // Resim ismini gizle
                tooltip: false,      // Zoom yüzdesini gizle
                movable: true,       // Sürüklemeye izin ver
                zoomable: true,      // Zoom'a izin ver
                rotatable: false,
                scalable: false,
                transition: true,    // CSS animasyonları ile pürüzsüz geçiş
                fullscreen: true,
                keyboard: true,
                
                // Güvenli Özel Oklar (Custom Desktop Arrows)
                ready() {
                    const viewerInstance = this.viewer;
                    // Sadece 1'den fazla görsel varsa okları ekle
                    if (viewerInstance.length > 1) {
                        const vContainer = viewerInstance.viewer; // Kütüphanenin oluşturduğu ana kapsayıcı
                        
                        // Geri Oku
                        const prevBtn = document.createElement('div');
                        prevBtn.className = 'custom-viewer-arrow prev';
                        prevBtn.innerHTML = '<i class="fa-solid fa-chevron-left"></i>';
                        prevBtn.onclick = (e) => { e.stopPropagation(); viewerInstance.prev(true); };
                        
                        // İleri Oku
                        const nextBtn = document.createElement('div');
                        nextBtn.className = 'custom-viewer-arrow next';
                        nextBtn.innerHTML = '<i class="fa-solid fa-chevron-right"></i>';
                        nextBtn.onclick = (e) => { e.stopPropagation(); viewerInstance.next(true); };
                        
                        // Kapsayıcıya enjekte et
                        vContainer.appendChild(prevBtn);
                        vContainer.appendChild(nextBtn);
                    }
                }
            });
        });
    }
};

document.addEventListener('DOMContentLoaded', () => {
    window.initImageViewer();

    // Raporlama Modalı Olayları
    const reportModal = document.getElementById('global-report-modal');
    const closeBtn = document.getElementById('close-global-report-modal');
    const reportForm = document.getElementById('global-report-form');
    const aciklamaInput = document.getElementById('global-report-aciklama');
    const aciklamaCount = document.getElementById('global-report-aciklama-count');

    if (reportModal) {
        closeBtn.addEventListener('click', () => {
            reportModal.classList.remove('show');
        });

        aciklamaInput.addEventListener('input', function() {
            aciklamaCount.textContent = this.value.length;
        });

        reportForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('submit-global-report-btn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Gönderiliyor...';
            btn.disabled = true;

            const formData = new FormData(this);
            formData.append('action', 'icerik_raporla');
            
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            if (csrfMeta && !formData.has('csrf_token')) formData.append('csrf_token', csrfMeta.content);

            fetch('/api/switch-case/akis', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    reportModal.classList.remove('show');
                    if (window.showToast) window.showToast(data.message, 'success');
                } else {
                    if (window.showToast) window.showToast(data.message || 'Bir hata oluştu.', 'error');
                }
            })
            .catch(err => {
                console.error("Rapor Hatası:", err);
                if (window.showToast) window.showToast('Sistemsel bir hata oluştu.', 'error');
            })
            .finally(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        });
    }
});

/**
 * Global İçerik Raporlama
 * 1: Soru, 2: Gönderi, 3: Soru Yorumu, 4: Gönderi Yorumu, 5: Çözüm
 */
window.reportContent = function(icerikTuru, icerikId) {
    const modal = document.getElementById('global-report-modal');
    if (!modal) {
        console.error("Raporlama modalı bulunamadı.");
        return;
    }
    
    // Formu sıfırla
    document.getElementById('global-report-form').reset();
    document.getElementById('global-report-aciklama-count').textContent = '0';
    
    // ID'leri set et
    document.getElementById('global-report-icerikTuru').value = icerikTuru;
    document.getElementById('global-report-icerikId').value = icerikId;
    
    // Modalı aç
    requestAnimationFrame(() => {
        modal.classList.add('show');
    });
};

/**
 * Global Confirm (Onay) Mekanizması
 * Kullanımı: window.showConfirm('Emin misiniz?', 'Açıklama', { confirmText: 'Sil', confirmClass: 'btn-danger', cancelText: 'Vazgeç' }).then(res => ...)
 */
window.showConfirm = function(title, message, options = {}) {
    return new Promise((resolve) => {
        const overlay = document.createElement('div');
        overlay.className = 'confirm-overlay';
        
        const modal = document.createElement('div');
        modal.className = 'confirm-modal';
        
        const titleEl = document.createElement('h3');
        titleEl.className = 'confirm-title';
        titleEl.textContent = title;
        
        const messageEl = document.createElement('p');
        messageEl.className = 'confirm-message';
        messageEl.textContent = message;
        
        const actionsEl = document.createElement('div');
        actionsEl.className = 'confirm-actions';
        
        const cancelBtn = document.createElement('button');
        cancelBtn.className = `btn ${options.cancelClass || 'btn-outline'}`;
        if (options.cancelStyle) cancelBtn.style.cssText = options.cancelStyle;
        cancelBtn.textContent = options.cancelText || 'İptal';
        
        const confirmBtn = document.createElement('button');
        confirmBtn.className = `btn ${options.confirmClass || 'btn-primary'}`;
        if (options.confirmStyle) confirmBtn.style.cssText = options.confirmStyle;
        confirmBtn.textContent = options.confirmText || 'Tamam';
        
        const close = (result) => {
            overlay.classList.remove('show');
            setTimeout(() => overlay.remove(), 300);
            resolve(result);
        };
        
        cancelBtn.addEventListener('click', () => close(false));
        confirmBtn.addEventListener('click', () => close(true));
        
        actionsEl.appendChild(cancelBtn);
        actionsEl.appendChild(confirmBtn);
        
        modal.appendChild(titleEl);
        modal.appendChild(messageEl);
        modal.appendChild(actionsEl);
        
        overlay.appendChild(modal);
        document.body.appendChild(overlay);
        
        requestAnimationFrame(() => {
            requestAnimationFrame(() => overlay.classList.add('show'));
        });
    });
};

/**
 * Global Password Prompt Mekanizması
 * Hassas işlemlerde şifre doğrulamak için kullanılır.
 */
window.showPasswordPrompt = function(title, message, options = {}) {
    return new Promise((resolve) => {
        const overlay = document.createElement('div');
        overlay.className = 'confirm-overlay';
        
        const modal = document.createElement('div');
        modal.className = 'confirm-modal';
        
        const titleEl = document.createElement('h3');
        titleEl.className = 'confirm-title';
        titleEl.textContent = title;
        
        const messageEl = document.createElement('p');
        messageEl.className = 'confirm-message';
        messageEl.textContent = message;

        const inputEl = document.createElement('input');
        inputEl.type = 'password';
        inputEl.className = 'confirm-prompt-input';
        inputEl.placeholder = 'Lütfen şifrenizi girin...';
        
        const actionsEl = document.createElement('div');
        actionsEl.className = 'confirm-actions';
        
        const cancelBtn = document.createElement('button');
        cancelBtn.className = `btn ${options.cancelClass || 'btn-outline'}`;
        cancelBtn.textContent = options.cancelText || 'İptal';
        
        const confirmBtn = document.createElement('button');
        confirmBtn.className = `btn ${options.confirmClass || 'btn-primary'}`;
        confirmBtn.textContent = options.confirmText || 'Doğrula';
        
        const close = (result) => {
            overlay.classList.remove('show');
            setTimeout(() => overlay.remove(), 300);
            resolve(result);
        };
        
        cancelBtn.addEventListener('click', () => close(null));
        confirmBtn.addEventListener('click', () => {
            if (!inputEl.value.trim()) {
                window.showToast('Uyarı', 'Lütfen şifrenizi girin!', 'warning');
                return;
            }
            close(inputEl.value);
        });

        // Enter tuşu desteği
        inputEl.addEventListener('keyup', (e) => {
            if (e.key === 'Enter') confirmBtn.click();
        });
        
        actionsEl.appendChild(cancelBtn);
        actionsEl.appendChild(confirmBtn);
        
        modal.appendChild(titleEl);
        modal.appendChild(messageEl);
        modal.appendChild(inputEl);
        modal.appendChild(actionsEl);
        
        overlay.appendChild(modal);
        document.body.appendChild(overlay);
        
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                overlay.classList.add('show');
                inputEl.focus();
            });
        });
    });
};

/* --- Global Comment Manager --- */
window.CommentManager = {
    modal: null,
    aktifSoruId: null,
    aktifUstYorumId: null,
    yorumlarYuklendi: false,

    init() {
        if (this.modal) return;
        const html = `
            <div class="cm-overlay" id="cmOverlay">
                <div class="cm-modal">
                    <div class="cm-header">
                        <h2>Yorumlar</h2>
                        <button class="cm-close" id="cmClose"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <div class="cm-body" id="cmBody">
                        <div class="sq-yorumlar-loading"><i class="fa-solid fa-spinner fa-spin"></i> Yükleniyor...</div>
                    </div>
                    <div class="cm-footer">
                        <div class="sq-yorum-input-wrap">
                            <div class="sq-reply-status" id="cmReplyStatus">
                                <span>Yanıtlanıyor: <strong id="cmReplyToUsername"></strong></span>
                                <button type="button" class="sq-cancel-reply" id="cmBtnCancelReply"><i class="fa-solid fa-xmark"></i> İptal</button>
                            </div>
                            <!-- Mention Autocomplete Dropdown -->
                            <div class="cm-mention-dropdown" id="cmMentionDropdown"></div>
                            <div class="sq-yorum-input-row">
                                <div class="sq-yorum-input cm-editor"
                                     id="cmYorumInput"
                                     contenteditable="true"
                                     data-placeholder="Yorum yaz..."
                                     role="textbox"
                                     aria-multiline="true"
                                     spellcheck="true"></div>
                                <button class="btn btn-primary btn-sm" id="cmBtnSendYorum">
                                    <i class="fa-solid fa-paper-plane"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', html);
        this.modal = document.getElementById('cmOverlay');
        
        document.getElementById('cmClose').addEventListener('click', () => this.close());
        this.modal.addEventListener('click', (e) => {
            if (e.target === this.modal) this.close();
        });

        document.getElementById('cmBtnCancelReply').addEventListener('click', () => {
            this.aktifUstYorumId = null;
            document.getElementById('cmReplyStatus').classList.remove('active');
            const editor = document.getElementById('cmYorumInput');
            // @mention tagini sil, sadece tag ise tümünü temizle
            const firstChild = editor.firstChild;
            if (firstChild && firstChild.classList && firstChild.classList.contains('cm-mention-tag')) {
                editor.innerHTML = '';
            }
            editor.dataset.placeholder = 'Yorum yaz...';
            this._closeMentionDropdown();
        });

        document.getElementById('cmBtnSendYorum').addEventListener('click', () => this.sendYorum());

        // Mention autocomplete - ana editör
        const editor = document.getElementById('cmYorumInput');
        const dd     = document.getElementById('cmMentionDropdown');
        this._attachMentionListeners(editor, dd);
        // Enter to send (Shift+Enter = newline)
        editor.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                if (this._currentDropdown?.children.length > 0 && this._currentDropdown?.style.display !== 'none') return;
                e.preventDefault();
                this.sendYorum();
            }
        });
    },

    // Modal açıkken sayfanın scroll pozisyonunu hatırla
    _scrollY: 0,
    _mentionQuery: null,
    _mentionSearchTimer: null,
    _currentEditor: null,
    _currentDropdown: null,

    open(soruId) {
        this.init();
        if (this.aktifSoruId !== soruId) {
            this.aktifSoruId = soruId;
            this.yorumlarYuklendi = false;
            document.getElementById('cmBody').innerHTML = '<div class="sq-yorumlar-loading"><i class="fa-solid fa-spinner fa-spin"></i> Yükleniyor...</div>';
            const editor = document.getElementById('cmYorumInput');
            if (editor) editor.innerHTML = '';
            document.getElementById('cmBtnCancelReply')?.click();
        }
        
        this.modal.style.display = 'flex';
        // Force reflow for animation
        void this.modal.offsetWidth;
        this.modal.classList.add('show');

        // iOS Safari dahil tüm mobillerde arka planı kilitle
        this._scrollY = window.scrollY;
        document.body.style.position = 'fixed';
        document.body.style.top = `-${this._scrollY}px`;
        document.body.style.width = '100%';
        document.body.style.overflow = 'hidden';
        
        if (!this.yorumlarYuklendi) {
            this.loadYorumlar();
        }
    },

    close() {
        if (!this.modal) return;
        this.modal.classList.remove('show');

        // Body kilidini aç ve scroll pozisyonunu geri yükle
        document.body.style.position = '';
        document.body.style.top = '';
        document.body.style.width = '';
        document.body.style.overflow = '';
        window.scrollTo(0, this._scrollY);

        setTimeout(() => {
            this.modal.style.display = 'none';
        }, 300);
    },

    async loadYorumlar() {
        const body = document.getElementById('cmBody');
        try {
            const res = await window.apiPost('/api/switch-case/yorumSoru', {
                action: 'getList',
                soruId: this.aktifSoruId,
                limit: 100
            });

            if (res.status === 'success') {
                this.yorumlarYuklendi = true;
                const yorumlar = res.data;
                if (yorumlar.length === 0) {
                    body.innerHTML = '<div class="sq-yorumlar-loading">İlk yorumu sen yaz!</div>';
                    return;
                }

                const parents = yorumlar.filter(y => !y.ustYorumId);
                const children = yorumlar.filter(y => y.ustYorumId);
                
                let html = '<div class="sq-yorumlar-list">';
                for (const p of parents) {
                    html += this.renderYorum(p, false);
                    const replies = children.filter(c => c.ustYorumId === p.yorumId);
                    if (replies.length > 0) {
                        html += `<button class="sq-toggle-replies" data-target="replies-${p.yorumId}">Yanıtları gör (${replies.length})</button>`;
                        html += `<div class="sq-replies-container" id="replies-${p.yorumId}">`;
                        for (const r of replies) {
                            html += this.renderYorum(r, true);
                        }
                        html += `</div>`;
                    }
                }
                html += '</div>';
                body.innerHTML = html;
                this.attachEvents();
            } else {
                body.innerHTML = '<div class="sq-yorumlar-loading" style="color:red">Yorumlar yüklenemedi.</div>';
            }
        } catch (e) {
            body.innerHTML = '<div class="sq-yorumlar-loading" style="color:red">Bağlantı hatası.</div>';
        }
    },

    getRoleLabel(rol) {
        if (rol == 0) return '<i class="fa-solid fa-shield-halved"></i> Moderatör';
        if (rol == 1) return '<i class="fa-solid fa-user"></i> Aday';
        if (rol == 2) return '<i class="fa-solid fa-chalkboard-user"></i> Öğretmen';
        if (rol == 3) return '<i class="fa-solid fa-book"></i> Yayınevi';
        if (rol == 4) return '<i class="fa-solid fa-graduation-cap"></i> Mentor';
        return '';
    },

    timeAgoJs(unixTS) {
        const diff = Math.floor(Date.now() / 1000) - unixTS;
        if (diff < 60) return 'Az önce';
        if (diff < 3600) return Math.floor(diff / 60) + 'd önce';
        if (diff < 86400) return Math.floor(diff / 3600) + 's önce';
        if (diff < 604800) return Math.floor(diff / 86400) + 'g önce';
        
        const d = new Date(unixTS * 1000);
        return d.toLocaleDateString('tr-TR', { day: '2-digit', month: '2-digit', year: 'numeric' });
    },

    renderYorum(y, isReply = false) {
        const avatar = y.profilFotografi 
            ? (y.profilFotografi.startsWith('http') ? y.profilFotografi : '/uploads/profiles/' + y.profilFotografi)
            : `https://sinavortagim.artilingo.com/system/default-avatar.png`;

        return `
            <div class="sq-yorum-item ${isReply ? 'sq-yorum-reply' : ''}" id="yorum-${y.yorumId}">
                <a href="/user/profil/${window.escHtml(y.kullaniciAdi)}">
                    ${y.rol == 0 ? `
                        <div class="is-mod-avatar" style="width: 35px; height: 35px; margin-right: 0.5rem;">
                            <img src="${window.escHtml(avatar)}" class="sq-avatar" alt="Avatar" style="margin: 0; width:100%; height:100%;">
                        </div>
                    ` : `
                        <img src="${window.escHtml(avatar)}" class="sq-avatar" alt="Avatar">
                    `}
                </a>
                <div class="sq-yorum-bubble">
                    <div class="sq-yorum-header">
                        <a href="/user/profil/${window.escHtml(y.kullaniciAdi)}" style="text-decoration: none; color: inherit;">
                            <strong class="${y.rol == 0 ? 'is-mod-text' : ''}">${y.adSoyad ? window.escHtml(y.adSoyad) : '@' + window.escHtml(y.kullaniciAdi)}</strong>
                        </a>
                        ${y.rol == 0 ? `<span class="badge-mod" style="font-size: 0.75rem; color: #a855f7; margin-left: 5px;"><i class="fa-solid fa-shield-halved"></i> Moderatör</span>` : ''}
                        ${!y.adSoyad ? '' : `<span>@${window.escHtml(y.kullaniciAdi)}</span>`}
                        ${y.rol != 0 ? `<span class="sq-yorum-role">${this.getRoleLabel(y.rol)}</span>` : ''}
                        <span>${this.timeAgoJs(y.olusturulmaTarihi)}</span>
                    </div>
                    <div class="sq-yorum-metin">${this._renderMentions(y.yorumMetni).replace(/\n/g, '<br>')}</div>
                    <div class="sq-yorum-footer">
                        ${!isReply ? `<button class="sq-yorum-action btn-reply" data-id="${y.yorumId}" data-user="@${window.escHtml(y.kullaniciAdi)}"><i class="fa-solid fa-reply"></i> Yanıtla</button>` : ''}
                        ${y.canEdit ? `<button class="sq-yorum-action edit btn-edit-yorum" data-id="${y.yorumId}" data-metin="${window.escHtml(y.yorumMetni)}"><i class="fa-solid fa-pen"></i> Düzenle</button>` : ''}
                        ${y.canDelete ? `<button class="sq-yorum-action delete btn-delete-yorum" data-id="${y.yorumId}"><i class="fa-solid fa-trash"></i> Sil</button>` : ''}
                        ${!y.canEdit ? `<button class="sq-yorum-action text-danger" onclick="reportContent(3, '${y.yorumId}')"><i class="fa-solid fa-flag"></i> Raporla</button>` : ''}
                    </div>
                </div>
            </div>
        `;
    },

    attachEvents() {
        const _this = this;
        document.querySelectorAll('.btn-reply').forEach(btn => {
            btn.addEventListener('click', function() {
                const replyTo = this.dataset.user; // '@kullaniciAdi'
                _this.aktifUstYorumId = this.dataset.id;
                document.getElementById('cmReplyToUsername').textContent = replyTo;
                document.getElementById('cmReplyStatus').classList.add('active');
                
                // Editor'a @mention tag'i ekle
                const editor = document.getElementById('cmYorumInput');
                editor.innerHTML = '';
                const tag = _this._createMentionTag(replyTo.replace('@', ''));
                editor.appendChild(tag);
                // tag sonrasına boflukluk ekle ve kursörü yerleştir
                const space = document.createTextNode('\u00a0');
                editor.appendChild(space);
                _this._placeCursorAfter(space);
                editor.dataset.placeholder = 'Yanıtını yaz...';
                editor.focus();
            });
        });

        document.querySelectorAll('.sq-toggle-replies').forEach(btn => {
            btn.addEventListener('click', function() {
                const targetId = this.dataset.target;
                const container = document.getElementById(targetId);
                if (container.classList.contains('open')) {
                    container.classList.remove('open');
                    this.textContent = `Yanıtları gör (${container.children.length})`;
                } else {
                    container.classList.add('open');
                    this.textContent = `Yanıtları gizle`;
                }
            });
        });

        document.querySelectorAll('.btn-delete-yorum').forEach(btn => {
            btn.addEventListener('click', function() {
                window.showConfirm('Yorumu Sil', 'Bu yorumu silmek istediğine emin misin?', { confirmClass: 'btn-danger', confirmText: 'Sil' })
                    .then(async (confirmed) => {
                        if (!confirmed) return;
                        
                        const id = this.dataset.id;
                        const res = await window.apiPost('/api/switch-case/yorumSoru', {
                            action: 'delete',
                            soruId: _this.aktifSoruId,
                            yorumId: id
                        });
                        if (res.status === 'success') {
                            window.showToast('Yorum silindi.');
                            _this.updateYorumCountUI(res.data?.yeniYorumSayisi);
                            _this.loadYorumlar();
                        } else {
                            window.showToast(res.message, 'error');
                        }
                    });
            });
        });

        document.querySelectorAll('.btn-edit-yorum').forEach(btn => {
            btn.addEventListener('click', function() {
                const yorumId   = this.dataset.id;
                const mevcutMetin = this.dataset.metin;
                const bubble    = this.closest('.sq-yorum-bubble');
                const metinEl   = bubble.querySelector('.sq-yorum-metin');
                const footerEl  = bubble.querySelector('.sq-yorum-footer');

                // Zaten düzenleme modunda ise çık
                if (bubble.querySelector('.sq-edit-area')) return;

                // Inline edit alanı oluştur
                const editArea = document.createElement('div');
                editArea.className = 'sq-edit-area';
                editArea.innerHTML = `
                    <div class="cm-mention-dropdown sq-edit-mention-dropdown"></div>
                    <div class="sq-edit-editor cm-editor sq-edit-textarea"
                         contenteditable="true"
                         data-placeholder="Yorumu düzenle..."
                         role="textbox"
                         aria-multiline="true"
                         spellcheck="true"></div>
                    <div class="sq-edit-actions">
                        <button class="sq-yorum-action edit sq-edit-save"><i class="fa-solid fa-check"></i> Kaydet</button>
                        <button class="sq-yorum-action sq-edit-cancel"><i class="fa-solid fa-xmark"></i> İptal</button>
                    </div>
                `;

                metinEl.style.display = 'none';
                footerEl.style.display = 'none';
                bubble.appendChild(editArea);

                const editEditorEl  = editArea.querySelector('.sq-edit-editor');
                const editDropdownEl = editArea.querySelector('.sq-edit-mention-dropdown');

                // Mevcut metni editöre yükle
                editEditorEl.textContent = mevcutMetin;
                _this._attachMentionListeners(editEditorEl, editDropdownEl);
                editEditorEl.focus();
                // Kursörü sona taşı
                const selRange = document.createRange();
                selRange.selectNodeContents(editEditorEl);
                selRange.collapse(false);
                window.getSelection().removeAllRanges();
                window.getSelection().addRange(selRange);

                // İptal
                editArea.querySelector('.sq-edit-cancel').addEventListener('click', () => {
                    editArea.remove();
                    metinEl.style.display = '';
                    footerEl.style.display = '';
                    // Ana editöre geri dön
                    const mainEditor = document.getElementById('cmYorumInput');
                    const mainDd     = document.getElementById('cmMentionDropdown');
                    if (mainEditor) { _this._currentEditor = mainEditor; _this._currentDropdown = mainDd; }
                });

                // Kaydet
                editArea.querySelector('.sq-edit-save').addEventListener('click', async () => {
                    const yeniMetin = _this._getEditorText(editEditorEl).trim();
                    if (!yeniMetin) return;

                    const saveBtn = editArea.querySelector('.sq-edit-save');
                    saveBtn.disabled = true;
                    saveBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

                    const res = await window.apiPost('/api/switch-case/yorumSoru', {
                        action: 'update',
                        yorumId: yorumId,
                        yorumMetni: yeniMetin
                    });

                    if (res.status === 'success') {
                        // DOM'u anlık güncelle
                        metinEl.innerHTML = _this._renderMentions(res.data.yeniMetin).replace(/\n/g, '<br>');
                        btn.dataset.metin = res.data.yeniMetin;
                        editArea.remove();
                        metinEl.style.display = '';
                        footerEl.style.display = '';
                        // Ana editöre geri dön
                        const mainEditor = document.getElementById('cmYorumInput');
                        const mainDd     = document.getElementById('cmMentionDropdown');
                        if (mainEditor) { _this._currentEditor = mainEditor; _this._currentDropdown = mainDd; }
                        window.showToast('Yorum güncellendi.');
                    } else {
                        window.showToast(res.message || 'Güncellenemedi.', 'error');
                        saveBtn.disabled = false;
                        saveBtn.innerHTML = '<i class="fa-solid fa-check"></i> Kaydet';
                    }
                });
            });
        });
    },

    updateYorumCountUI(count) {
        if (count !== undefined) {
            // Update on details page if available
            document.querySelectorAll('.sq-yorum-count, .sd-action-btn[data-soru-id="'+this.aktifSoruId+'"] span').forEach(el => {
                el.textContent = count;
            });
        }
    },

    async sendYorum() {
        const editor = document.getElementById('cmYorumInput');
        const btn    = document.getElementById('cmBtnSendYorum');
        const metin  = this._getEditorText(editor).trim();
        if (!metin) return;
        
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

        const res = await window.apiPost('/api/switch-case/yorumSoru', {
            action: 'create',
            soruId: this.aktifSoruId,
            yorumMetni: metin,
            ustYorumId: this.aktifUstYorumId || ''
        });

        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i>';

        if (res.status === 'success') {
            document.getElementById('cmBtnCancelReply').click();
            editor.innerHTML = '';
            this.updateYorumCountUI(res.data?.yeniYorumSayisi);
            this.loadYorumlar();
            setTimeout(() => {
                const body = document.getElementById('cmBody');
                body.scrollTop = body.scrollHeight;
            }, 300);
        } else {
            window.showToast(res.message || 'Gönderilemedi', 'error');
        }
    },

    /* ---- Mention (Etiket) Sistemi ---- */

    /** Editor'daki tüm metni (mention tag'leri dahil) düz string olarak al */
    _getEditorText(editor) {
        let text = '';
        editor.childNodes.forEach(node => {
            if (node.nodeType === Node.TEXT_NODE) {
                text += node.textContent;
            } else if (node.classList && node.classList.contains('cm-mention-tag')) {
                text += '@' + node.dataset.username;
            } else {
                text += node.textContent;
            }
        });
        return text;
    },

    /** Yorum metnindeki @kullaniciAdi'lerini mor tıklanabilir linke dönüştür */
    _renderMentions(text) {
        return window.escHtml(text)
            .replace(/@([a-zA-Z0-9_\.]{1,30})/g,
                '<a href="/user/profil/$1" class="cm-mention-link">@$1</a>');
    },

    /** Mention tag span'i oluştur */
    _createMentionTag(username) {
        const span = document.createElement('span');
        span.className = 'cm-mention-tag';
        span.contentEditable = 'false';
        span.dataset.username = username;
        span.textContent = '@' + username;
        return span;
    },

    /** Kursörü bir node sonrasına taşı */
    _placeCursorAfter(node) {
        const sel = window.getSelection();
        const range = document.createRange();
        range.setStartAfter(node);
        range.collapse(true);
        sel.removeAllRanges();
        sel.addRange(range);
    },

    _attachMentionListeners(editorEl, dropdownEl) {
        editorEl.addEventListener('focus', () => {
            this._currentEditor  = editorEl;
            this._currentDropdown = dropdownEl;
        });
        editorEl.addEventListener('input', () => this._onEditorInput());
        editorEl.addEventListener('keydown', (e) => this._onEditorKeydown(e));
        // İlk bağlamada hemen aktif yap
        this._currentEditor  = editorEl;
        this._currentDropdown = dropdownEl;
    },

    _closeMentionDropdown() {
        const dd = this._currentDropdown;
        if (dd) { dd.innerHTML = ''; dd.style.display = 'none'; }
        this._mentionQuery = null;
    },

    _onEditorInput() {
        const sel = window.getSelection();
        if (!sel.rangeCount) return;

        // Kursörün solundaki metni al
        const range = sel.getRangeAt(0);
        const textBefore = range.startContainer.textContent?.slice(0, range.startOffset) || '';

        // Son @... patternını ara
        const match = textBefore.match(/@([a-zA-Z0-9_\.]*)$/);
        if (match && match[1].length >= 3) {
            const query = match[1];
            if (query !== this._mentionQuery) {
                this._mentionQuery = query;
                clearTimeout(this._mentionSearchTimer);
                this._mentionSearchTimer = setTimeout(() => this._fetchMentionSuggestions(query), 300);
            }
        } else {
            this._closeMentionDropdown();
        }
    },

    _onEditorKeydown(e) {
        const dd = document.getElementById('cmMentionDropdown');
        const items = dd?.querySelectorAll('.cm-mention-item');
        if (!items || items.length === 0 || dd.style.display === 'none') return;

        const active = dd.querySelector('.cm-mention-item.active');
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            const next = active ? active.nextElementSibling : items[0];
            active?.classList.remove('active');
            next?.classList.add('active');
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            const prev = active ? active.previousElementSibling : items[items.length - 1];
            active?.classList.remove('active');
            prev?.classList.add('active');
        } else if (e.key === 'Enter' || e.key === 'Tab') {
            if (active) {
                e.preventDefault();
                this._insertMention(active.dataset.username);
            }
        } else if (e.key === 'Escape') {
            this._closeMentionDropdown();
        }
    },

    async _fetchMentionSuggestions(query) {
        try {
            const res = await window.apiPost('/api/switch-case/search', {
                action: 'search',
                keyword: query
            });
            if (res.status === 'success') {
                this._showMentionDropdown(res.data.slice(0, 5));
            }
        } catch (e) { /* sessiz hata */ }
    },

    _showMentionDropdown(users) {
        const dd = this._currentDropdown;
        if (!users || users.length === 0) { this._closeMentionDropdown(); return; }

        dd.innerHTML = users.map(u => {
            const avatar = u.profilFotografi
                ? (u.profilFotografi.startsWith('http') ? u.profilFotografi : '/uploads/profiles/' + u.profilFotografi)
                : `https://sinavortagim.artilingo.com/system/default-avatar.png`;
            return `
                <div class="cm-mention-item" data-username="${window.escHtml(u.kullaniciAdi)}">
                    <img src="${window.escHtml(avatar)}" alt="">
                    <div class="cm-mention-info">
                        <strong>${window.escHtml(u.adSoyad || u.kullaniciAdi)}</strong>
                        <span>@${window.escHtml(u.kullaniciAdi)}</span>
                    </div>
                </div>`;
        }).join('');
        dd.style.display = 'block';

        dd.querySelectorAll('.cm-mention-item').forEach(item => {
            item.addEventListener('mousedown', (e) => {
                e.preventDefault();
                this._insertMention(item.dataset.username);
            });
        });
    },

    _insertMention(username) {
        const editor = this._currentEditor;
        const sel = window.getSelection();
        if (!sel.rangeCount || !editor) return;

        const range = sel.getRangeAt(0);
        const node  = range.startContainer;
        const text  = node.textContent || '';
        const offset = range.startOffset;

        // @ ile başlayan kısmı sil
        const atIdx = text.lastIndexOf('@', offset - 1);
        if (atIdx !== -1) {
            node.textContent = text.slice(0, atIdx) + text.slice(offset);
            range.setStart(node, atIdx);
            range.collapse(true);
        }

        // Mention span ekle
        const tag = this._createMentionTag(username);
        range.insertNode(tag);

        // Tag sonrasına boşluk ekle ve kursörü oraya taşı
        const space = document.createTextNode('\u00a0');
        tag.parentNode.insertBefore(space, tag.nextSibling);
        this._placeCursorAfter(space);

        this._closeMentionDropdown();
        editor.focus();
    },

};

/* --- Global Gonderi Comment Manager --- */
window.GonderiCommentManager = {
    modal: null,
    aktifGonderiId: null,
    aktifUstYorumId: null,
    yorumlarYuklendi: false,

    init() {
        if (this.modal) return;
        const html = `
            <div class="cm-overlay" id="gcmOverlay">
                <div class="cm-modal">
                    <div class="cm-header">
                        <h2>Yorumlar</h2>
                        <button class="cm-close" id="gcmClose"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <div class="cm-body" id="gcmBody">
                        <div class="sq-yorumlar-loading"><i class="fa-solid fa-spinner fa-spin"></i> Yükleniyor...</div>
                    </div>
                    <div class="cm-footer">
                        <div class="sq-yorum-input-wrap">
                            <div class="sq-reply-status" id="gcmReplyStatus">
                                <span>Yanıtlanıyor: <strong id="gcmReplyToUsername"></strong></span>
                                <button type="button" class="sq-cancel-reply" id="gcmBtnCancelReply"><i class="fa-solid fa-xmark"></i> İptal</button>
                            </div>
                            <!-- Mention Autocomplete Dropdown -->
                            <div class="cm-mention-dropdown" id="gcmMentionDropdown"></div>
                            <div class="sq-yorum-input-row">
                                <div class="sq-yorum-input cm-editor"
                                     id="gcmYorumInput"
                                     contenteditable="true"
                                     data-placeholder="Yorum yaz..."
                                     role="textbox"
                                     aria-multiline="true"
                                     spellcheck="true"></div>
                                <button class="btn btn-primary btn-sm" id="cmBtnSendYorum">
                                    <i class="fa-solid fa-paper-plane"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', html);
        this.modal = document.getElementById('gcmOverlay');
        
        document.getElementById('gcmClose').addEventListener('click', () => this.close());
        this.modal.addEventListener('click', (e) => {
            if (e.target === this.modal) this.close();
        });

        document.getElementById('gcmBtnCancelReply').addEventListener('click', () => {
            this.aktifUstYorumId = null;
            document.getElementById('gcmReplyStatus').classList.remove('active');
            const editor = document.getElementById('gcmYorumInput');
            // @mention tagini sil, sadece tag ise tümünü temizle
            const firstChild = editor.firstChild;
            if (firstChild && firstChild.classList && firstChild.classList.contains('cm-mention-tag')) {
                editor.innerHTML = '';
            }
            editor.dataset.placeholder = 'Yorum yaz...';
            this._closeMentionDropdown();
        });

        document.getElementById('cmBtnSendYorum').addEventListener('click', () => this.sendYorum());

        // Mention autocomplete - ana editör
        const editor = document.getElementById('gcmYorumInput');
        const dd     = document.getElementById('gcmMentionDropdown');
        this._attachMentionListeners(editor, dd);
        // Enter to send (Shift+Enter = newline)
        editor.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                if (this._currentDropdown?.children.length > 0 && this._currentDropdown?.style.display !== 'none') return;
                e.preventDefault();
                this.sendYorum();
            }
        });
    },

    // Modal açıkken sayfanın scroll pozisyonunu hatırla
    _scrollY: 0,
    _mentionQuery: null,
    _mentionSearchTimer: null,
    _currentEditor: null,
    _currentDropdown: null,

    open(gonderiId) {
        this.init();
        if (this.aktifGonderiId !== gonderiId) {
            this.aktifGonderiId = gonderiId;
            this.yorumlarYuklendi = false;
            document.getElementById('gcmBody').innerHTML = '<div class="sq-yorumlar-loading"><i class="fa-solid fa-spinner fa-spin"></i> Yükleniyor...</div>';
            const editor = document.getElementById('gcmYorumInput');
            if (editor) editor.innerHTML = '';
            document.getElementById('gcmBtnCancelReply')?.click();
        }
        
        this.modal.style.display = 'flex';
        // Force reflow for animation
        void this.modal.offsetWidth;
        this.modal.classList.add('show');

        // iOS Safari dahil tüm mobillerde arka planı kilitle
        this._scrollY = window.scrollY;
        document.body.style.position = 'fixed';
        document.body.style.top = `-${this._scrollY}px`;
        document.body.style.width = '100%';
        document.body.style.overflow = 'hidden';
        
        if (!this.yorumlarYuklendi) {
            this.loadYorumlar();
        }
    },

    close() {
        if (!this.modal) return;
        this.modal.classList.remove('show');

        // Body kilidini aç ve scroll pozisyonunu geri yükle
        document.body.style.position = '';
        document.body.style.top = '';
        document.body.style.width = '';
        document.body.style.overflow = '';
        window.scrollTo(0, this._scrollY);

        setTimeout(() => {
            this.modal.style.display = 'none';
        }, 300);
    },

    async loadYorumlar() {
        const body = document.getElementById('gcmBody');
        try {
            const res = await window.apiPost('/api/switch-case/yorumGonderi', {
                action: 'getList',
                gonderiId: this.aktifGonderiId,
                limit: 100
            });

            if (res.status === 'success') {
                this.yorumlarYuklendi = true;
                const yorumlar = res.data;
                if (yorumlar.length === 0) {
                    body.innerHTML = '<div class="sq-yorumlar-loading">İlk yorumu sen yaz!</div>';
                    return;
                }

                const parents = yorumlar.filter(y => !y.ustYorumId);
                const children = yorumlar.filter(y => y.ustYorumId);
                
                let html = '<div class="sq-yorumlar-list">';
                for (const p of parents) {
                    html += this.renderYorum(p, false);
                    const replies = children.filter(c => c.ustYorumId === p.yorumId);
                    if (replies.length > 0) {
                        html += `<button class="sq-toggle-replies" data-target="replies-${p.yorumId}">Yanıtları gör (${replies.length})</button>`;
                        html += `<div class="sq-replies-container" id="replies-${p.yorumId}">`;
                        for (const r of replies) {
                            html += this.renderYorum(r, true);
                        }
                        html += `</div>`;
                    }
                }
                html += '</div>';
                body.innerHTML = html;
                this.attachEvents();
            } else {
                body.innerHTML = '<div class="sq-yorumlar-loading" style="color:red">Yorumlar yüklenemedi.</div>';
            }
        } catch (e) {
            body.innerHTML = '<div class="sq-yorumlar-loading" style="color:red">Bağlantı hatası.</div>';
        }
    },

    getRoleLabel(rol) {
        if (rol == 0) return '<i class="fa-solid fa-shield-halved"></i> Moderatör';
        if (rol == 1) return '<i class="fa-solid fa-user"></i> Aday';
        if (rol == 2) return '<i class="fa-solid fa-chalkboard-user"></i> Öğretmen';
        if (rol == 3) return '<i class="fa-solid fa-book"></i> Yayınevi';
        if (rol == 4) return '<i class="fa-solid fa-graduation-cap"></i> Mentor';
        return '';
    },

    timeAgoJs(unixTS) {
        const diff = Math.floor(Date.now() / 1000) - unixTS;
        if (diff < 60) return 'Az önce';
        if (diff < 3600) return Math.floor(diff / 60) + 'd önce';
        if (diff < 86400) return Math.floor(diff / 3600) + 's önce';
        if (diff < 604800) return Math.floor(diff / 86400) + 'g önce';
        
        const d = new Date(unixTS * 1000);
        return d.toLocaleDateString('tr-TR', { day: '2-digit', month: '2-digit', year: 'numeric' });
    },

    renderYorum(y, isReply = false) {
        const avatar = y.profilFotografi 
            ? (y.profilFotografi.startsWith('http') ? y.profilFotografi : '/uploads/profiles/' + y.profilFotografi)
            : `https://sinavortagim.artilingo.com/system/default-avatar.png`;

        return `
            <div class="sq-yorum-item ${isReply ? 'sq-yorum-reply' : ''}" id="yorum-${y.yorumId}">
                <a href="/user/profil/${window.escHtml(y.kullaniciAdi)}">
                    ${y.rol == 0 ? `
                        <div class="is-mod-avatar" style="width: 35px; height: 35px; margin-right: 0.5rem;">
                            <img src="${window.escHtml(avatar)}" class="sq-avatar" alt="Avatar" style="margin: 0; width:100%; height:100%;">
                        </div>
                    ` : `
                        <img src="${window.escHtml(avatar)}" class="sq-avatar" alt="Avatar">
                    `}
                </a>
                <div class="sq-yorum-bubble">
                    <div class="sq-yorum-header">
                        <a href="/user/profil/${window.escHtml(y.kullaniciAdi)}" style="text-decoration: none; color: inherit;">
                            <strong class="${y.rol == 0 ? 'is-mod-text' : ''}">${y.adSoyad ? window.escHtml(y.adSoyad) : '@' + window.escHtml(y.kullaniciAdi)}</strong>
                        </a>
                        ${y.rol == 0 ? `<span class="badge-mod" style="font-size: 0.75rem; color: #a855f7; margin-left: 5px;"><i class="fa-solid fa-shield-halved"></i> Moderatör</span>` : ''}
                        ${!y.adSoyad ? '' : `<span>@${window.escHtml(y.kullaniciAdi)}</span>`}
                        ${y.rol != 0 ? `<span class="sq-yorum-role">${this.getRoleLabel(y.rol)}</span>` : ''}
                        <span>${this.timeAgoJs(y.olusturulmaTarihi)}</span>
                    </div>
                    <div class="sq-yorum-metin">${this._renderMentions(y.yorumMetni).replace(/\n/g, '<br>')}</div>
                    <div class="sq-yorum-footer">
                        ${!isReply ? `<button class="sq-yorum-action btn-reply" data-id="${y.yorumId}" data-user="@${window.escHtml(y.kullaniciAdi)}"><i class="fa-solid fa-reply"></i> Yanıtla</button>` : ''}
                        ${y.canEdit ? `<button class="sq-yorum-action edit btn-edit-yorum" data-id="${y.yorumId}" data-metin="${window.escHtml(y.yorumMetni)}"><i class="fa-solid fa-pen"></i> Düzenle</button>` : ''}
                        ${y.canDelete ? `<button class="sq-yorum-action delete btn-delete-yorum" data-id="${y.yorumId}"><i class="fa-solid fa-trash"></i> Sil</button>` : ''}
                        ${!y.canEdit ? `<button class="sq-yorum-action text-danger" onclick="reportContent(4, '${y.yorumId}')"><i class="fa-solid fa-flag"></i> Raporla</button>` : ''}
                    </div>
                </div>
            </div>
        `;
    },

    attachEvents() {
        const _this = this;
        document.querySelectorAll('.btn-reply').forEach(btn => {
            btn.addEventListener('click', function() {
                const replyTo = this.dataset.user; // '@kullaniciAdi'
                _this.aktifUstYorumId = this.dataset.id;
                document.getElementById('gcmReplyToUsername').textContent = replyTo;
                document.getElementById('gcmReplyStatus').classList.add('active');
                
                // Editor'a @mention tag'i ekle
                const editor = document.getElementById('gcmYorumInput');
                editor.innerHTML = '';
                const tag = _this._createMentionTag(replyTo.replace('@', ''));
                editor.appendChild(tag);
                // tag sonrasına boflukluk ekle ve kursörü yerleştir
                const space = document.createTextNode('\u00a0');
                editor.appendChild(space);
                _this._placeCursorAfter(space);
                editor.dataset.placeholder = 'Yanıtını yaz...';
                editor.focus();
            });
        });

        document.querySelectorAll('.sq-toggle-replies').forEach(btn => {
            btn.addEventListener('click', function() {
                const targetId = this.dataset.target;
                const container = document.getElementById(targetId);
                if (container.classList.contains('open')) {
                    container.classList.remove('open');
                    this.textContent = `Yanıtları gör (${container.children.length})`;
                } else {
                    container.classList.add('open');
                    this.textContent = `Yanıtları gizle`;
                }
            });
        });

        document.querySelectorAll('.btn-delete-yorum').forEach(btn => {
            btn.addEventListener('click', function() {
                window.showConfirm('Yorumu Sil', 'Bu yorumu silmek istediğine emin misin?', { confirmClass: 'btn-danger', confirmText: 'Sil' })
                    .then(async (confirmed) => {
                        if (!confirmed) return;
                        
                        const id = this.dataset.id;
                        const res = await window.apiPost('/api/switch-case/yorumGonderi', {
                            action: 'delete',
                            gonderiId: _this.aktifGonderiId,
                            yorumId: id
                        });
                        if (res.status === 'success') {
                            window.showToast('Yorum silindi.');
                            _this.updateYorumCountUI(res.data?.yeniYorumSayisi);
                            _this.loadYorumlar();
                        } else {
                            window.showToast(res.message, 'error');
                        }
                    });
            });
        });

        document.querySelectorAll('.btn-edit-yorum').forEach(btn => {
            btn.addEventListener('click', function() {
                const yorumId   = this.dataset.id;
                const mevcutMetin = this.dataset.metin;
                const bubble    = this.closest('.sq-yorum-bubble');
                const metinEl   = bubble.querySelector('.sq-yorum-metin');
                const footerEl  = bubble.querySelector('.sq-yorum-footer');

                // Zaten düzenleme modunda ise çık
                if (bubble.querySelector('.sq-edit-area')) return;

                // Inline edit alanı oluştur
                const editArea = document.createElement('div');
                editArea.className = 'sq-edit-area';
                editArea.innerHTML = `
                    <div class="cm-mention-dropdown sq-edit-mention-dropdown"></div>
                    <div class="sq-edit-editor cm-editor sq-edit-textarea"
                         contenteditable="true"
                         data-placeholder="Yorumu düzenle..."
                         role="textbox"
                         aria-multiline="true"
                         spellcheck="true"></div>
                    <div class="sq-edit-actions">
                        <button class="sq-yorum-action edit sq-edit-save"><i class="fa-solid fa-check"></i> Kaydet</button>
                        <button class="sq-yorum-action sq-edit-cancel"><i class="fa-solid fa-xmark"></i> İptal</button>
                    </div>
                `;

                metinEl.style.display = 'none';
                footerEl.style.display = 'none';
                bubble.appendChild(editArea);

                const editEditorEl  = editArea.querySelector('.sq-edit-editor');
                const editDropdownEl = editArea.querySelector('.sq-edit-mention-dropdown');

                // Mevcut metni editöre yükle
                editEditorEl.textContent = mevcutMetin;
                _this._attachMentionListeners(editEditorEl, editDropdownEl);
                editEditorEl.focus();
                // Kursörü sona taşı
                const selRange = document.createRange();
                selRange.selectNodeContents(editEditorEl);
                selRange.collapse(false);
                window.getSelection().removeAllRanges();
                window.getSelection().addRange(selRange);

                // İptal
                editArea.querySelector('.sq-edit-cancel').addEventListener('click', () => {
                    editArea.remove();
                    metinEl.style.display = '';
                    footerEl.style.display = '';
                    // Ana editöre geri dön
                    const mainEditor = document.getElementById('gcmYorumInput');
                    const mainDd     = document.getElementById('gcmMentionDropdown');
                    if (mainEditor) { _this._currentEditor = mainEditor; _this._currentDropdown = mainDd; }
                });

                // Kaydet
                editArea.querySelector('.sq-edit-save').addEventListener('click', async () => {
                    const yeniMetin = _this._getEditorText(editEditorEl).trim();
                    if (!yeniMetin) return;

                    const saveBtn = editArea.querySelector('.sq-edit-save');
                    saveBtn.disabled = true;
                    saveBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

                    const res = await window.apiPost('/api/switch-case/yorumGonderi', {
                        action: 'update',
                        yorumId: yorumId,
                        yorumMetni: yeniMetin
                    });

                    if (res.status === 'success') {
                        // DOM'u anlık güncelle
                        metinEl.innerHTML = _this._renderMentions(res.data.yeniMetin).replace(/\n/g, '<br>');
                        btn.dataset.metin = res.data.yeniMetin;
                        editArea.remove();
                        metinEl.style.display = '';
                        footerEl.style.display = '';
                        // Ana editöre geri dön
                        const mainEditor = document.getElementById('gcmYorumInput');
                        const mainDd     = document.getElementById('gcmMentionDropdown');
                        if (mainEditor) { _this._currentEditor = mainEditor; _this._currentDropdown = mainDd; }
                        window.showToast('Yorum güncellendi.');
                    } else {
                        window.showToast(res.message || 'Güncellenemedi.', 'error');
                        saveBtn.disabled = false;
                        saveBtn.innerHTML = '<i class="fa-solid fa-check"></i> Kaydet';
                    }
                });
            });
        });
    },

    updateYorumCountUI(count) {
        if (count !== undefined) {
            // Update on details page if available
            document.querySelectorAll('.sq-yorum-count, .sd-action-btn[data-soru-id="'+this.aktifGonderiId+'"] span').forEach(el => {
                el.textContent = count;
            });
        }
    },

    async sendYorum() {
        const editor = document.getElementById('gcmYorumInput');
        const btn    = document.getElementById('cmBtnSendYorum');
        const metin  = this._getEditorText(editor).trim();
        if (!metin) return;
        
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

        const res = await window.apiPost('/api/switch-case/yorumGonderi', {
            action: 'create',
            gonderiId: this.aktifGonderiId,
            yorumMetni: metin,
            ustYorumId: this.aktifUstYorumId || ''
        });

        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i>';

        if (res.status === 'success') {
            document.getElementById('gcmBtnCancelReply').click();
            editor.innerHTML = '';
            this.updateYorumCountUI(res.data?.yeniYorumSayisi);
            this.loadYorumlar();
            setTimeout(() => {
                const body = document.getElementById('gcmBody');
                body.scrollTop = body.scrollHeight;
            }, 300);
        } else {
            window.showToast(res.message || 'Gönderilemedi', 'error');
        }
    },

    /* ---- Mention (Etiket) Sistemi ---- */

    /** Editor'daki tüm metni (mention tag'leri dahil) düz string olarak al */
    _getEditorText(editor) {
        let text = '';
        editor.childNodes.forEach(node => {
            if (node.nodeType === Node.TEXT_NODE) {
                text += node.textContent;
            } else if (node.classList && node.classList.contains('cm-mention-tag')) {
                text += '@' + node.dataset.username;
            } else {
                text += node.textContent;
            }
        });
        return text;
    },

    /** Yorum metnindeki @kullaniciAdi'lerini mor tıklanabilir linke dönüştür */
    _renderMentions(text) {
        return window.escHtml(text)
            .replace(/@([a-zA-Z0-9_\.]{1,30})/g,
                '<a href="/user/profil/$1" class="cm-mention-link">@$1</a>');
    },

    /** Mention tag span'i oluştur */
    _createMentionTag(username) {
        const span = document.createElement('span');
        span.className = 'cm-mention-tag';
        span.contentEditable = 'false';
        span.dataset.username = username;
        span.textContent = '@' + username;
        return span;
    },

    /** Kursörü bir node sonrasına taşı */
    _placeCursorAfter(node) {
        const sel = window.getSelection();
        const range = document.createRange();
        range.setStartAfter(node);
        range.collapse(true);
        sel.removeAllRanges();
        sel.addRange(range);
    },

    _attachMentionListeners(editorEl, dropdownEl) {
        editorEl.addEventListener('focus', () => {
            this._currentEditor  = editorEl;
            this._currentDropdown = dropdownEl;
        });
        editorEl.addEventListener('input', () => this._onEditorInput());
        editorEl.addEventListener('keydown', (e) => this._onEditorKeydown(e));
        // İlk bağlamada hemen aktif yap
        this._currentEditor  = editorEl;
        this._currentDropdown = dropdownEl;
    },

    _closeMentionDropdown() {
        const dd = this._currentDropdown;
        if (dd) { dd.innerHTML = ''; dd.style.display = 'none'; }
        this._mentionQuery = null;
    },

    _onEditorInput() {
        const sel = window.getSelection();
        if (!sel.rangeCount) return;

        // Kursörün solundaki metni al
        const range = sel.getRangeAt(0);
        const textBefore = range.startContainer.textContent?.slice(0, range.startOffset) || '';

        // Son @... patternını ara
        const match = textBefore.match(/@([a-zA-Z0-9_\.]*)$/);
        if (match && match[1].length >= 3) {
            const query = match[1];
            if (query !== this._mentionQuery) {
                this._mentionQuery = query;
                clearTimeout(this._mentionSearchTimer);
                this._mentionSearchTimer = setTimeout(() => this._fetchMentionSuggestions(query), 300);
            }
        } else {
            this._closeMentionDropdown();
        }
    },

    _onEditorKeydown(e) {
        const dd = document.getElementById('gcmMentionDropdown');
        const items = dd?.querySelectorAll('.cm-mention-item');
        if (!items || items.length === 0 || dd.style.display === 'none') return;

        const active = dd.querySelector('.cm-mention-item.active');
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            const next = active ? active.nextElementSibling : items[0];
            active?.classList.remove('active');
            next?.classList.add('active');
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            const prev = active ? active.previousElementSibling : items[items.length - 1];
            active?.classList.remove('active');
            prev?.classList.add('active');
        } else if (e.key === 'Enter' || e.key === 'Tab') {
            if (active) {
                e.preventDefault();
                this._insertMention(active.dataset.username);
            }
        } else if (e.key === 'Escape') {
            this._closeMentionDropdown();
        }
    },

    async _fetchMentionSuggestions(query) {
        try {
            const res = await window.apiPost('/api/switch-case/search', {
                action: 'search',
                keyword: query
            });
            if (res.status === 'success') {
                this._showMentionDropdown(res.data.slice(0, 5));
            }
        } catch (e) { /* sessiz hata */ }
    },

    _showMentionDropdown(users) {
        const dd = this._currentDropdown;
        if (!users || users.length === 0) { this._closeMentionDropdown(); return; }

        dd.innerHTML = users.map(u => {
            const avatar = u.profilFotografi
                ? (u.profilFotografi.startsWith('http') ? u.profilFotografi : '/uploads/profiles/' + u.profilFotografi)
                : `https://sinavortagim.artilingo.com/system/default-avatar.png`;
            return `
                <div class="cm-mention-item" data-username="${window.escHtml(u.kullaniciAdi)}">
                    <img src="${window.escHtml(avatar)}" alt="">
                    <div class="cm-mention-info">
                        <strong>${window.escHtml(u.adSoyad || u.kullaniciAdi)}</strong>
                        <span>@${window.escHtml(u.kullaniciAdi)}</span>
                    </div>
                </div>`;
        }).join('');
        dd.style.display = 'block';

        dd.querySelectorAll('.cm-mention-item').forEach(item => {
            item.addEventListener('mousedown', (e) => {
                e.preventDefault();
                this._insertMention(item.dataset.username);
            });
        });
    },

    _insertMention(username) {
        const editor = this._currentEditor;
        const sel = window.getSelection();
        if (!sel.rangeCount || !editor) return;

        const range = sel.getRangeAt(0);
        const node  = range.startContainer;
        const text  = node.textContent || '';
        const offset = range.startOffset;

        // @ ile başlayan kısmı sil
        const atIdx = text.lastIndexOf('@', offset - 1);
        if (atIdx !== -1) {
            node.textContent = text.slice(0, atIdx) + text.slice(offset);
            range.setStart(node, atIdx);
            range.collapse(true);
        }

        // Mention span ekle
        const tag = this._createMentionTag(username);
        range.insertNode(tag);

        // Tag sonrasına boşluk ekle ve kursörü oraya taşı
        const space = document.createTextNode('\u00a0');
        tag.parentNode.insertBefore(space, tag.nextSibling);
        this._placeCursorAfter(space);

        this._closeMentionDropdown();
        editor.focus();
    },

};

window.toggleLikePost = function(btn, gonderiId) {
    const formData = new FormData();
    formData.append('action', 'toggleLike');
    formData.append('gonderiId', gonderiId);
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    if (csrfToken) formData.append('csrf_token', csrfToken);

    fetch('/api/switch-case/gonderi', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            const icon = btn.querySelector('i.fa-heart');
            const span = btn.querySelector('span');
            
            if (data.begendi) {
                btn.classList.add('liked');
                icon.classList.remove('fa-regular');
                icon.classList.add('fa-solid');
                icon.style.color = 'var(--danger-color, #ef4444)';
            } else {
                btn.classList.remove('liked');
                icon.classList.remove('fa-solid');
                icon.classList.add('fa-regular');
                icon.style.color = '';
            }
            
            if (span) {
                span.textContent = data.yeniSayisi;
            }
        } else {
            if (typeof window.showToast === 'function') {
                window.showToast(data.message || 'Hata oluştu', 'error');
            }
        }
    })
    .catch(err => {
        if (typeof window.showToast === 'function') {
            window.showToast('Bağlantı hatası', 'error');
        }
    });
};

window.toggleSavePost = function(btn, gonderiId) {
    const formData = new FormData();
    formData.append('action', 'toggleSave');
    formData.append('gonderiId', gonderiId);
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    if (csrfToken) formData.append('csrf_token', csrfToken);

    fetch('/api/switch-case/gonderi', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            const icon = btn.querySelector('i.fa-bookmark');
            const span = btn.querySelector('span') || btn.childNodes[btn.childNodes.length - 1];
            
            if (data.kaydetti) {
                btn.classList.add('saved');
                icon.classList.remove('fa-regular');
                icon.classList.add('fa-solid');
                icon.style.color = 'var(--brand-primary, #3b82f6)';
            } else {
                btn.classList.remove('saved');
                icon.classList.remove('fa-solid');
                icon.classList.add('fa-regular');
                icon.style.color = '';
            }
            
            // Eğer butonda span veya metin node'u varsa sayıyı güncelle
            if (data.yeniSayisi !== undefined) {
                if (btn.querySelector('span')) {
                    btn.querySelector('span').textContent = data.yeniSayisi;
                } else {
                    btn.innerHTML = `<i class="${data.kaydetti ? 'fa-solid' : 'fa-regular'} fa-bookmark" ${data.kaydetti ? 'style="color: var(--brand-primary, #3b82f6);"' : ''}></i> ${data.yeniSayisi}`;
                }
            }
        } else {
            if (typeof window.showToast === 'function') {
                window.showToast(data.message || 'Hata oluştu', 'error');
            }
        }
    })
    .catch(err => {
        if (typeof window.showToast === 'function') {
            window.showToast('Bağlantı hatası', 'error');
        }
    });
};


/* --- Global Mention Manager --- */
class GlobalMentionManager {
    constructor(editorElement, dropdownElement) {
        this.editor = typeof editorElement === 'string' ? document.querySelector(editorElement) : editorElement;
        this.dropdown = typeof dropdownElement === 'string' ? document.querySelector(dropdownElement) : dropdownElement;
        
        this.mentionQuery = null;
        this.searchTimer = null;
        this.activeRange = null;

        if (this.editor && this.dropdown) {
            this.initListeners();
        }
    }

    initListeners() {
        this.editor.addEventListener('input', (e) => this.handleInput(e));
        this.editor.addEventListener('keydown', (e) => this.handleKeyDown(e));
        document.addEventListener('click', (e) => {
            if (!this.editor.contains(e.target) && !this.dropdown.contains(e.target)) {
                this.closeDropdown();
            }
        });
    }

    handleInput(e) {
        const selection = window.getSelection();
        if (!selection.rangeCount) return;
        const range = selection.getRangeAt(0);
        
        // Sadece metin düğümlerinde işlem yap
        if (range.startContainer.nodeType !== Node.TEXT_NODE) {
            this.closeDropdown();
            return;
        }

        const text = range.startContainer.textContent;
        const offset = range.startOffset;
        
        // İmleçten geriye doğru @ işaretini ara (boşluk veya string başı olmalı)
        const textBeforeCursor = text.slice(0, offset);
        const match = textBeforeCursor.match(/(?:^|\s)@(\w*)$/);

        if (match) {
            const query = match[1];
            this.activeRange = range.cloneRange(); // Konumu kaydet
            
            if (query !== this.mentionQuery) {
                this.mentionQuery = query;
                clearTimeout(this.searchTimer);
                this.searchTimer = setTimeout(() => this.fetchSuggestions(query), 300);
            }
        } else {
            this.closeDropdown();
        }
    }

    handleKeyDown(e) {
        if (this.dropdown.style.display !== 'block') return;

        const items = this.dropdown.querySelectorAll('.cm-mention-item');
        if (items.length === 0) return;

        const active = this.dropdown.querySelector('.cm-mention-item.active');
        let index = Array.from(items).indexOf(active);

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (active) active.classList.remove('active');
            index = (index + 1) % items.length;
            items[index].classList.add('active');
            items[index].scrollIntoView({ block: 'nearest' });
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (active) active.classList.remove('active');
            index = (index - 1 + items.length) % items.length;
            items[index].classList.add('active');
            items[index].scrollIntoView({ block: 'nearest' });
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (active) {
                this.insertMention(active.dataset.username);
            }
        } else if (e.key === 'Escape') {
            this.closeDropdown();
        }
    }

    async fetchSuggestions(query) {
        if (!query && query !== '') return;
        
        try {
            const res = await window.apiPost('/api/switch-case/search', {
                action: 'search',
                keyword: query
            });

            if (res.status === 'success' && res.data.length > 0) {
                this.showDropdown(res.data.slice(0, 5));
            } else {
                this.closeDropdown();
            }
        } catch (err) {
            console.error('Mention arama hatası', err);
            this.closeDropdown();
        }
    }

    showDropdown(users) {
        if (!users || users.length === 0) {
            this.closeDropdown();
            return;
        }

        let html = '';
        users.forEach((u, idx) => {
            const avatar = u.profilFotografi || 'https://sinavortagim.artilingo.com/system/default-avatar.png';
            html += `
                <div class="cm-mention-item ${idx === 0 ? 'active' : ''}" data-username="${window.escHtml(u.kullaniciAdi)}">
                    <img src="${window.escHtml(avatar)}" alt="Avatar">
                    <div class="cm-mention-info">
                        <strong>${window.escHtml(u.adSoyad)}</strong>
                        <span>@${window.escHtml(u.kullaniciAdi)}</span>
                    </div>
                </div>
            `;
        });

        this.dropdown.innerHTML = html;
        this.dropdown.style.display = 'block';

        this.dropdown.querySelectorAll('.cm-mention-item').forEach(item => {
            item.addEventListener('mouseenter', () => {
                this.dropdown.querySelector('.active')?.classList.remove('active');
                item.classList.add('active');
            });
            item.addEventListener('click', () => {
                this.insertMention(item.dataset.username);
            });
        });
    }

    closeDropdown() {
        this.dropdown.style.display = 'none';
        this.mentionQuery = null;
    }

    insertMention(username) {
        if (!this.activeRange) return;

        const selection = window.getSelection();
        selection.removeAllRanges();
        selection.addRange(this.activeRange);

        const node = this.activeRange.startContainer;
        const text = node.textContent || '';
        const offset = this.activeRange.startOffset;

        // @ işaretini bul ve sil
        const atIdx = text.lastIndexOf('@', offset - 1);
        if (atIdx !== -1) {
            node.textContent = text.slice(0, atIdx) + text.slice(offset);
            this.activeRange.setStart(node, atIdx);
            this.activeRange.collapse(true);
        }

        // Tag oluştur
        const span = document.createElement('span');
        span.className = 'cm-mention-tag';
        span.contentEditable = 'false';
        span.dataset.username = username;
        span.textContent = '@' + username;

        this.activeRange.insertNode(span);

        // Boşluk ekle ve imleci sona al
        const space = document.createTextNode('\u00a0');
        span.parentNode.insertBefore(space, span.nextSibling);
        
        selection.removeAllRanges();
        const newRange = document.createRange();
        newRange.setStart(space, 1);
        newRange.collapse(true);
        selection.addRange(newRange);

        this.closeDropdown();
        this.editor.focus();
    }

    // Gönderim öncesi tüm metni düz yazıya çevirme
    getPlainText() {
        return this.editor.innerText.trim();
    }
}
window.GlobalMentionManager = GlobalMentionManager;