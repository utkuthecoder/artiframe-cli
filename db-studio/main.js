const { app, BrowserWindow, ipcMain } = require('electron');
const path = require('path');
const fs = require('fs');
const mysql = require('mysql2/promise');
const dotenv = require('dotenv');

let mainWindow;
let projectRoot = '';
let appMode = 'live'; // 'live' or 'offline'

// Parse arguments
process.argv.forEach(arg => {
    if (arg.startsWith('--mode=')) {
        appMode = arg.split('=')[1];
    } else if (arg !== '.' && arg !== '..' && !arg.includes('node_modules') && !arg.includes('electron') && !arg.endsWith('.js') && !arg.startsWith('--')) {
        // Assume the last non-flag, non-executable argument is the project root
        projectRoot = arg;
    }
});
if (!projectRoot) projectRoot = process.cwd();

// DEBUG LOGGING
fs.writeFileSync(path.join(__dirname, 'debug.log'), JSON.stringify(process.argv) + '\nappMode:' + appMode + '\nprojectRoot:' + projectRoot);


const envPath = path.join(projectRoot, '.env');
let schemaPath = path.join(projectRoot, 'schema.sql');

if (appMode === 'live') {
    dotenv.config({ path: envPath });
}


async function createConnection() {
    if (appMode === 'offline') {
        throw new Error('Offline modda veritabanı bağlantısı kapalıdır.');
    }
    return await mysql.createConnection({
        host: process.env.DB_HOST || 'localhost',
        user: process.env.DB_USER || 'root',
        password: process.env.DB_PASSWORD || process.env.DB_PASS || '',
        database: process.env.DB_NAME || '',
        port: process.env.DB_PORT || 3306,
        multipleStatements: true
    });
}

function createWindow() {
    mainWindow = new BrowserWindow({
        width: 1400,
        height: 900,
        title: 'ArtiFrame DB Studio',
        webPreferences: {
            preload: path.join(__dirname, 'preload.js'),
            contextIsolation: true,
            nodeIntegration: false
        }
    });
    mainWindow.removeMenu();
    if (appMode === 'offline') {
        mainWindow.loadFile(path.join(__dirname, 'launcher.html'));
    } else {
        mainWindow.loadFile(path.join(__dirname, 'index.html'));
    }
    mainWindow.webContents.on('did-finish-load', () => {
        mainWindow.webContents.send('set-app-mode', { mode: appMode, projectRoot });
    });
}


const crypto = require('crypto');
const artiSecretKey = crypto.createHash('sha256').update('ArtiFrameDBStudio_TopSecret_v1').digest();
const artiIV = crypto.createHash('md5').update('ArtiFrameDBStudio_IV_v1').digest();

ipcMain.handle('save-artiframe-file', async (event, globalDbData) => {
    const { dialog } = require('electron');
    const result = await dialog.showSaveDialog(mainWindow, {
        title: 'Dışa Aktar (.artiframe)',
        defaultPath: 'schema.artiframe',
        filters: [{ name: 'ArtiFrame DB', extensions: ['artiframe', 'arti'] }]
    });
    
    if (result.canceled || !result.filePath) return false;

    const artiData = {
        metadata: { version: "2.0", type: "ArtiFrameDBDiagram", exportedAt: new Date().toISOString() },
        schema: globalDbData
    };

    const jsonStr = JSON.stringify(artiData);
    const cipher = crypto.createCipheriv('aes-256-cbc', artiSecretKey, artiIV);
    
    const magic = Buffer.from('ARTIFRAME\0');
    const encrypted = Buffer.concat([cipher.update(jsonStr, 'utf8'), cipher.final()]);
    
    fs.writeFileSync(result.filePath, Buffer.concat([magic, encrypted]));
    return true;
});

