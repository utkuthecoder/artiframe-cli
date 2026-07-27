#!/usr/bin/env node

/**
 * ArtiFrame CLI — Node.js wrapper
 *
 * Handles:
 *  1. PHP 8.1+ availability check
 *  2. --version / -v flag: shows current + latest version, optional self-update
 *  3. Delegates all other args to the PHP bootstrap
 */

const { spawnSync } = require('child_process');
const path          = require('path');
const fs            = require('fs');
const https         = require('https');
const readline      = require('readline');

const PACKAGE_NAME    = '@artilingo/artiframe-cli';
const PKG             = require(path.join(__dirname, '..', 'package.json'));
const CURRENT_VERSION = PKG.version;

// ── ANSI colour helpers ─────────────────────────────────────────
const G = '\x1b[38;2;0;157;108m';  // ArtiFrame green
const W = '\x1b[1;37m';             // white bold
const D = '\x1b[38;2;100;100;100m'; // dim gray
const Y = '\x1b[38;5;220m';         // yellow
const R = '\x1b[0m';                 // reset

// ── Language detection (mirrors PHP App.php logic) ──────────────
function getLanguage() {
    for (const arg of process.argv.slice(2)) {
        if (arg.startsWith('--lang=')) {
            const code = arg.slice(7, 9).toLowerCase();
            if (['tr', 'en', 'de', 'fr', 'es'].includes(code)) return code;
        }
    }
    const home       = process.env.HOME || process.env.USERPROFILE;
    const configFile = home ? path.join(home, '.artiframe_lang') : null;
    if (configFile && fs.existsSync(configFile)) {
        const lang = fs.readFileSync(configFile, 'utf8').trim();
        if (['tr', 'en', 'de', 'fr', 'es'].includes(lang)) return lang;
    }
    return 'en';
}

// ── Inline translations for the version/update screen ───────────
const MESSAGES = {
    tr: {
        currentVersion : 'Mevcut sürüm   ',
        latestVersion  : 'Son sürüm      ',
        checking       : 'Güncelleme kontrol ediliyor...',
        upToDate       : '✅  Güncel — Yeni sürüm bulunmuyor.',
        updateAvail    : '🔔  Güncelleme mevcut!',
        updatePrompt   : 'Şimdi güncellemek ister misiniz? [e/H]: ',
        yesKeys        : ['e', 'y'],
        updating       : '📦  Güncelleniyor...',
        updateSuccess  : '✅  Güncelleme tamamlandı! Terminali yeniden başlatın.',
        updateCancelled: 'Güncelleme iptal edildi.',
        updateFailed   : '❌  Güncelleme başarısız oldu.',
        networkError   : '⚠️   Sürüm kontrolü yapılamadı (ağ hatası).',
    },
    en: {
        currentVersion : 'Current version',
        latestVersion  : 'Latest version ',
        checking       : 'Checking for updates...',
        upToDate       : '✅  Up to date — No new version available.',
        updateAvail    : '🔔  Update available!',
        updatePrompt   : 'Would you like to update now? [y/N]: ',
        yesKeys        : ['y'],
        updating       : '📦  Updating...',
        updateSuccess  : '✅  Update complete! Please restart your terminal.',
        updateCancelled: 'Update cancelled.',
        updateFailed   : '❌  Update failed.',
        networkError   : '⚠️   Could not check for updates (network error).',
    },
    de: {
        currentVersion : 'Aktuelle Version',
        latestVersion  : 'Neueste Version ',
        checking       : 'Nach Updates suchen...',
        upToDate       : '✅  Aktuell — Keine neue Version verfügbar.',
        updateAvail    : '🔔  Update verfügbar!',
        updatePrompt   : 'Möchten Sie jetzt aktualisieren? [j/N]: ',
        yesKeys        : ['j', 'y'],
        updating       : '📦  Wird aktualisiert...',
        updateSuccess  : '✅  Update abgeschlossen! Bitte Terminal neu starten.',
        updateCancelled: 'Update abgebrochen.',
        updateFailed   : '❌  Update fehlgeschlagen.',
        networkError   : '⚠️   Keine Verbindung zum Update-Server (Netzwerkfehler).',
    },
    fr: {
        currentVersion : 'Version actuelle',
        latestVersion  : 'Dernière version',
        checking       : 'Vérification des mises à jour...',
        upToDate       : '✅  À jour — Aucune nouvelle version disponible.',
        updateAvail    : '🔔  Mise à jour disponible !',
        updatePrompt   : 'Mettre à jour maintenant ? [o/N] : ',
        yesKeys        : ['o', 'y'],
        updating       : '📦  Mise à jour en cours...',
        updateSuccess  : '✅  Mise à jour terminée ! Veuillez redémarrer votre terminal.',
        updateCancelled: 'Mise à jour annulée.',
        updateFailed   : '❌  Échec de la mise à jour.',
        networkError   : '⚠️   Impossible de vérifier les mises à jour (erreur réseau).',
    },
    es: {
        currentVersion : 'Versión actual  ',
        latestVersion  : 'Última versión  ',
        checking       : 'Verificando actualizaciones...',
        upToDate       : '✅  Al día — No hay nueva versión disponible.',
        updateAvail    : '🔔  ¡Actualización disponible!',
        updatePrompt   : '¿Actualizar ahora? [s/N]: ',
        yesKeys        : ['s', 'y'],
        updating       : '📦  Actualizando...',
        updateSuccess  : '✅  ¡Actualización completa! Por favor reinicie su terminal.',
        updateCancelled: 'Actualización cancelada.',
        updateFailed   : '❌  Error en la actualización.',
        networkError   : '⚠️   No se pudo verificar las actualizaciones (error de red).',
    },
};

