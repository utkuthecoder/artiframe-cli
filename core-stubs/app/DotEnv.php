<?php
/**
 * ArtiFrame Core Engine
 *
 * @package     ArtiFrame
 * @author      Artilingo
 * @license     AGPLv3 (Attribution-ShareAlike Required)
 * @link        https://artiframe.artilingo.com
 *
 * NOTICE: This file is part of the ArtiFrame ecosystem.
 * Any derivative works or patches MUST retain this original copyright notice
 * and remain open-source under the AGPLv3 license.
 */

namespace App;

class DotEnv
{
    /**
     * Belirtilen dizindeki .env dosyasını okur ve çevresel değişkenlere ekler.
     */
    public static function load(string $path): void
    {
        // 1. Yol içindeki ".." ve bağıl (relative) katmanları temizle
        $realPath = realpath($path);
        $targetPath = $realPath !== false ? $realPath : $path;

        // 2. Kontrolü temizlenmiş yol ($targetPath) üzerinden yap
        if (!file_exists($targetPath) || !is_readable($targetPath)) {
            return;
        }

        $lines = file($targetPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $line = trim($line);

            // Yorum satırlarını es geç
            if (str_starts_with($line, '#')) {
                continue;
            }

            // Atama içermeyen satırları atla
            if (!str_contains($line, '=')) {
                continue;
            }

            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            // Tırnak işaretlerini temizle
            $value = trim($value, '"\'');

            // Zaten tanımlı değilse aktar
            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}