ipcMain.handle('load-artiframe-file', async () => {
    const { dialog } = require('electron');
    const result = await dialog.showOpenDialog(mainWindow, {
        title: 'İçe Aktar (.artiframe)',
        filters: [{ name: 'ArtiFrame DB', extensions: ['artiframe', 'arti'] }],
        properties: ['openFile']
    });
    
    if (result.canceled || result.filePaths.length === 0) return null;
    
    const filePath = result.filePaths[0];
    const buffer = fs.readFileSync(filePath);
    
    const magic = buffer.subarray(0, 10);
    if (magic.toString() !== 'ARTIFRAME\0') {
        return { error: 'Geçersiz veya bozuk .artiframe dosyası! Sadece ArtiFrame DB Studio tarafından üretilmiş orjinal dosyalar açılabilir.' };
    }
    
    const encrypted = buffer.subarray(10);
    const decipher = crypto.createDecipheriv('aes-256-cbc', artiSecretKey, artiIV);
    
    try {
        const decrypted = Buffer.concat([decipher.update(encrypted), decipher.final()]);
        const json = JSON.parse(decrypted.toString('utf8'));
        if (json.metadata?.type !== "ArtiFrameDBDiagram") {
            return { error: 'Geçersiz dosya içeriği!' };
        }
        return { success: true, data: json };
    } catch (e) {
        return { error: 'Dosyanın şifresi çözülemedi. Dosya bozuk veya farklı bir sürümden alınmış olabilir.' };
    }
});


app.whenReady().then(createWindow);
app.on('window-all-closed', () => { if (process.platform !== 'darwin') app.quit(); });

function parseSqlToJson(sql) {
    const tables = [];
    const relationships = [];
    
    const createTableRegex = /CREATE TABLE (?:IF NOT EXISTS )?\`([^\`]+)\` \(([\s\S]*?)\).*?;/g;
    let match;
    while ((match = createTableRegex.exec(sql)) !== null) {
        const rawSql = match[0];
        const tableName = match[1];
        const body = match[2];
        const columns = [];
        const pks = new Set();
        const uks = new Set();
        const fks = [];
        const indexes = [];
        
        const lines = body.split('\n').map(l => l.trim()).filter(l => l.length > 0);
        for (const line of lines) {
            if (line.startsWith('PRIMARY KEY')) {
                const pkMatch = line.match(/\((.*?)\)/);
                if (pkMatch) {
                    pkMatch[1].split(',').forEach(k => pks.add(k.replace(/`/g, '').trim()));
                }
            } else if (line.startsWith('CONSTRAINT') || line.includes('FOREIGN KEY')) {
                const fkMatch = line.match(/(?:CONSTRAINT \`([^\`]+)\`\s+)?FOREIGN KEY \(\`([^\`]+)\`\) REFERENCES \`([^\`]+)\` \(\`([^\`]+)\`\)(?:\s+ON DELETE (CASCADE|RESTRICT|SET NULL|NO ACTION))?(?:\s+ON UPDATE (CASCADE|RESTRICT|SET NULL|NO ACTION))?/i);
                if (fkMatch) {
                    const constraintName = fkMatch[1] || `fk_${tableName}_${fkMatch[2]}`;
                    fks.push({
                        name: constraintName,
                        localCol: fkMatch[2],
                        refTable: fkMatch[3],
                        refCol: fkMatch[4],
                        onDelete: (fkMatch[5] || 'RESTRICT').toUpperCase(),
                        onUpdate: (fkMatch[6] || 'RESTRICT').toUpperCase()
                    });
                    relationships.push({fromTable: tableName, fromCol: fkMatch[2], toTable: fkMatch[3], toCol: fkMatch[4]});
                }
            } else if (line.match(/^(UNIQUE\s+)?(?:KEY|INDEX)\s+\`([^\`]+)\`\s+\((.+)\)/i)) {
                const idxMatch = line.match(/^(UNIQUE\s+)?(?:KEY|INDEX)\s+\`([^\`]+)\`\s+\((.+)\)/i);
                if(idxMatch) {
                    indexes.push({
                        isUnique: !!idxMatch[1],
                        name: idxMatch[2],
                        col: idxMatch[3].replace(/[\` ]/g, '')
                    });
                    if (!!idxMatch[1]) idxMatch[3].split(',').forEach(k => uks.add(k.replace(/[\` ]/g, '')));
                }
            }
        }
        for (const line of lines) {
            if (line.startsWith('`')) {
                const colMatch = line.match(/^\`([^\`]+)\`\s+([a-zA-Z0-9_\(\)]+)/);
                if (colMatch) {
                    columns.push({
                        name: colMatch[1],
                        type: colMatch[2],
                        isPk: pks.has(colMatch[1]),
                        isUk: uks.has(colMatch[1]),
                        isAi: line.includes('AUTO_INCREMENT'),
                        isNotNull: line.includes('NOT NULL')
                    });
                }
            }
        }
        const filteredIndexes = indexes.filter(idx => !fks.some(fk => fk.name === idx.name || (!idx.isUnique && fk.localCol === idx.col)));
        tables.push({ name: tableName, columns, rawSql, fks, indexes: filteredIndexes });
    }
    return { tables, relationships };
}

async function syncDbToFile() {
    let conn;
    try {
        conn = await createConnection();
        const [tablesRows] = await conn.query('SHOW TABLES');
        let schemaContent = "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\nSTART TRANSACTION;\nSET time_zone = \"+00:00\";\n\n";

        for (const row of tablesRows) {
            const tableName = Object.values(row)[0];
            const [createRows] = await conn.query(`SHOW CREATE TABLE \`${tableName}\``);
            let createSql = createRows[0]['Create Table'];
            createSql = createSql.replace(/ AUTO_INCREMENT=\d+/g, '');
            schemaContent += `-- Tablo: ${tableName}\n`;
            schemaContent += createSql + ";\n\n";
        }
        schemaContent += "COMMIT;\n";
        fs.writeFileSync(schemaPath, schemaContent);
        return schemaContent;
    } finally {
        if (conn) await conn.end();
    }
}




