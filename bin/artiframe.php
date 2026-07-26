#!/usr/bin/env php
<?php
/**
 * ArtiFrame CLI
 * 
 * @package     ArtiFrame
 * @author      Artilingo
 * @license     AGPLv3 (Attribution-ShareAlike Required)
 * @link        https://artiframe.artilingo.com
 */

// Define constants
define('ARTIFRAME_CLI_ROOT', dirname(__DIR__));
// Read version dynamically from package.json
$_pkgFile = dirname(__DIR__) . '/package.json';
$_pkgVersion = '?.?.?';
if (file_exists($_pkgFile)) {
    $_pkg = json_decode(file_get_contents($_pkgFile), true);
    if (!empty($_pkg['version'])) {
        $_pkgVersion = $_pkg['version'];
    }
}
define('ARTIFRAME_VERSION', $_pkgVersion);
unset($_pkgFile, $_pkg, $_pkgVersion);

// Register a simple autoloader for the CLI's own classes
spl_autoload_register(function ($class) {
    // Prefix for CLI classes
    $prefix = 'ArtiFrame\\Cli\\';
    $base_dir = ARTIFRAME_CLI_ROOT . '/src/';

    // Does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return; // no, move to the next registered autoloader
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

use ArtiFrame\Cli\App;

// Bootstrap and run the application
try {
    $app = new App($argv);
    $app->run();
} catch (\Exception $e) {
    echo "[ERROR] " . $e->getMessage() . PHP_EOL;
    exit(1);
}
