const { app, BrowserWindow, ipcMain, shell } = require('electron');
const path = require('path');
const os = require('os');
const { spawn } = require('child_process');
const http = require('http');

let mainWindow;
let phpProcess;
let internalLogServer;
let loggerPort = 0;

const args = process.argv.slice(2);
let projectRoot = args[0] || process.cwd();
let serverPort = parseInt(args[1]) || 9002;

function getLocalIp() {
    const interfaces = os.networkInterfaces();
    for (const name of Object.keys(interfaces)) {
        for (const iface of interfaces[name]) {
            if (iface.family === 'IPv4' && !iface.internal) {
                return iface.address;
            }
        }
    }
    return '127.0.0.1';
}

function startLogServer() {
    return new Promise((resolve) => {
        internalLogServer = http.createServer((req, res) => {
            if (req.method === 'POST') {
                let body = '';
                req.on('data', chunk => { body += chunk.toString(); });
                req.on('end', () => {
                    try {
                        const logData = JSON.parse(body);
                        if (mainWindow) {
                            mainWindow.webContents.send('new-log', logData);
                        }
                    } catch (e) {}
                    res.writeHead(200);
                    res.end('OK');
                });
            } else {
                res.writeHead(404);
                res.end();
            }
        });

        internalLogServer.listen(0, '127.0.0.1', () => {
            loggerPort = internalLogServer.address().port;
            resolve();
        });
    });
}

function startPhpServer() {
    if (phpProcess) {
        phpProcess.kill();
    }
    
    const routerPath = path.join(__dirname, 'router.php');
    
    phpProcess = spawn('php', ['-S', `0.0.0.0:${serverPort}`, '-t', path.join(projectRoot, 'public'), routerPath], {
        cwd: projectRoot,
        env: {
            ...process.env,
            ARTIFRAME_STUDIO_LOGGER_PORT: loggerPort,
            ARTIFRAME_PROJECT_ROOT: projectRoot
        }
    });

    phpProcess.stdout.on('data', (data) => console.log(`PHP: ${data}`));
    phpProcess.stderr.on('data', (data) => console.log(`PHP ERR: ${data}`));
    
    if (mainWindow) {
        mainWindow.webContents.send('server-status', { 
            running: true, 
            port: serverPort, 
            ip: getLocalIp() 
        });
    }
}

async function createWindow() {
    await startLogServer();
    
    mainWindow = new BrowserWindow({
        width: 1050,
        height: 650,
        minWidth: 900,
        minHeight: 500,
        backgroundColor: '#111418',
        webPreferences: {
            nodeIntegration: true,
            contextIsolation: false
        },
        autoHideMenuBar: true,
        title: 'ArtiFrame Server Studio'
    });

    mainWindow.loadFile('index.html');
    
    mainWindow.webContents.on('did-finish-load', () => {
        startPhpServer();
    });
}

app.whenReady().then(createWindow);

app.on('window-all-closed', () => {
    if (phpProcess) phpProcess.kill();
    app.quit();
});

ipcMain.handle('open-external', (e, url) => {
    shell.openExternal(url);
});

ipcMain.handle('change-port', (e, newPort) => {
    serverPort = parseInt(newPort);
    startPhpServer();
});

ipcMain.handle('stop-server', () => {
    if (phpProcess) {
        phpProcess.kill();
        phpProcess = null;
    }
    if (mainWindow) {
        mainWindow.webContents.send('server-status', { running: false, port: serverPort, ip: getLocalIp() });
    }
});
