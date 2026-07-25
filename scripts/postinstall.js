/**
 * ArtiFrame CLI — postinstall script
 *
 * Sets the execute permission on bin/artiframe.php on Unix-like systems.
 * Silently skipped on Windows (not needed).
 */

const fs   = require('fs');
const path = require('path');

if (process.platform === 'win32') {
    process.exit(0);
}

const target = path.join(__dirname, '..', 'bin', 'artiframe.php');

try {
    fs.chmodSync(target, 0o755);
} catch (e) {
    // Non-fatal — if permissions fail (e.g. read-only fs), skip silently.
}
