
let currentWorkspace = null;
let promptCallback = null;
let previewDataCache = null;

async function initLauncher() {
    if (window.api && window.api.getWorkspace) {
        currentWorkspace = await window.api.getWorkspace();
        if (currentWorkspace) {
            document.getElementById('workspace-path').innerText = currentWorkspace;
        } else {
            document.getElementById('workspace-path').innerText = "Belirlenmedi (Lütfen Seçin)";
            document.getElementById('workspace-path').style.color = "#f39c12";
        }
    }
}

async function selectWorkspace() {
    if (window.api && window.api.selectDirectory) {
        const dir = await window.api.selectDirectory();
        if (dir) {
            await window.api.setWorkspace(dir);
            currentWorkspace = dir;
            document.getElementById('workspace-path').innerText = dir;
            document.getElementById('workspace-path').style.color = "#d4d4d4";
        }
    }
}

function openPrompt(title, desc, callback) {
    document.getElementById('modal-overlay').style.display = 'flex';
    document.getElementById('prompt-modal').style.display = 'block';
    document.getElementById('preview-choice-modal').style.display = 'none';
    document.getElementById('project-list-modal').style.display = 'none';
    
    document.getElementById('prompt-title').innerText = title;
    document.getElementById('prompt-desc').innerText = desc;
    document.getElementById('prompt-input').value = '';
    document.getElementById('prompt-input').focus();
    promptCallback = callback;
}

function closeModal() {
    document.getElementById('modal-overlay').style.display = 'none';
    promptCallback = null;
    previewDataCache = null;
}

function submitPrompt() {
    const val = document.getElementById('prompt-input').value.trim();
    if (val && promptCallback) {
        const cb = promptCallback;
        closeModal();
        cb(val);
    }
}

async function createNewProject() {
    if (!currentWorkspace) {
        document.getElementById('workspace-path').innerText = "Önce Buradan Çalışma Alanı Seçin ->";
        document.getElementById('workspace-path').style.color = "#e74c3c";
        return;
    }
    
    openPrompt("Yeni Proje Başlat", "Projeniz için bir klasör adı girin (Örn: my-app):\n(Proje " + currentWorkspace + " dizininde kurulacaktır)", async (pName) => {
        if (window.api && window.api.scaffoldProject) {
            const btn = document.querySelector('.card.success');
            const oldHtml = btn.innerHTML;
            btn.innerHTML = "<div class='title'>🚀 Proje Kuruluyor...</div>";
            
            const res = await window.api.scaffoldProject(pName, { tables: [] }, currentWorkspace);
            
            if (res.success) {
                window.api.openProject(res.path);
            } else {
                alert("Hata: " + res.error);
                btn.innerHTML = oldHtml;
            }
        }
    });
}

async function loadProject() {
    if (!currentWorkspace) {
        document.getElementById('workspace-path').innerText = "Önce Buradan Çalışma Alanı Seçin ->";
        document.getElementById('workspace-path').style.color = "#e74c3c";
        return;
    }
    
    // We should get all directories in workspace, not just the ones with .env, because .env might not exist yet if scaffold failed or just created.
    // Actually, getProjects in main.js filters by .env or 'artiframe'. Let's ensure it shows more.
    const projects = await window.api.getProjects(currentWorkspace);
    if (projects.length === 0) {
        openPrompt("Proje Bulunamadı", "Seçili çalışma alanında proje (.env veya artiframe dosyası içeren klasör) bulunamadı.", async (dir) => {
            // Do nothing special
        });
        return;
    }
    
    // Open custom project list modal
    document.getElementById('modal-overlay').style.display = 'flex';
    document.getElementById('prompt-modal').style.display = 'none';
    document.getElementById('preview-choice-modal').style.display = 'none';
    document.getElementById('project-list-modal').style.display = 'block';
    
    const select = document.getElementById('project-select');
    select.innerHTML = '';
    projects.forEach(p => {
        const opt = document.createElement('option');
        opt.value = p;
        opt.innerText = p;
        select.appendChild(opt);
    });
}

function submitProjectList() {
    const select = document.getElementById('project-select');
    if (select.value) {
        const pPath = currentWorkspace + '/' + select.value;
        closeModal();
        if (window.api && window.api.openProject) {
            window.api.openProject(pPath);
        }
    }
}

async function previewSchema() {
    if (!window.api || !window.api.loadArtiframeFile) return;
    
    const res = await window.api.loadArtiframeFile();
    if (!res) return; // User canceled dialog
    
    if (res.error) {
        alert(res.error);
        return;
    }
    
    previewDataCache = res.data.schema;
    
    // Show custom choice modal
    document.getElementById('modal-overlay').style.display = 'flex';
    document.getElementById('prompt-modal').style.display = 'none';
    document.getElementById('project-list-modal').style.display = 'none';
    document.getElementById('preview-choice-modal').style.display = 'block';
}

function choosePreview(action) {
    if (!previewDataCache) return;
    
    const schema = previewDataCache;
    closeModal();
    
    if (action === 'inspect') {
        if (window.api && window.api.previewSchemaFile) {
            window.api.previewSchemaFile(schema);
        }
    } else if (action === 'scaffold') {
        if (!currentWorkspace) {
            document.getElementById('workspace-path').innerText = "Önce Buradan Çalışma Alanı Seçin ->";
            document.getElementById('workspace-path').style.color = "#e74c3c";
            return;
        }
        
        openPrompt("Proje Kurulumu", "Bu şema ile kurulacak proje için klasör adı girin (Örn: blog-app):", async (pName) => {
            const btn = document.querySelector('.card.warning');
            const oldHtml = btn.innerHTML;
            btn.innerHTML = "<div class='title'>🚀 Proje Kuruluyor...</div>";
            
            const res = await window.api.scaffoldProject(pName, schema, currentWorkspace); 
            if (res.success) {
                window.api.openProject(res.path);
            } else {
                alert("Hata: " + res.error);
                btn.innerHTML = oldHtml;
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', initLauncher);