ipcMain.handle('get-table-data', async (event, tableName, limit = 50, offset = 0) => {
    let conn;
    try {
        conn = await createConnection();
        const [rows] = await conn.query(`SELECT * FROM \`${tableName}\` LIMIT ${Number(limit)} OFFSET ${Number(offset)}`);
        const [countRes] = await conn.query(`SELECT COUNT(*) as total FROM \`${tableName}\``);
        return { success: true, data: rows, total: countRes[0].total };
    } catch (error) {
        return { success: false, error: error.message };
    } finally {
        if (conn) await conn.end();
    }
});

ipcMain.handle('update-row', async (event, tableName, pkCol, pkVal, updateData) => {
    let conn;
    try {
        conn = await createConnection();
        const keys = Object.keys(updateData);
        if(keys.length === 0) return { success: true };
        
        const setClause = keys.map(k => `\`${k}\` = ?`).join(', ');
        const values = Object.values(updateData);
        values.push(pkVal);
        
        await conn.query(`UPDATE \`${tableName}\` SET ${setClause} WHERE \`${pkCol}\` = ?`, values);
        return { success: true };
    } catch (error) {
        return { success: false, error: error.message };
    } finally {
        if (conn) await conn.end();
    }
});

ipcMain.handle('delete-row', async (event, tableName, pkCol, pkVal) => {
    let conn;
    try {
        conn = await createConnection();
        await conn.query(`DELETE FROM \`${tableName}\` WHERE \`${pkCol}\` = ?`, [pkVal]);
        return { success: true };
    } catch (error) {
        return { success: false, error: error.message };
    } finally {
        if (conn) await conn.end();
    }
});

ipcMain.handle('seed-table', async (event, tableName, columns, count) => {
    const { faker } = await import('@faker-js/faker');
    let conn;
    try {
        conn = await createConnection();
        
        // 1. Find Foreign Keys for this table
        const [fkRows] = await conn.query(`
            SELECT COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME 
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL
        `, [tableName]);
        
        // 2. Fetch valid values for each FK
        const fkValues = {};
        for (const fk of fkRows) {
            const [refRows] = await conn.query(`SELECT \`${fk.REFERENCED_COLUMN_NAME}\` AS val FROM \`${fk.REFERENCED_TABLE_NAME}\``);
            if (refRows.length === 0) {
                return { success: false, error: `Bağlantılı tablo olan '${fk.REFERENCED_TABLE_NAME}' tablosu boş. FK hatası almamak için önce o tabloya sahte veri eklemelisiniz!` };
            }
            fkValues[fk.COLUMN_NAME] = refRows.map(r => r.val);
        }

        let inserted = 0;
        for(let i = 0; i < count; i++) {
            const insertData = {};
            for(const col of columns) {
                if(col.isAi) continue; // Skip auto_increment
                
                if (fkValues[col.name]) {
                    // Pick a random valid Foreign Key value
                    const vals = fkValues[col.name];
                    insertData[col.name] = vals[Math.floor(Math.random() * vals.length)];
                    continue;
                }
                
                const type = col.type.toLowerCase();
                const name = col.name.toLowerCase();
                
                let val = null;
                if(name.includes('email')) val = faker.internet.email();
                else if(name.includes('name') || name.includes('isim')) val = faker.person.fullName();
                else if(name.includes('password') || name.includes('sifre')) val = faker.internet.password();
                else if(type.includes('int')) val = faker.number.int({min:1, max:100});
                else if(type.includes('varchar') || type.includes('text')) val = faker.lorem.words(3);
                else if(type.includes('date') || type.includes('time') || type.includes('timestamp')) val = faker.date.recent();
                else if(type.includes('bool') || type.includes('tinyint')) val = faker.datatype.boolean() ? 1 : 0;
                else val = faker.string.alphanumeric(10);
                
                insertData[col.name] = val;
            }
            
            const keys = Object.keys(insertData);
            if(keys.length > 0) {
                const cols = keys.map(k => `\`${k}\``).join(', ');
                const placeholders = keys.map(() => '?').join(', ');
                const values = Object.values(insertData);
                await conn.query(`INSERT INTO \`${tableName}\` (${cols}) VALUES (${placeholders})`, values);
                inserted++;
            }
        }
        return { success: true, message: `${inserted} sahte veri başarıyla eklendi.` };
    } catch (error) {
        return { success: false, error: error.message };
    } finally {
        if (conn) await conn.end();
    }
});

