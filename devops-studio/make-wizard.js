// make-wizard.js — ArtiFrame DevOps Studio Make Wizard Logic
// NOT: fs, path, currentCwd, updateExplorerPreview, loadTree
// ana index.html script bloğunda tanımlıdır; buradan window üzerinden erişilir.

'use strict';

let wizardActivePath = '';
let wizardActiveType = '';

function renderMakeWizard(relPath) {
    wizardActivePath = relPath;
    document.getElementById('make-wizard-path').innerText = 'Seçili Dizin: ' + relPath + '/';
    document.getElementById('make-btn-generate').style.display = 'block';

    const form = document.getElementById('make-wizard-form');
    let html = '';

    if (relPath.startsWith('public/api')) {
        wizardActiveType = 'api';
        html  = '<div style="margin-bottom:15px;">';
        html += '<label style="display:block;font-size:12px;color:#8b949e;margin-bottom:5px;">API Adı (Örn: auth/login veya dosya)</label>';
        html += '<input type="text" id="mw-name" class="form-control" oninput="updateMakePreview()" placeholder="api_adi">';
        html += '</div>';
        html += '<div style="margin-bottom:15px;display:flex;gap:15px;">';
        html += '<label style="display:flex;align-items:center;gap:5px;font-size:13px;color:#c9d1d9;"><input type="radio" name="mw-api-type" value="standart" onchange="updateMakePreview()" checked> Standart API</label>';
        html += '<label style="display:flex;align-items:center;gap:5px;font-size:13px;color:#c9d1d9;"><input type="radio" name="mw-api-type" value="switch-case" checked onchange="updateMakePreview()"> Switch-Case API</label>';
        html += '</div>';
        html += '<div><label style="display:flex;align-items:center;gap:5px;font-size:13px;color:#c9d1d9;"><input type="checkbox" id="mw-api-guest" onchange="updateMakePreview()"> Misafirlere Açık Mı? (ApiAuthControl)</label></div>';

    } else if (relPath.startsWith('src') || relPath.startsWith('app') || relPath.startsWith('config')) {
        wizardActiveType = 'class';
        html  = '<div style="margin-bottom:15px;">';
        html += '<label style="display:block;font-size:12px;color:#8b949e;margin-bottom:5px;">Sınıf Adı (Örn: User, Payment/Stripe)</label>';
        html += '<input type="text" id="mw-name" class="form-control" oninput="updateMakePreview()" placeholder="SinifAdi">';
        html += '</div>';
        html += '<div style="margin-bottom:15px;"><label style="display:flex;align-items:center;gap:5px;font-size:13px;color:#c9d1d9;"><input type="checkbox" id="mw-class-singleton" onchange="updateMakePreview()"> Singleton (getInstance) kalıbı ekle</label></div>';
        html += '<div><label style="display:flex;align-items:center;gap:5px;font-size:13px;color:#c9d1d9;"><input type="checkbox" id="mw-class-crud" onchange="updateMakePreview()"> Standart CRUD (create, update, delete vb.) iskeleti ekle</label></div>';

    } else if (relPath.startsWith('public')) {
        wizardActiveType = 'view';
        html  = '<div style="margin-bottom:15px;">';
        html += '<label style="display:block;font-size:12px;color:#8b949e;margin-bottom:5px;">Sayfa Adı (Örn: panel/ayarlar)</label>';
        html += '<input type="text" id="mw-name" class="form-control" oninput="updateMakePreview()" placeholder="sayfa-adi">';
        html += '</div>';
        html += '<div style="margin-bottom:15px;"><label style="display:flex;align-items:center;gap:5px;font-size:13px;color:#c9d1d9;"><input type="checkbox" id="mw-view-layout" checked onchange="updateMakePreview()"> Standart App Layout (Header/Sidebar) eklensin</label></div>';
        html += '<div><label style="display:flex;align-items:center;gap:5px;font-size:13px;color:#c9d1d9;"><input type="checkbox" id="mw-view-assets" checked onchange="updateMakePreview()"> Özel CSS/JS dosyası oluşturulup sayfaya bağlansın</label></div>';

    } else {
        wizardActiveType = null;
        document.getElementById('make-btn-generate').style.display = 'none';
        html = '<div style="color:#8b949e;font-size:13px;text-align:center;">Bu klasör için üretim şablonu yok. Sağ tıklayarak dosya ekleyebilirsiniz.</div>';
    }

    form.innerHTML = html;
    updateMakePreview();
}