// ── npm registry fetch ──────────────────────────────────────────
function fetchLatestVersion() {
    return new Promise((resolve, reject) => {
        const req = https.request({
            hostname: 'registry.npmjs.org',
            path    : '/' + encodeURIComponent(PACKAGE_NAME) + '/latest',
            method  : 'GET',
            headers : { Accept: 'application/json' },
            timeout : 6000,
        }, (res) => {
            let raw = '';
            res.on('data', c => raw += c);
            res.on('end', () => {
                try { resolve(JSON.parse(raw).version); }
                catch { reject(new Error('parse')); }
            });
        });
        req.on('timeout', () => { req.destroy(); reject(new Error('timeout')); });
        req.on('error', reject);
        req.end();
    });
}

function compareVersions(a, b) {
    const pa = a.split('.').map(Number);
    const pb = b.split('.').map(Number);
    for (let i = 0; i < 3; i++) {
        if (pa[i] > pb[i]) return  1;
        if (pa[i] < pb[i]) return -1;
    }
    return 0;
}

// ── --version handler ───────────────────────────────────────────
async function handleVersion() {
    const lang = getLanguage();
    const m    = MESSAGES[lang] || MESSAGES.en;

    console.log();
    console.log(`  ${W}${m.currentVersion}:${R}  ${G}v${CURRENT_VERSION}${R}`);
    process.stdout.write(`  ${D}${m.checking}${R}`);

    let latest;
    try {
        latest = await fetchLatestVersion();
    } catch {
        console.log();
        console.log(`  ${Y}${m.networkError}${R}`);
        console.log();
        process.exit(0);
    }

    // Clear the "checking..." line by overwriting with spaces then reprint
    process.stdout.write('\r' + ' '.repeat(60) + '\r');
    console.log(`  ${W}${m.latestVersion}:${R}  ${G}v${latest}${R}`);
    console.log();

    if (compareVersions(latest, CURRENT_VERSION) <= 0) {
        console.log(`  ${m.upToDate}`);
        console.log();
        process.exit(0);
    }

    // ── Update available ─────────────────────────────────────────
    console.log(`  ${Y}${m.updateAvail}${R}  ${D}v${CURRENT_VERSION}${R} → ${G}v${latest}${R}`);
    console.log();
    process.stdout.write(`  ${m.updatePrompt}`);

    const rl = readline.createInterface({ input: process.stdin, terminal: false });

    rl.once('line', (answer) => {
        rl.close();
        const yes = m.yesKeys.includes(answer.trim().toLowerCase());

        if (!yes) {
            console.log();
            console.log(`  ${D}${m.updateCancelled}${R}`);
            console.log();
            process.exit(0);
        }

        console.log();
        console.log(`  ${m.updating}`);
        console.log();

        const result = spawnSync('npm', ['install', '-g', PACKAGE_NAME + '@latest', '--prefer-online'], {
            stdio: 'inherit',
            shell: true,
        });

        console.log();
        if (result.status === 0) {
            console.log(`  ${G}${m.updateSuccess}${R}`);
        } else {
            console.log(`  ${m.updateFailed}`);
        }
        console.log();
        process.exit(result.status ?? 1);
    });
}

// ── PHP availability check ──────────────────────────────────────
function findPhp() {
    const bins = ['php', 'php8', 'php8.1', 'php8.2', 'php8.3', 'php8.4', 'php81', 'php82', 'php83', 'php84'];
    for (const bin of bins) {
        const r = spawnSync(bin, ['--version'], { encoding: 'utf8', shell: false });
        if (r.status === 0) return bin;
    }
    return null;
}

// ── Entry point ─────────────────────────────────────────────────
const args = process.argv.slice(2);

if (args.includes('--version') || args.includes('-v')) {
    handleVersion().catch(() => process.exit(1));
} else {
    // Normal PHP delegation
    const php = findPhp();

    if (!php) {
        const lang = getLanguage();
        console.error(`\n\x1b[1;31m[ArtiFrame] PHP Not Found!\x1b[0m`);
        console.error(`\x1b[33mArtiFrame CLI requires PHP 8.1 or higher.\x1b[0m\n`);
        console.error('Install PHP:');
        console.error('  Windows : https://windows.php.net/download/');
        console.error('  macOS   : brew install php');
        console.error('  Linux   : sudo apt install php8.2-cli\n');
        process.exit(1);
    }

    const vr = spawnSync(php, ['-r', 'echo PHP_MAJOR_VERSION . "." . PHP_MINOR_VERSION;'], {
        encoding: 'utf8', shell: false,
    });
    const [major, minor] = (vr.stdout || '0.0').split('.').map(Number);
    if (major < 8 || (major === 8 && minor < 1)) {
        console.error(`\n\x1b[1;31m[ArtiFrame] PHP version too old!\x1b[0m`);
        console.error(`\x1b[33mFound PHP ${major}.${minor} — ArtiFrame requires PHP 8.1+\x1b[0m\n`);
        process.exit(1);
    }

    const phpScript = path.join(__dirname, 'artiframe.php');
    const result    = spawnSync(php, [phpScript, ...args], {
        stdio: 'inherit',
        shell: false,
    });
    process.exit(result.status ?? 1);
}