ipcMain.handle('get-schema', async () => {
    if (appMode === 'preview' && global.previewSchemaData) {
        return { schema: global.previewSchemaData };
    }
    
    let schemaStr = '';
    try {
        // ALWAYS sync from live DB so PhpMyAdmin changes reflect instantly
        schemaStr = await syncDbToFile();
    } catch (e) {
        if (!fs.existsSync(schemaPath)) return { error: 'Veritabanı bağlantısı kurulamadı. Hata: ' + e.message };
        schemaStr = fs.readFileSync(schemaPath, 'utf8');
    }
    
    const parsed = parseSqlToJson(schemaStr);
    
    // Fetch live stats
    let conn;
    try {
        conn = await createConnection();
        const [stats] = await conn.query(`
            SELECT TABLE_NAME, (DATA_LENGTH + INDEX_LENGTH) AS SIZE_BYTES
            FROM information_schema.TABLES 
            WHERE TABLE_SCHEMA = DATABASE()
        `);
        const statsMap = {};
        stats.forEach(s => statsMap[s.TABLE_NAME] = { sizeBytes: s.SIZE_BYTES || 0 });
        
        for (const t of parsed.tables) {
            t.stats = statsMap[t.name] || { sizeBytes: 0 };
            try {
                const [countRes] = await conn.query(`SELECT COUNT(*) as c FROM \`${t.name}\``);
                t.stats.rows = countRes[0].c;
            } catch(e) {
                t.stats.rows = 0;
            }
        }
    } catch(e) {
        // ignore if stats fail
    } finally {
        if(conn) await conn.end();
    }
    
    
        let layoutData = {};
        if (projectRoot) {
            const layoutPath = path.join(projectRoot, '.artiframe_layout.json');
            if (fs.existsSync(layoutPath)) {
                try { layoutData = JSON.parse(fs.readFileSync(layoutPath, 'utf8')); } catch(e){}
            }
        }
        parsed.tables.forEach(t => {
            if (layoutData[t.name]) t.ui = layoutData[t.name];
        });
        
        return { json: parsed };
});

ipcMain.handle('save-layout', (event, layoutData) => {
    if (!projectRoot || appMode !== 'live') return { success: false, error: 'Sadece aktif projelerde dizilim kaydedilebilir.' };
    try {
        const layoutPath = path.join(projectRoot, '.artiframe_layout.json');
        fs.writeFileSync(layoutPath, JSON.stringify(layoutData, null, 2), 'utf8');
        return { success: true };
    } catch (err) {
        return { success: false, error: err.message };
    }
});

ipcMain.handle('execute-sql', async (event, sqlQuery) => {
    let conn;
    try {
        conn = await createConnection();
        await conn.query('SET FOREIGN_KEY_CHECKS=0;');
        const [result] = await conn.query(sqlQuery);
        await conn.query('SET FOREIGN_KEY_CHECKS=1;');
        const newSchemaStr = await syncDbToFile();
        return { success: true, message: 'Senkronize edildi.', json: parseSqlToJson(newSchemaStr), data: Array.isArray(result) ? result : null };
    } catch (error) {
        return { success: false, error: error.message };
    } finally {
        if (conn) await conn.end();
    }
});


