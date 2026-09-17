/**
 * ArtiFrame CLI — postinstall script
 *
 * Sets the execute permission on bin/artiframe.php on Unix-like systems.
 * Creates the default global Workspace directory (C:\ArtiFrame or ~/ArtiFrame).
 */

const fs   = require('fs');
const path = require('path');
const os   = require('os');

// 1. CHMOD for Unix
const target = path.join(__dirname, '..', 'bin', 'artiframe.php');
if (process.platform !== 'win32') {
    try {
        fs.chmodSync(target, 0o755);
    } catch (e) {}
}

// 2. Create Default Workspace
const defaultWorkspace = process.platform === 'win32' 
    ? 'C:\\ArtiFrame' 
    : path.join(os.homedir(), 'ArtiFrame');

if (!fs.existsSync(defaultWorkspace)) {
    try {
        fs.mkdirSync(defaultWorkspace, { recursive: true });
        
        // Put a nice greeting file
        const greeting = `ArtiFrame Workspace\n===================\nWelcome to the ArtiFrame ecosystem!\n\nAll your DevOps Studio projects will be created and managed here.\nTo start the studio, simply run:\n\n> artiframe devops\n`;
        fs.writeFileSync(path.join(defaultWorkspace, 'README.txt'), greeting);
        
        console.log('\n\x1b[32m%s\x1b[0m', '✅ ArtiFrame workspace created at: ' + defaultWorkspace);
        console.log('\x1b[36m%s\x1b[0m', '   Run `artiframe devops` to launch the studio!\n');
    } catch (e) {
        // Silently fail if no permissions
    }
} else {
    console.log('\n\x1b[36m%s\x1b[0m', '✅ ArtiFrame workspace ready at: ' + defaultWorkspace);
    console.log('\x1b[36m%s\x1b[0m', '   Run `artiframe devops` to launch the studio!\n');
}


// 3. Install dependencies for studios
const { execSync } = require('child_process');

['devops-studio', 'db-studio', 'server-studio'].forEach(studio => {
    const studioPath = path.join(__dirname, '..', studio);
    if (fs.existsSync(studioPath)) {
        console.log('\x1b[36m%s\x1b[0m', `📦 Installing dependencies for ${studio}...`);
        try {
            execSync('npm install --production', { cwd: studioPath, stdio: 'ignore' });
        } catch (e) {
            console.log('\x1b[31m%s\x1b[0m', `❌ Failed to install dependencies for ${studio}`);
        }
    }
});

// 4. Create Desktop Shortcuts for GUI
try {
    if (process.platform === 'linux') {
        const appDir = path.join(os.homedir(), '.local', 'share', 'applications');
        if (fs.existsSync(appDir)) {
            const desktopFile = path.join(appDir, 'artiframe-devops.desktop');
            const content = `[Desktop Entry]
Name=ArtiFrame DevOps
Comment=Launch ArtiFrame DevOps Studio
Exec=bash -ic "artiframe devops"
Icon=utilities-terminal
Terminal=false
Type=Application
Categories=Development;
`;
            fs.writeFileSync(desktopFile, content);
            fs.chmodSync(desktopFile, 0o755);
            console.log('\x1b[36m%s\x1b[0m', '✅ Application menu shortcut created (Linux).');
        }
    } else if (process.platform === 'win32') {
        const { execSync } = require('child_process');
        
        // Create a silent VBS runner in Workspace to prevent CMD window pop-up
        const runnerVbs = path.join(defaultWorkspace, 'run-devops.vbs');
        const runnerCode = `Set WshShell = CreateObject("WScript.Shell")\nWshShell.Run "cmd.exe /c artiframe devops", 0, False`;
        fs.writeFileSync(runnerVbs, runnerCode);
        
        // Generate the .lnk shortcut on the Desktop pointing to the silent runner
        const desktopPath = path.join(os.homedir(), 'Desktop', 'ArtiFrame DevOps.lnk');
        const vbsPath = path.join(os.tmpdir(), 'create_shortcut.vbs');
        
        const vbsCode = `
Set ws = WScript.CreateObject("WScript.Shell")
Set link = ws.CreateShortcut("${desktopPath}")
link.TargetPath = "wscript.exe"
link.Arguments = """${runnerVbs}"""
link.Description = "ArtiFrame DevOps Studio"
link.IconLocation = "%SystemRoot%\\System32\\SHELL32.dll,27"
link.Save
        `;
        fs.writeFileSync(vbsPath, vbsCode);
        execSync(`cscript //nologo "${vbsPath}"`);
        console.log('\x1b[36m%s\x1b[0m', '✅ Desktop shortcut created (Windows).');
    }
} catch (e) {
    console.log('\x1b[33m%s\x1b[0m', '⚠️ Could not create desktop shortcut. You can still run via terminal.');
}
