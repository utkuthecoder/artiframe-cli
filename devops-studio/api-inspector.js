const http = require('http'); // Node http for fetching csrf token if needed

function renderApiInspector(relPath, content) {
    const container = document.getElementById('api-inspector-container');
    const pathLabel = document.getElementById('api-inspector-path');
    
    if (!relPath.startsWith('public/api') || !relPath.endsWith('.php')) {
        pathLabel.innerText = "Sadece public/api altındaki PHP dosyaları test edilebilir.";
        container.innerHTML = '<div style="flex:1; display:flex; align-items:center; justify-content:center; color:#8b949e; font-size:13px; text-align:center;">Lütfen public/api klasöründen bir API dosyası seçin.</div>';
        return;
    }

    pathLabel.innerText = relPath;

    // Load APP_URL from .env
    const envPath = path.join(currentCwd, '.env');
    let appUrl = 'http://localhost:9002';
    if (fs.existsSync(envPath)) {
        const envContent = fs.readFileSync(envPath, 'utf8');
        const urlMatch = envContent.match(/^APP_URL=(.*)$/m);
        if (urlMatch) {
            appUrl = urlMatch[1].trim();
        }
    }
    const urlInput = document.getElementById('api-base-url');
    if (urlInput.value === 'http://localhost:9002') {
        urlInput.value = appUrl;
    }

    // Determine Endpoint Route
    // e.g. public/api/auth/login.php -> /api/auth/login
    let route = relPath.replace('public/', '/').replace('.php', '');

    // Smart Parsing
    const params = [];
    // Match $_POST['...'], $_GET['...'], $_REQUEST['...']
    const paramRegex = /\$_(POST|GET|REQUEST)\s*\[\s*['"]([a-zA-Z0-9_]+)['"]\s*\]/g;
    let pMatch;
    const foundKeys = new Set();

    while ((pMatch = paramRegex.exec(content)) !== null) {
        const type = pMatch[1];
        const key = pMatch[2];
        if (!foundKeys.has(key)) {
            foundKeys.add(key);
            // Try to infer type
            let inputType = 'text';
            if (key.toLowerCase().includes('password') || key.toLowerCase().includes('sifre')) {
                inputType = 'password';
            } else if (key.toLowerCase().includes('mail')) {
                inputType = 'email';
            }
            params.push({ key, type, inputType });
        }
    }

    // Build UI
    let uiHtml = `
        <div style="width:350px; background:#161b22; border:1px solid #30363d; border-radius:8px; display:flex; flex-direction:column; flex-shrink:0;">
            <div style="padding:15px; border-bottom:1px solid #30363d; display:flex; gap:10px;">
                <select id="api-method" style="background:#0d1117; border:1px solid #30363d; color:#58a6ff; font-weight:bold; border-radius:4px; padding:6px; outline:none; font-size:12px;">
                    <option value="POST" selected>POST</option>
                    <option value="GET">GET</option>
                    <option value="PUT">PUT</option>
                    <option value="DELETE">DELETE</option>
                </select>
                <input type="text" id="api-route" value="${route}" style="flex:1; background:#0d1117; border:1px solid #30363d; color:#c9d1d9; border-radius:4px; padding:6px 10px; font-size:12px; outline:none; font-family:monospace;">
            </div>
            
            <div style="flex:1; overflow-y:auto; padding:15px; display:flex; flex-direction:column; gap:15px;" id="api-params-form">
                <div style="font-size:12px; font-weight:bold; color:#f0f6fc; margin-bottom:5px;">Otomatik Çıkarılan Parametreler</div>
                ${params.length === 0 ? '<div style="color:#8b949e; font-size:12px; font-style:italic;">Parametre bulunamadı.</div>' : ''}
                ${params.map(p => `
                    <div style="display:flex; flex-direction:column; gap:4px;">
                        <label style="color:#8b949e; font-size:11px; display:flex; justify-content:space-between;">
                            <span style="font-family:monospace; color:#c9d1d9;">${p.key}</span>
                            <span style="color:#3fb950; font-size:10px;">$_${p.type}</span>
                        </label>
                        <input type="${p.inputType}" data-param="${p.key}" class="api-param-input" placeholder="Değer..." style="background:#0d1117; border:1px solid #30363d; color:#c9d1d9; border-radius:4px; padding:8px 10px; font-size:12px; outline:none; transition:border 0.2s;">
                    </div>
                `).join('')}
                
                <div style="margin-top:10px; border-top:1px dashed #30363d; padding-top:15px;">
                    <div style="font-size:12px; font-weight:bold; color:#f0f6fc; margin-bottom:10px;">DevOps Sihri</div>
                    <label style="display:flex; align-items:center; gap:8px; font-size:12px; color:#8b949e; cursor:pointer;">
                        <input type="checkbox" id="api-auto-csrf" checked style="accent-color:#58a6ff;">
                        CSRF Token'ı otomatik çek ve ekle
                    </label>
                </div>
            </div>
            
            <div style="padding:15px; border-top:1px solid #30363d; background:#0d1117; border-radius:0 0 8px 8px;">
                <button onclick="executeApiRequest()" style="width:100%; background:#238636; color:#fff; border:none; padding:10px; border-radius:6px; font-weight:bold; cursor:pointer; font-size:13px; display:flex; align-items:center; justify-content:center; gap:8px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                    GÖNDER (TEST ET)
                </button>
            </div>
        </div>
        
        <div style="flex:1; background:#0d1117; border:1px solid #30363d; border-radius:8px; display:flex; flex-direction:column; overflow:hidden;">
            <div style="padding:10px 15px; border-bottom:1px solid #30363d; background:#161b22; display:flex; justify-content:space-between; align-items:center;">
                <div style="font-size:12px; font-weight:bold; color:#f0f6fc;">Yanıt Görüntüleyici (Response)</div>
                <div style="display:flex; gap:15px; font-size:11px; font-family:monospace; color:#8b949e;">
                    <div id="api-res-status">Durum: Bekleniyor</div>
                    <div id="api-res-time">Süre: -</div>
                </div>
            </div>
            <div id="api-res-body" style="flex:1; padding:15px; overflow-y:auto; font-family:'JetBrains Mono', monospace; font-size:12px; color:#c9d1d9; white-space:pre-wrap; word-wrap:break-word;">
                <div style="color:#8b949e; opacity:0.6; text-align:center; margin-top:50px;">Henüz bir istek atılmadı.</div>
            </div>
        </div>
    `;
    
    container.innerHTML = uiHtml;
}

window.executeApiRequest = async function() {
    const baseUrl = document.getElementById('api-base-url').value.replace(/\/+$/, '');
    const route = document.getElementById('api-route').value;
    const method = document.getElementById('api-method').value;
    const autoCsrf = document.getElementById('api-auto-csrf').checked;
    
    const fullUrl = baseUrl + route;
    const formData = new FormData();
    
    // Topla
    document.querySelectorAll('.api-param-input').forEach(input => {
        formData.append(input.dataset.param, input.value);
    });
    
    const resBody = document.getElementById('api-res-body');
    const resStatus = document.getElementById('api-res-status');
    const resTime = document.getElementById('api-res-time');
    
    resBody.innerHTML = '<div style="color:#58a6ff; text-align:center; margin-top:50px;">İstek gönderiliyor... ⏳</div>';
    resStatus.innerText = 'Durum: Yükleniyor';
    resTime.innerText = 'Süre: -';
    
    let csrfToken = null;
    let cookieStr = '';
    
    try {
        if (autoCsrf) {
            resBody.innerHTML = '<div style="color:#d2a8ff; text-align:center; margin-top:50px;">CSRF Token alınıyor... 🛡️</div>';
            
            // fetch to base url to get CSRF
            const csrfRes = await fetch(baseUrl);
            const htmlText = await csrfRes.text();
            
            const metaMatch = htmlText.match(/<meta\s+name=["']csrf-token["']\s+content=["']([^"']+)["']/i);
            if (metaMatch) {
                csrfToken = metaMatch[1];
            } else {
                // Try finding it in inputs
                const inputMatch = htmlText.match(/<input\s+type=["']hidden["']\s+name=["']csrf_token["']\s+value=["']([^"']+)["']/i);
                if (inputMatch) csrfToken = inputMatch[1];
            }
            
            if (csrfToken) {
                formData.append('csrf_token', csrfToken); // ArtiFrame uses csrf_token
            }
        }
        
        const startTime = performance.now();
        
        const options = {
            method: method,
            credentials: 'include'
        };
        
        if (method !== 'GET') {
            options.body = formData;
        }
        
        const response = await fetch(fullUrl, options);
        const endTime = performance.now();
        
        const statusColor = response.ok ? '#3fb950' : '#ff7b72';
        resStatus.innerHTML = `Durum: <span style="color:${statusColor}; font-weight:bold;">${response.status} ${response.statusText}</span>`;
        resTime.innerText = `Süre: ${Math.round(endTime - startTime)}ms`;
        
        const contentType = response.headers.get('content-type');
        let responseText = await response.text();
        
        if (contentType && contentType.includes('application/json')) {
            try {
                const jsonObj = JSON.parse(responseText);
                responseText = JSON.stringify(jsonObj, null, 4);
                // Syntax highlighting
                responseText = responseText.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
                    let cls = 'color:#79c0ff;'; // string
                    if (/^"/.test(match)) {
                        if (/:$/.test(match)) {
                            cls = 'color:#f0f6fc; font-weight:bold;'; // key
                        } else {
                            cls = 'color:#a5d6ff;'; // string value
                        }
                    } else if (/true|false/.test(match)) {
                        cls = 'color:#ff7b72;'; // boolean
                    } else if (/null/.test(match)) {
                        cls = 'color:#d2a8ff;'; // null
                    } else {
                        cls = 'color:#3fb950;'; // number
                    }
                    return '<span style="' + cls + '">' + match + '</span>';
                });
            } catch(e) {}
        } else {
            // Escape HTML if not JSON
            responseText = responseText.replace(/</g, '&lt;').replace(/>/g, '&gt;');
        }
        
        resBody.innerHTML = responseText;
        
    } catch (err) {
        resStatus.innerHTML = `Durum: <span style="color:#ff7b72; font-weight:bold;">Hata!</span>`;
        resBody.innerHTML = `<span style="color:#ff7b72;">İstek başarısız oldu: ${err.message}</span>\n\nLütfen sunucunun (APP_URL) çalıştığından ve CORS engeli olmadığından emin olun.`;
    }
}

// Attach hook to global
window.renderApiInspector = renderApiInspector;