ipcMain.handle('get-app-mode', () => ({ mode: appMode, projectRoot }));


ipcMain.handle('get-workspace', () => {
    try {
        if (fs.existsSync(workspaceConfigPath)) {
            return fs.readFileSync(workspaceConfigPath, 'utf8').trim();
        }
    } catch(e) {}
    return null;
});

ipcMain.handle('set-workspace', (event, dirPath) => {
    try {
        fs.writeFileSync(workspaceConfigPath, dirPath, 'utf8');
        return true;
    } catch(e) {
        return false;
    }
});

ipcMain.handle('select-directory', async () => {
    const { dialog } = require('electron');
    const result = await dialog.showOpenDialog(mainWindow, {
        properties: ['openDirectory']
    });
    if (!result.canceled && result.filePaths.length > 0) {
        return result.filePaths[0];
    }
    return null;
});

ipcMain.handle('get-projects', async (event, workspacePath) => {
    try {
        const dirs = fs.readdirSync(workspacePath, { withFileTypes: true })
            .filter(dirent => dirent.isDirectory())
            .map(dirent => dirent.name);
            
        // Filter projects that have .env or are valid artiframe projects
        const validProjects = [];
        for (const dir of dirs) {
            // Let's just return all directories for now so the user can select their project even if it's missing .env temporarily
            validProjects.push(dir);
        }
        return validProjects;
    } catch(e) {
        return [];
    }
});

ipcMain.handle('open-project', (event, pPath) => {
    appMode = 'live';
    projectRoot = pPath;
    
    const envPath = path.join(projectRoot, '.env');
    dotenv.config({ path: envPath });
    
    schemaPath = path.join(projectRoot, 'schema.sql');
    
    mainWindow.loadFile(path.join(__dirname, 'index.html'));
});



ipcMain.handle('preview-schema-file', (event, schemaJson) => {
    appMode = 'preview';
    global.previewSchemaData = schemaJson; // Hacky but works for transferring
    mainWindow.loadFile(path.join(__dirname, 'index.html'));
});


ipcMain.handle('restart-offline', async () => {
    app.relaunch({ args: process.argv.slice(1).filter(a => !a.includes('--mode')).concat(['--mode=offline']) });
    app.exit(0);
});

ipcMain.handle('scaffold-project', async (event, dirName, schemaJson, customWorkspace = null) => {
    return new Promise((resolve) => {
        const { spawn } = require('child_process');
        const rootPath = customWorkspace || projectRoot;
        const targetPath = path.join(rootPath, dirName);
        
        // Find cli php script path
        const cliPath = path.resolve(__dirname, '../bin/artiframe.php');
        
        const child = spawn('php', [cliPath, 'new', dirName], {
            cwd: rootPath,
            stdio: ['pipe', 'pipe', 'pipe']
        });
        
        let out = '';
        child.stdout.on('data', (data) => {
            const str = data.toString();
            out += str;
            // Answer prompts automatically with defaults (Enter key)
            if (str.includes('?')) {
                child.stdin.write('\n');
            }
        });
        
        child.stderr.on('data', (data) => {
            console.error(data.toString());
        });
        
        child.on('close', (code) => {
            if (code === 0) {
                // Generate schema.sql inside the new project
                let sqlScript = "/* ArtiFrame DB Studio - Otomatik Üretilmiş Şema */\n\n";
                if (schemaJson && schemaJson.tables) {
                    schemaJson.tables.forEach(t => {
                        let cols = [];
                        let pks = [];
                        t.columns.forEach(c => {
                            let def = `\`${c.name}\` ${c.type}`;
                            if(!c.nullable) def += ' NOT NULL';
                            if(c.isAi) def += ' AUTO_INCREMENT';
                            cols.push(def);
                            if(c.isPk) pks.push(`\`${c.name}\``);
                        });
                        if(pks.length > 0) cols.push(`PRIMARY KEY (${pks.join(',')})`);
                        sqlScript += `CREATE TABLE \`${t.name}\` (\n  ${cols.join(',\n  ')}\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n\n`;
                    });
                }
                
                const schemaFilePath = path.join(targetPath, 'schema.sql');
                fs.writeFileSync(schemaFilePath, sqlScript, 'utf8');
                
                resolve({ success: true, path: targetPath });
            } else {
                resolve({ success: false, error: 'Proje oluşturulurken CLI tarafında bir hata oluştu.' });
            }
        });
    });
});