function updateMakePreview() {
    const preview = document.getElementById('make-wizard-preview');
    const pathInfo = document.getElementById('make-wizard-path');
    
    if (!wizardActiveType) { 
        preview.innerText = ''; 
        if(pathInfo) pathInfo.innerText = 'Klasör seçimi bekleniyor...';
        return; 
    }

    const nameEl = document.getElementById('mw-name');
    const name   = nameEl ? nameEl.value.trim() : '';
    
    if (pathInfo) {
        let finalPath = wizardActivePath;
        if (wizardActiveType === 'api') {
            const apiTypeEl = document.querySelector('input[name="mw-api-type"]:checked');
            if (apiTypeEl && (wizardActivePath === 'public/api' || wizardActivePath === 'public/api/')) {
                finalPath = wizardActivePath + '/' + apiTypeEl.value;
            }
        }
        let ext = name ? (name.endsWith('.php') ? '' : '.php') : '';
        pathInfo.innerText = 'Hedef: ' + finalPath + '/' + name + ext;
    }

    if (!name) { preview.innerText = '// Lütfen bir isim girin...'; return; }

    const L = [];

    if (wizardActiveType === 'api') {
        const isGuest   = document.getElementById('mw-api-guest').checked;
        const apiType   = document.querySelector('input[name="mw-api-type"]:checked').value;
        const bootstrap = isGuest ? 'ApiAuthControl' : 'ApiControl';

        L.push('<?php');
        L.push("$allowedMethods = ['POST'];");
        L.push("require_once $_SERVER['DOCUMENT_ROOT'] . '/../app/" + bootstrap + ".php';");
        L.push('', 'use Bin\\SystemMethod;', '');

        if (isGuest) {
            L.push("// Misafir API: CSRF manuel dogrulanmalidir.");
            L.push("if (!SystemMethod::verifyCsrf($_POST['csrf_token'] ?? '')) {");
            L.push("    jsonResponse(['status' => 'error', 'message' => 'Gecersiz CSRF token.'], 403);");
            L.push('}');
        } else {
            L.push('// ApiControl: Auth ve CSRF otomatik dogrulandi.');
        }
        L.push('');

        if (apiType === 'switch-case') {
            L.push("$action = SystemMethod::sanitizeString($_POST['action'] ?? '');", '');
            L.push('switch ($action) {');
            L.push("    case 'ornek_islem':", '        // Islemler...', "        jsonResponse(['status' => 'success'], 200);", '        break;');
            L.push('', '    default:', "        jsonResponse(['status' => 'error', 'message' => 'Gecersiz islem.'], 400);", '}');
        } else {
            L.push('// Islemler...', "jsonResponse(['status' => 'success'], 200);");
        }

    } else if (wizardActiveType === 'class') {
        const isSingleton = document.getElementById('mw-class-singleton').checked;
        const isCrud      = document.getElementById('mw-class-crud').checked;

        const raw     = (wizardActivePath + '/' + name).split('/').filter(Boolean);
        const clsName = raw[raw.length - 1].charAt(0).toUpperCase() + raw[raw.length - 1].slice(1);
        const ns      = raw.slice(0, -1).map(function(p) { return p.charAt(0).toUpperCase() + p.slice(1); }).join('\\');

        L.push('<?php', 'namespace ' + ns + ';', '', 'use App\\Database;', 'use Bin\\SystemMethod;', '', 'class ' + clsName, '{');

        if (isSingleton) {
            L.push(
                '    private static ?' + clsName + ' $instance = null;',
                '    public $db;', '',
                '    private function __construct()',
                '    {',
                '        $this->db = \App\Database::getInstance();',
                '    }', '',
                '    public static function getInstance(): ' + clsName,
                '    {',
                '        if (self::$instance === null) { self::$instance = new self(); }',
                '        return self::$instance;',
                '    }'
            );
        }

        if (isCrud) {
            var m = isSingleton ? '' : 'static ';
            var methods = [
                ['create',  'array $data',                  'array',   ["$id = SystemMethod::byteId();", "$result = dbInsert('tablo_adi', $data);", "return ['success' => (bool)$result, 'id' => bin2hex($id)];"]],
                ['update',  'string $id, array $data',      'bool',    ["$result = dbUpdate('tablo_adi', $data, ['id' => $id]);", "return (bool)$result;"]],
                ['delete',  'string $id',                   'bool',    ["$result = dbDelete('tablo_adi', ['id' => $id]);", "return (bool)$result;"]],
                ['getById', 'string $id',                   '?array',  ["return dbGet('tablo_adi', ['id' => $id]);"]],
                ['getList', 'array $filters = [], int $limit = 100', 'array', ["return dbList('tablo_adi', $filters, '', $limit);"]],
            ];
            methods.forEach(function(def) {
                var fn = def[0], params = def[1], ret = def[2], body = def[3];
                L.push('', '    public ' + m + 'function ' + fn + '(' + params + '): ' + ret, '    {');
                body.forEach(function(l) { L.push('        ' + l); });
                L.push('    }');
            });
        }

        if (!isSingleton && !isCrud) {
            L.push('', '    public static function doSomething()', '    {', '        // ...', '    }');
        }
        L.push('}');

    } else if (wizardActiveType === 'view') {
        var isLayout = document.getElementById('mw-view-layout').checked;
        var isAssets = document.getElementById('mw-view-assets').checked;
        var baseName  = name.split('/').pop();
        var titleName = baseName.charAt(0).toUpperCase() + baseName.slice(1);

        L.push('<?php', "require_once $_SERVER['DOCUMENT_ROOT'] . '/../app/ViewControl.php';", 'use Bin\\ViewMethod;', '', '?>');
        L.push('<!DOCTYPE html>', '<html lang="tr" data-theme="default" data-mode="light">', '<head>');
        var headFile = (wizardActivePath.includes('public/auth')) ? 'head-auth.php' : 'head.php';
        L.push("    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/' + headFile + ''; ?>");
        L.push('    <title>' + titleName + '</title>');
        if (isAssets) L.push('    <link rel="stylesheet" href="/assets/css/' + name + '.css">');
        L.push('</head>', '<body>');

        if (isLayout) {
            L.push(
                '    <div class="app-layout">',
                "        <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/sidebar.php'; ?>",
                '        <div class="app-main-content">',
                "            <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/header.php'; ?>",
                '            <main class="main-content">',
                '                <div class="card">',
                '                    <h3>' + baseName + '</h3>',
                '                    <p>Icerik buraya...</p>',
                '                </div>',
                '            </main>',
                '        </div>',
                '    </div>'
            );
        } else {
            L.push('    <main>', '        <h1>' + baseName + '</h1>', '    </main>');
        }
        if (isAssets) L.push('    <script src="/assets/js/' + name + '.js"><\/script>');
        L.push('</body>', '</html>');
    }

    preview.innerText = L.join('\n');
}

