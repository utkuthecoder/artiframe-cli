const { app, BrowserWindow, ipcMain, clipboard, dialog } = require('electron');
const path = require('path');

let mainWindow;
const args = process.argv.slice(2);
let defaultTab = args[0] || 'launcher';
const fs = require('fs');
const os = require('os');

let projectCwd = args[1] || process.cwd();

const defaultWorkspace = process.platform === 'win32' 
    ? 'C:\\ArtiFrame' 
    : path.join(os.homedir(), 'ArtiFrame');

// Klasör yoksa oluştur
if (!fs.existsSync(defaultWorkspace)) {
    try { fs.mkdirSync(defaultWorkspace, { recursive: true }); } catch (e) {}
}

let isProject = fs.existsSync(path.join(projectCwd, 'public', 'index.php')) || 
                fs.existsSync(path.join(projectCwd, '.env')) ||
                fs.existsSync(path.join(projectCwd, 'bin', 'artiframe.php'));

if (!isProject) {
    projectCwd = defaultWorkspace;
}

function createWindow() {
    mainWindow = new BrowserWindow({
        width: 1050,
        height: 750,
        minWidth: 900,
        minHeight: 600,
        backgroundColor: '#0d1117',
        webPreferences: {
            nodeIntegration: true,
            contextIsolation: false
        },
        autoHideMenuBar: true,
        title: 'ArtiFrame DevOps Studio'
    });

    mainWindow.loadFile('index.html'); mainWindow.webContents.openDevTools();
    
    mainWindow.webContents.on('did-finish-load', () => {
        mainWindow.webContents.send('init-data', { tab: defaultTab, cwd: projectCwd });
    });
}

app.whenReady().then(createWindow);

app.on('window-all-closed', () => {
    app.quit();
});

ipcMain.handle('copy-to-clipboard', (e, text) => {
    clipboard.writeText(text);
    return true;
});

ipcMain.handle('select-folder', async () => {
    const result = await dialog.showOpenDialog(mainWindow, {
        properties: ['openDirectory']
    });
    return result.canceled ? null : result.filePaths[0];
});
