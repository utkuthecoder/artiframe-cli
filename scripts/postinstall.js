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