function executeMakeWizard() {
    if (!wizardActiveType || !wizardActivePath) return;
    var nameEl = document.getElementById('mw-name');
    var name   = nameEl ? nameEl.value.trim() : '';
    if (!name) { alert('Lutfen isim girin.'); return; }

    var safeName = name;
    if (wizardActiveType === 'class') {
        var parts = name.split('/');
        parts[parts.length - 1] = parts[parts.length - 1].charAt(0).toUpperCase() + parts[parts.length - 1].slice(1);
        safeName = parts.join('/');
    }
    var ext        = safeName.endsWith('.php') ? '' : '.php';
    
    // API için standart veya switch-case seçimine göre alt klasöre yönlendir
    var finalPath = wizardActivePath;
    if (wizardActiveType === 'api') {
        var apiType = document.querySelector('input[name="mw-api-type"]:checked').value;
        if (wizardActivePath === 'public/api' || wizardActivePath === 'public/api/') {
            finalPath = path.join(wizardActivePath, apiType);
        }
    }
    
    var targetPath = path.join(currentCwd, finalPath, safeName + ext);

    if (fs.existsSync(targetPath)) { alert('Hata: Dosya zaten var!\n' + targetPath); return; }

    try {
        var dir = path.dirname(targetPath);
        if (!fs.existsSync(dir)) fs.mkdirSync(dir, { recursive: true });

        var code = document.getElementById('make-wizard-preview').innerText;
        fs.writeFileSync(targetPath, code, 'utf8');
        var log = '✅ PHP Dosyasi olusturuldu:\n' + targetPath + '\n';

        if (wizardActiveType === 'view') {
            var isAssets2 = document.getElementById('mw-view-assets').checked;
            if (isAssets2) {
                var cssPath = path.join(currentCwd, 'public', 'assets', 'css', name + '.css');
                var jsPath  = path.join(currentCwd, 'public', 'assets', 'js',  name + '.js');
                [cssPath, jsPath].forEach(function(p) {
                    var d = path.dirname(p);
                    if (!fs.existsSync(d)) fs.mkdirSync(d, { recursive: true });
                });
                if (!fs.existsSync(cssPath)) { fs.writeFileSync(cssPath, '/* ' + name + ' */'); log += '✅ CSS olusturuldu:\n' + cssPath + '\n'; }
                if (!fs.existsSync(jsPath))  { fs.writeFileSync(jsPath,  '// ' + name + '.js\n\n// CSRF Token (API istekleri icin zorunludur)\nconst csrfToken = document.querySelector(\'meta[name="csrf-token"]\')?.getAttribute(\'content\');\n'); log += '✅ JS olusturuldu:\n'  + jsPath  + '\n'; }
            }
        }

        updateExplorerPreview(log);
        loadTree();
    } catch (e) {
        alert('Hata olustu: ' + e.message);
    }
}
// handleFileClick hook for DevOps Studio
function handleFileClick(relPath) {
    if (!relPath.endsWith('.php') && !relPath.endsWith('.css') && !relPath.endsWith('.js')) {
        document.getElementById('exp-tab-cheat').innerHTML = '<div style="padding:30px; text-align:center; color:#8b949e;">Sadece kod dosyaları analiz edilebilir.</div>';
        document.getElementById('exp-tab-arch').innerHTML = '<div style="padding:30px; text-align:center; color:#8b949e;">Sadece kod dosyaları analiz edilebilir.</div>';
        if (typeof switchExpTab === 'function') switchExpTab('cheat');
        return;
    }

    const fullPath = path.join(currentCwd, relPath);
    let content = '';
    try {
        content = fs.readFileSync(fullPath, 'utf8');
    } catch (e) {
        return;
    }

    // ==========================================
    // TAB 3: MİMARİ BAĞIMLILIK HARİTASI (RÖNTGEN)
    // ==========================================
    let roleTitle = "Bilinmeyen Dosya";
    let roleDesc = "Bu dosyanın projedeki temel amacı anlaşılamadı.";
    let roleIcon = "❓";
    let isView = false;
    let isClass = false;
    let isApi = false;
    
    if (relPath.startsWith('public/api')) {
        isApi = true;
        roleTitle = "API Uç Noktası (Endpoint)";
        roleDesc = "Bu dosya, mobil uygulamalar veya dış sistemlerle JSON formatında haberleşmeyi sağlar. Arayüzü yoktur, sadece veri alıp veri gönderir.";
        roleIcon = "⚡";
    } else if (relPath.startsWith('public/')) {
        isView = true;
        roleTitle = "Kullanıcı Arayüzü (View)";
        roleDesc = "Kullanıcıların tarayıcıda gördüğü HTML tasarımını ve sayfayı barındırır. Son kullanıcının etkileşime girdiği yerdir.";
        roleIcon = "🖥️";
    } else if (relPath.startsWith('app/')) {
        isClass = true;
        roleTitle = "Sistem Çekirdeği (Core)";
        roleDesc = "Framework'ün kalbidir. Veritabanı bağlantısı, güvenlik kalkanları veya temel ayarlar burada barınır. (Dikkatli düzenlenmelidir).";
        roleIcon = "⚙️";
    } else if (relPath.startsWith('src/')) {
        isClass = true;
        roleTitle = "İş Mantığı (Service / Model)";
        roleDesc = "Uygulamanın beynidir. Karmaşık hesaplamalar, veritabanı sorguları ve kurallar burada yazılır. View veya API'ler buradaki kodları çağırır.";
        roleIcon = "🧠";
    }
    
    const usesDB = /dbList|dbGet|dbInsert|dbUpdate|dbDelete|\bPDO\b|\$this->db->prepare/i.test(content);
    const usesViewClass = /ViewMethod::/i.test(content);
    
    // Auth Mantığı
    let authTitle = "Korunmasız Dosya";
    let authDesc = "Bu dosyada hiçbir kontrol sınıfı çağırılmamış! Sistemin güvenlik duvarlarından (ViewControl/ApiControl) geçmiyor. Tehlikeli olabilir.";
    let authColor = "#ff7b72"; // Red
    
    // Açık sistem dosyaları ve hata sayfaları istisnası
    const publicFiles = ['404.php', '503.php', '403.php'];
    let isPublicSystemFile = publicFiles.some(f => relPath.endsWith(f));
    let isRouter = relPath.endsWith('index.php');

    if (!relPath.startsWith('public/')) {
        authTitle = "Kapalı Kutu (Backend)";
        authDesc = "Bu dosya web dizinine (public) açık DEĞİLDİR! Tarayıcıdan URL yazılarak doğrudan erişilemez. Sadece sistem içinden güvenle çağrılabilir.";
        authColor = "#3fb950"; // Green
    } else {
        if (/AuthControl|AuthApiControl/i.test(content)) {
            authTitle = "Ziyaretçi Kalkanı (Misafir)";
            authDesc = "Bu dosya aslında herkese (misafirlere) AÇIKTIR. Güvenlik/oturum katmanı yoktur. Aksine, hali hazırda giriş yapmış kullanıcılar buraya giremez (Örn: Giriş sayfası).";
            authColor = "#d2a8ff"; // Purple
        } else if (/ViewControl|ApiControl/i.test(content)) {
            authTitle = "Sistem Güvenlik Katmanı (Korumalı)";
            authDesc = "Uygulamanın asıl kalkanları olan ViewControl / ApiControl tarafından korunmaktadır. RBAC ve oturum (Session) denetimleri burada işler.";
            authColor = "#3fb950"; // Green
        } else if (isRouter) {
            authTitle = "Ana Yönlendirici (Router)";
            authDesc = "Bu dosya tüm istekleri karşılayıp ilgili sayfalara dağıtır. Doğası gereği herkese açıktır. Güvenlik, yönlendirdiği alt dosyaların sorumluluğundadır.";
            authColor = "#58a6ff"; // Blue
        } else if (isPublicSystemFile) {
            authTitle = "Sistem Hata Sayfası";
            authDesc = "Bu bir hata veya durum sayfasıdır. Güvenlik bariyeri uygulanmaz, doğası gereği herkese açık olmak zorundadır.";
            authColor = "#58a6ff"; // Blue
        }
    }

    // PHP Bağımlılıkları (use)
    const uses = [];
    const useRegex = /use\s+([^;]+);/g;
    let uMatch;
    while ((uMatch = useRegex.exec(content)) !== null) {
        uses.push(uMatch[1].trim());
    }

    // View için CSS/JS ve Include taraması
    const assets = [];
    const cssRegex = /href="([^"]+\.css[^"]*)"/g;
    const jsRegex = /src="([^"]+\.js[^"]*)"/g;
    let aMatch;
    while ((aMatch = cssRegex.exec(content)) !== null) assets.push({type: 'CSS', path: aMatch[1]});
    while ((aMatch = jsRegex.exec(content)) !== null) assets.push({type: 'JS', path: aMatch[1]});

    const includes = [];
    const reqRegex = /(?:require_once|include_once|require|include)[^\n]*?['"]([^'"]+\.php)['"]/g;
    let reqMatch;
    while ((reqMatch = reqRegex.exec(content)) !== null) {
        let incPath = reqMatch[1].split('/').pop();
        includes.push(incPath);
    }

    let archHtml = '<div style="padding:30px; display:flex; flex-direction:column; gap:20px;">';
    
    archHtml += '<div style="border-bottom:1px solid #30363d; padding-bottom:15px;">';
    archHtml += '<h2 style="margin:0; color:#58a6ff; font-size:22px; display:flex; align-items:center; gap:10px;">🕸️ Mimari Röntgeni</h2>';
    archHtml += '<p style="color:#8b949e; font-size:13px; margin:5px 0 0 0; line-height:1.5;">Bu panel, dosyanın uygulamanın geri kalanıyla nasıl konuştuğunu gösterir.</p>';
    archHtml += '</div>';

    archHtml += '<div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">';
    
    // Rol Kartı
    archHtml += '<div style="background:#0d1117; border:1px solid #30363d; border-radius:8px; padding:15px; display:flex; flex-direction:column; gap:8px;">';
    archHtml += '<div style="display:flex; align-items:center; gap:8px; font-size:15px; font-weight:bold; color:#f0f6fc;"><span>' + roleIcon + '</span> Dosyanın Görevi</div>';
    archHtml += '<div style="color:#79c0ff; font-size:13px; font-weight:600;">' + roleTitle + '</div>';
    archHtml += '<div style="color:#8b949e; font-size:12px; line-height:1.5;">' + roleDesc + '</div>';
    archHtml += '</div>';

    // Veritabanı Kartı
    archHtml += '<div style="background:#0d1117; border:1px solid #30363d; border-radius:8px; padding:15px; display:flex; flex-direction:column; gap:8px;">';
    archHtml += '<div style="display:flex; align-items:center; gap:8px; font-size:15px; font-weight:bold; color:#f0f6fc;"><span>🗄️</span> Veritabanı Etkileşimi</div>';
    if (usesDB) {
        archHtml += '<div style="color:#3fb950; font-size:13px; font-weight:600;">Aktif Bağlantı Var</div>';
        archHtml += '<div style="color:#8b949e; font-size:12px; line-height:1.5;">Bu dosya veritabanına sorgu atarak veri okuma veya yazma işlemleri yapıyor.</div>';
    } else {
        archHtml += '<div style="color:#8b949e; font-size:13px; font-weight:600;">Bağlantı Yok</div>';
        archHtml += '<div style="color:#8b949e; font-size:12px; line-height:1.5;">Bu dosya veritabanına dokunmuyor, sadece kendi içindeki mantıksal işlemleri yapıyor.</div>';
    }
    archHtml += '</div>';

    // Güvenlik Kartı
    archHtml += '<div style="background:#0d1117; border:1px solid #30363d; border-radius:8px; padding:15px; display:flex; flex-direction:column; gap:8px;">';
    archHtml += '<div style="display:flex; align-items:center; gap:8px; font-size:15px; font-weight:bold; color:#f0f6fc;"><span>🛡️</span> Güvenlik / Erişim</div>';
    archHtml += '<div style="color:' + authColor + '; font-size:13px; font-weight:600;">' + authTitle + '</div>';
    archHtml += '<div style="color:#8b949e; font-size:12px; line-height:1.5;">' + authDesc + '</div>';
    archHtml += '</div>';

    // Tasarım Kartı
    archHtml += '<div style="background:#0d1117; border:1px solid #30363d; border-radius:8px; padding:15px; display:flex; flex-direction:column; gap:8px;">';
    archHtml += '<div style="display:flex; align-items:center; gap:8px; font-size:15px; font-weight:bold; color:#f0f6fc;"><span>🎨</span> Tasarım / View Çıktısı</div>';
    if (usesViewClass || isView) {
        archHtml += '<div style="color:#d2a8ff; font-size:13px; font-weight:600;">Görsel Çıktı Üretiyor</div>';
        archHtml += '<div style="color:#8b949e; font-size:12px; line-height:1.5;">Bu dosya/sınıf ekrana HTML tasarımı (View) basıyor.</div>';
    } else {
        archHtml += '<div style="color:#8b949e; font-size:13px; font-weight:600;">Görsel Çıktı Yok</div>';
        archHtml += '<div style="color:#8b949e; font-size:12px; line-height:1.5;">Arka planda çalışır. Ekrana tasarım basmaz.</div>';
    }
    archHtml += '</div>';
    
    // View ise Varlıklar
    if (isView) {
        archHtml += '<div style="background:#0d1117; border:1px solid #30363d; border-radius:8px; padding:15px; display:flex; flex-direction:column; gap:8px; grid-column:1/-1;">';
        archHtml += '<div style="display:flex; align-items:center; gap:8px; font-size:15px; font-weight:bold; color:#f0f6fc;"><span>🔗</span> View Bağımlılıkları (Assets & Includes)</div>';
        
        if (assets.length > 0) {
            archHtml += '<div style="display:flex; flex-wrap:wrap; gap:8px; margin-top:5px;">';
            assets.forEach(a => {
                let badgeColor = a.type === 'CSS' ? '#d2a8ff' : '#ff7b72';
                archHtml += '<span style="background:#161b22; border:1px solid #30363d; color:#c9d1d9; font-size:11px; padding:3px 8px; border-radius:4px;"><strong style="color:'+badgeColor+';">' + a.type + ':</strong> ' + a.path + '</span>';
            });
            archHtml += '</div>';
        } else {
            archHtml += '<div style="color:#8b949e; font-size:12px;">Özel CSS/JS bağımlılığı tespit edilmedi.</div>';
        }

        if (includes.length > 0) {
            archHtml += '<div style="color:#79c0ff; font-size:12px; margin-top:10px; font-weight:600;">Dahil Edilen Modüller:</div>';
            archHtml += '<div style="display:flex; flex-wrap:wrap; gap:8px;">';
            includes.forEach(inc => {
                archHtml += '<span style="background:#21262d; border:1px solid #444c56; color:#8b949e; font-size:11px; padding:3px 8px; border-radius:4px;">' + inc + '</span>';
            });
            archHtml += '</div>';
        }
        archHtml += '</div>';
    }

    archHtml += '</div>'; // End Grid

    // Sınıf Bağımlılıkları (use)
    if (uses.length > 0) {
        archHtml += '<div style="background:#0d1117; border:1px solid #30363d; border-radius:8px; padding:15px; margin-top:15px;">';
        archHtml += '<div style="font-size:14px; font-weight:bold; color:#f0f6fc; margin-bottom:10px;">📦 PHP Kütüphane Bağımlılıkları (use)</div>';
        archHtml += '<div style="display:flex; flex-wrap:wrap; gap:8px;">';
        uses.forEach(u => {
            archHtml += '<span style="background:#161b22; border:1px solid #444c56; color:#79c0ff; font-size:12px; font-family:monospace; padding:4px 10px; border-radius:12px;">' + u + '</span>';
        });
        archHtml += '</div></div>';
    }
    
    archHtml += '</div>';
    document.getElementById('exp-tab-arch').innerHTML = archHtml;

    // ==========================================
    // TAB 2: SINIF CHEAT-SHEET
    // ==========================================
    const namespaceMatch = content.match(/namespace\s+([^;]+);/);
    const classMatch = content.match(/class\s+([a-zA-Z0-9_]+)/);
    
    if (!classMatch) {
        document.getElementById('exp-tab-cheat').innerHTML = '<div style="padding:30px; text-align:center; color:#8b949e;"><h2 style="color:#f0f6fc;">Bu bir sınıf değil.</h2><p>Bu dosyada geçerli bir Sınıf (Class) tanımı bulunamadı. Mimari Haritası sekmesinden dosya yeteneklerini inceleyebilirsiniz.</p></div>';
        if (typeof window.renderApiInspector === 'function') window.renderApiInspector(relPath, content);

    if (typeof switchExpTab === 'function') {
            if (isApi) { switchExpTab('api'); } else if (isClass) { switchExpTab('cheat'); } else { switchExpTab('arch'); }
        }
        return;
    }

    const nsName = namespaceMatch ? namespaceMatch[1].trim() : 'Global';
    const className = classMatch[1].trim();

    const props = [];
    const propRegex = /(public|protected|private)\s+(?:static\s+)?(?:\?[a-zA-Z0-9_\\]+\s+)?(\$[a-zA-Z0-9_]+)/g;
    let pMatch;
    while ((pMatch = propRegex.exec(content)) !== null) {
        props.push({ visibility: pMatch[1], name: pMatch[2] });
    }

    const methods = [];
    const methodRegex = /(?:(\/\*\*(?:(?!\*\/)[\s\S])*?\*\/)\s*)?(public|protected|private)\s+(static\s+)?function\s+([a-zA-Z0-9_]+)\s*\(([\s\S]*?)\)(?:\s*:\s*([a-zA-Z0-9_?\\|]+))?/g;
    let mMatch;
    while ((mMatch = methodRegex.exec(content)) !== null) {
        let rawDoc = mMatch[1] || '';
        let cleanDoc = rawDoc.replace(/\/\*\*|\*\/|^\s*\*\s?/gm, '').trim();
        
        methods.push({
            visibility: mMatch[2],
            isStatic: !!mMatch[3],
            name: mMatch[4],
            params: mMatch[5].trim(),
            returnType: mMatch[6] ? mMatch[6].trim() : 'mixed',
            doc: cleanDoc,
            id: 'method-' + mMatch[4]
        });
    }

    let cheatHtml = '<div style="padding:30px; display:flex; flex-direction:column; gap:20px;">';
    cheatHtml += '<div style="border-bottom:1px solid #30363d; padding-bottom:15px;">';
    cheatHtml += '<h2 style="margin:0; color:#58a6ff; font-size:24px; display:flex; align-items:center; gap:10px;">';
    cheatHtml += '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>';
    cheatHtml += className + '</h2>';
    cheatHtml += '<div style="color:#8b949e; font-size:13px; margin-top:5px; font-family:monospace;">namespace ' + nsName + ';</div>';
    cheatHtml += '</div>';

    if (props.length > 0) {
        cheatHtml += '<div>';
        cheatHtml += '<h4 style="color:#f0f6fc; border-bottom:1px solid #30363d; padding-bottom:8px; margin-bottom:10px;">Özellikler</h4>';
        cheatHtml += '<div style="display:flex; flex-wrap:wrap; gap:6px;">';
        props.forEach(p => {
            const color = p.visibility === 'public' ? '#3fb950' : (p.visibility === 'protected' ? '#d2a8ff' : '#ff7b72');
            cheatHtml += '<div style="display:flex; align-items:center; gap:8px; font-family:monospace; font-size:12px; background:#0d1117; padding:4px 10px; border-radius:4px; border:1px solid #30363d;">';
            cheatHtml += '<span style="color:' + color + ';">' + p.visibility + '</span>';
            cheatHtml += '<span style="color:#c9d1d9;">' + p.name + '</span>';
            cheatHtml += '</div>';
        });
        cheatHtml += '</div></div>';
    }

    if (methods.length > 0) {
        cheatHtml += '<div>';
        cheatHtml += '<div style="display:flex; justify-content:space-between; align-items:flex-end; border-bottom:1px solid #30363d; padding-bottom:8px; margin-bottom:10px;">';
        cheatHtml += '<h4 style="color:#f0f6fc; margin:0;">Metodlar</h4>';
        cheatHtml += '<input type="text" id="mw-method-search" placeholder="Metod Ara..." style="background:#0d1117; border:1px solid #30363d; color:#c9d1d9; border-radius:4px; padding:4px 8px; font-size:12px; width:150px; outline:none;" onkeyup="filterMethods()">';
        cheatHtml += '</div>';
        
        cheatHtml += '<div id="mw-methods-list" style="display:flex; flex-direction:column; gap:8px;">';
        methods.forEach(m => {
            const color = m.visibility === 'public' ? '#3fb950' : (m.visibility === 'protected' ? '#d2a8ff' : '#ff7b72');
            const staticBadge = m.isStatic ? '<span style="background:#21262d; border:1px solid #444c56; color:#8b949e; font-size:10px; padding:2px 6px; border-radius:10px;">STATIC</span>' : '';
            const usageExample = m.isStatic ? `${className}::${m.name}(${m.params ? '...' : ''})` : `$obj->${m.name}(${m.params ? '...' : ''})`;
            const hasDetails = m.params || m.doc || m.returnType !== 'mixed';

            cheatHtml += '<div class="mw-method-item" data-name="' + m.name.toLowerCase() + '" style="background:#0d1117; padding:12px; border-radius:6px; border:1px solid #30363d; display:flex; flex-direction:column; gap:8px;">';
            
            cheatHtml += '<div style="display:flex; justify-content:space-between; align-items:center;">';
            cheatHtml += '<div style="display:flex; align-items:center; gap:8px; font-family:monospace; font-size:14px; flex-wrap:wrap;">';
            cheatHtml += '<span style="color:' + color + ';">' + m.visibility + '</span>';
            cheatHtml += staticBadge;
            cheatHtml += '<span style="color:#79c0ff; font-weight:bold;">' + m.name + '</span>';
            cheatHtml += '<span style="color:#8b949e;">(' + (m.params ? '...' : '') + ')</span>';
            cheatHtml += '</div>';
            
            cheatHtml += '<div style="display:flex; gap:6px;">';
            if (hasDetails) {
                cheatHtml += `<button onclick="toggleMethodDetails('${m.id}')" style="background:#161b22; border:1px solid #30363d; color:#58a6ff; cursor:pointer; padding:4px 8px; border-radius:4px; font-size:11px;">Detaylar</button>`;
            }
            cheatHtml += `<button onclick="copyToClipboard('${usageExample.replace(/'/g, "\'")}', this)" style="background:#161b22; border:1px solid #30363d; color:#8b949e; cursor:pointer; padding:4px 8px; border-radius:4px; font-size:11px; width:75px;">Kopyala</button>`;
            cheatHtml += '</div>';
            cheatHtml += '</div>';
            
            if (hasDetails) {
                cheatHtml += `<div id="${m.id}" style="display:none; margin-top:8px; padding-top:8px; border-top:1px dashed #30363d; flex-direction:column; gap:8px; font-size:12px;">`;
                
                if (m.doc) {
                    cheatHtml += '<div style="color:#a5d6ff; font-style:italic; white-space:pre-wrap;">' + m.doc + '</div>';
                }
                
                if (m.params) {
                    cheatHtml += '<div style="background:#161b22; padding:8px; border-radius:4px; font-family:monospace; color:#e6edf3;">';
                    cheatHtml += '<div style="color:#8b949e; margin-bottom:4px;">Parametreler:</div>';
                    let coloredParams = m.params.replace(/(\$[a-zA-Z0-9_]+)/g, '<span style="color:#79c0ff;">$1</span>');
                    coloredParams = coloredParams.replace(/([a-zA-Z0-9_\\]+)\s+\$[a-zA-Z0-9_]+/g, '<span style="color:#ff7b72;">$1</span> $$');
                    cheatHtml += coloredParams;
                    cheatHtml += '</div>';
                }
                
                cheatHtml += '<div style="display:flex; gap:5px; align-items:center; color:#8b949e; font-family:monospace;">';
                cheatHtml += 'Dönüş (Return): <span style="color:#d2a8ff; font-weight:bold;">' + m.returnType + '</span>';
                cheatHtml += '</div>';
                
                cheatHtml += '</div>';
            }

            cheatHtml += '</div>';
        });
        cheatHtml += '</div></div>';
    }

    if (props.length === 0 && methods.length === 0) {
        cheatHtml += '<div style="color:#8b949e; font-size:13px; text-align:center; margin-top:20px;">Sınıfa ait özellik veya metod bulunamadı.</div>';
    }

    cheatHtml += '</div>';
    document.getElementById('exp-tab-cheat').innerHTML = cheatHtml;
    
    if (typeof window.renderApiInspector === 'function') window.renderApiInspector(relPath, content);

    if (typeof switchExpTab === 'function') {
        if (isApi) { switchExpTab('api'); } else if (isClass) { switchExpTab('cheat'); } else { switchExpTab('arch'); }
    }
}

// Global Filter function for Methods
window.filterMethods = function() {
    const query = document.getElementById('mw-method-search').value.toLowerCase();
    const items = document.querySelectorAll('.mw-method-item');
    items.forEach(item => {
        const name = item.getAttribute('data-name');
        if (name.includes(query)) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
};

// Clipboard helper
function copyToClipboard(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        if (btn) {
            const old = btn.innerText;
            btn.innerText = 'Kopyalandı!';
            btn.style.color = '#3fb950';
            btn.style.borderColor = '#3fb950';
            setTimeout(() => {
                btn.innerText = old;
                btn.style.color = '#8b949e';
                btn.style.borderColor = '#30363d';
            }, 1500);
        }
    });
}

// Global Toggle function for Method Details
window.toggleMethodDetails = function(id) {
    const el = document.getElementById(id);
    if (el) {
        el.style.display = el.style.display === 'none' ? 'flex' : 'none';
    }
};
