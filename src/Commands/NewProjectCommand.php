<?php
namespace ArtiFrame\Cli\Commands;

use ArtiFrame\Cli\Services\Translator;
use ArtiFrame\Cli\Services\Safeguard;

class NewProjectCommand
{
    private Translator $translator;
    private Safeguard  $safeguard;

    // İlerleme takibi
    private int $totalSteps  = 0;
    private int $currentStep = 0;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
        $this->safeguard  = new Safeguard($translator);
    }

    public function execute(array $args): void
    {
        $targetPath = $args[0] ?? null;

        if (!$targetPath) {
            echo $this->translator->get('DIR_REQUIRED_ERROR') . PHP_EOL;
            return;
        }

        // 1. Dizin çözümleme
        $targetDir   = $this->resolvePath($targetPath);
        $projectName = basename($targetDir);

        // 2. Safeguard Kontrolü
        if (!$this->safeguard->checkTarget($targetDir)) {
            return;
        }

        // 3. Composer Kontrolü
        if (!$this->isComposerInstalled()) {
            echo PHP_EOL . "❌ " . $this->translator->get('COMPOSER_MISSING_TITLE') . PHP_EOL;
            echo str_repeat("-", 50) . PHP_EOL;
            echo $this->translator->get('COMPOSER_MISSING_BODY') . PHP_EOL;
            return;
        }

        // ── Başlık Ekranı ────────────────────────────────────
        echo PHP_EOL;
        $builderTitle = $this->translator->get('PROJECT_BUILDER_TITLE');
        echo "  ╔══════════════════════════════════════════════════╗" . PHP_EOL;
        printf("  ║  %-49s║" . PHP_EOL, $builderTitle);
        echo "  ╚══════════════════════════════════════════════════╝" . PHP_EOL;
        echo PHP_EOL;
        echo "  " . $this->translator->get('PROJECT_LABEL')  . "  : " . $projectName . PHP_EOL;
        echo "  " . $this->translator->get('LOCATION_LABEL') . "  : " . $targetDir   . PHP_EOL;
        echo PHP_EOL;

        // Toplam adım sayısını hesapla
        $coreStubs    = \ARTIFRAME_CLI_ROOT . '/core-stubs';
        $stubCount    = $this->countFiles($coreStubs);
        $dynamicCount = 14; // createDynamicFiles içindeki dosya sayısı
        $dirCount     = 10;  // oluşturulacak dinamik dizin sayısı
        $this->totalSteps = $stubCount + $dynamicCount + $dirCount;

        // ── FAZ 1: KOPYALAMA ─────────────────────────────────
        $this->printPhaseHeader(1, 4, $this->translator->get('PHASE_COPYING'), $stubCount);

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        $this->copyDirectory($coreStubs, $targetDir);

        // 1.5 Dökümantasyon ve Readme Yerelleştirmesi
        $lang = $this->translator->getLang();
        $this->localizeDocsAndReadme($targetDir, $lang);

        $this->finishPhase();

        // ── FAZ 2: DİZİNLER ──────────────────────────────────
        $this->printPhaseHeader(2, 4, $this->translator->get('PHASE_DIRS'), $dirCount);

        $directoriesToCreate = [
            'src/Auth',
            'src/Email',
            'src/Service',
            'config',
            'app',
            '.agents',
            'public/api/standart',
            'public/api/switch-case',
            'public/auth',
            'public/assets/images',
        ];

        foreach ($directoriesToCreate as $dir) {
            $path = $targetDir . \DIRECTORY_SEPARATOR . $dir;
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
            $this->tick($dir . '/');
        }
        $this->finishPhase();

        // ── FAZ 3: DİNAMİK DOSYALAR ──────────────────────────
        $this->printPhaseHeader(3, 4, $this->translator->get('PHASE_FILES'), $dynamicCount);
        $this->createDynamicFiles($targetDir);
        $this->finishPhase();

        // ── FAZ 4: COMPOSER ──────────────────────────────────
        echo PHP_EOL;
        $phaseLabel = 'Faz 4/4 — ' . $this->translator->get('PHASE_DEPS');
        echo "  ┌─────────────────────────────────────────────────┐" . PHP_EOL;
        printf("  │  📦  %-43s│" . PHP_EOL, $phaseLabel);
        echo "  └─────────────────────────────────────────────────┘" . PHP_EOL;
        echo PHP_EOL;

        $this->runComposerInstall($targetDir);

        // ── BAŞARI ────────────────────────────────────────────
        echo PHP_EOL;
        $successTitle = $this->translator->get('SUCCESS_TITLE');
        echo "  ╔══════════════════════════════════════════════════╗" . PHP_EOL;
        printf("  ║  %-49s║" . PHP_EOL, $successTitle);
        echo "  ╚══════════════════════════════════════════════════╝" . PHP_EOL;
        echo PHP_EOL;

        // Dizin ağacı
        $this->printDirectoryTree($targetDir, $projectName);

        echo PHP_EOL;
        echo "  " . $this->translator->get('NEXT_STEPS') . PHP_EOL;
        echo "     cd " . $projectName . PHP_EOL;
        echo "     " . $this->translator->get('NEXT_STEPS_EDIT_ENV') . PHP_EOL;
        echo PHP_EOL;
    }

    // ─── İlerleme Sistemi ─────────────────────────────────────

    private function printPhaseHeader(int $phase, int $total, string $label, int $fileCount): void
    {
        $filesLabel = $this->translator->get('FILES_TO_PROCESS', ['count' => $fileCount]);
        echo PHP_EOL;
        echo "  ┌─────────────────────────────────────────────────┐" . PHP_EOL;
        printf("  │  📂  Faz %d/%d — %-36s│" . PHP_EOL, $phase, $total, $label);
        printf("  │      %-43s│" . PHP_EOL, $filesLabel);
        echo "  └─────────────────────────────────────────────────┘" . PHP_EOL;
    }

    private function tick(string $label): void
    {
        $this->currentStep++;
        $pct    = (int) round(($this->currentStep / $this->totalSteps) * 100);
        $filled = (int) round($pct / 5); // 20 karakterlik bar
        $empty  = max(0, 20 - $filled);

        $bar  = str_repeat('█', $filled) . str_repeat('░', $empty);
        $line = sprintf(
            "  [%s] %3d%%  (%d/%d)  %-25s",
            $bar,
            $pct,
            $this->currentStep,
            $this->totalSteps,
            mb_substr($label, 0, 25)
        );

        // Aynı satırı güncelle (\r ile başa dön, yeni satır yok)
        echo "\r" . $line;
        flush();
    }

    private function finishPhase(): void
    {
        echo PHP_EOL; // İlerleme satırını tamamla
    }

    // ─── Dosya Sayacı ─────────────────────────────────────────

    private function countFiles(string $dir): int
    {
        $count = 0;
        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS)
        );
        foreach ($items as $item) {
            if ($item->isFile()) {
                $count++;
            }
        }
        return $count;
    }

    // ─── Dizin Ağacı Çizici ───────────────────────────────────

    private function printDirectoryTree(string $dir, string $projectName): void
    {
        echo "  📂 " . $projectName . "/" . PHP_EOL;

        $priorityOrder = [
            'bin', 'config', 'src', 'app', 'public', '.agents',
            'vendor', '.env', '.env.example', '.gitignore',
            'composer.json', 'composer.lock', 'schema.sql',
            'README.md', 'OKUBENI.md', 'LESEMICH.md', 'LISEZMOI.md', 'LEAME.md',
            'kilavuz.html', 'guide.html', 'handbuch.html', 'guide_fr.html', 'guia.html', 'LICENSE',
        ];

        $entries = array_filter(scandir($dir), fn($e) => $e !== '.' && $e !== '..');

        usort($entries, function ($a, $b) use ($priorityOrder) {
            $ia = array_search($a, $priorityOrder);
            $ib = array_search($b, $priorityOrder);
            $ia = ($ia === false) ? 99 : $ia;
            $ib = ($ib === false) ? 99 : $ib;
            return $ia <=> $ib;
        });

        $entries = array_values($entries);
        $total   = count($entries);

        foreach ($entries as $i => $entry) {
            $isLast    = ($i === $total - 1);
            $connector = $isLast ? '└──' : '├──';
            $fullPath  = $dir . \DIRECTORY_SEPARATOR . $entry;

            if (is_dir($fullPath)) {
                $subItems = array_filter(scandir($fullPath), fn($e) => $e !== '.' && $e !== '..');
                $subCount = count($subItems);
                $countStr = $subCount > 0 ? '  ' . $this->translator->get('DIR_ITEM_COUNT', ['count' => $subCount]) : '';
                echo "  │  {$connector} 📂 {$entry}/{$countStr}" . PHP_EOL;
            } else {
                $size    = filesize($fullPath);
                $sizeStr = $size > 1024 ? round($size / 1024, 1) . ' KB' : $size . ' B';
                echo "  │  {$connector} 📄 {$entry}  [{$sizeStr}]" . PHP_EOL;
            }
        }
        echo "  │" . PHP_EOL;
    }

    // ─── Yol Çözümleyici ──────────────────────────────────────

    private function resolvePath(string $path): string
    {
        if (strpos($path, '/') === 0 || preg_match('/^[a-zA-Z]:\\\\/', $path)) {
            return str_replace('\\', '/', $path);
        }
        return str_replace('\\', '/', getcwd() . '/' . $path);
    }

    private function isComposerInstalled(): bool
    {
        $output    = [];
        $returnVar = -1;
        exec("composer --version 2>&1", $output, $returnVar);
        return $returnVar === 0;
    }

    // ─── Kopyalama Motoru ─────────────────────────────────────

    private function copyDirectory(string $source, string $target): void
    {
        if (!is_dir($target)) {
            mkdir($target, 0755, true);
        }

        $dir = opendir($source);
        while (false !== ($file = readdir($dir))) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $srcFile    = $source . '/' . $file;
            $targetFile = $target . '/' . $file;

            if (is_dir($srcFile)) {
                $this->copyDirectory($srcFile, $targetFile);
            } else {
                copy($srcFile, $targetFile);
                $this->tick($file);
            }
        }
        closedir($dir);
    }

    private function localizeDocsAndReadme(string $targetDir, string $lang): void
    {
        $langMap = [
            'tr' => ['docs' => 'kilavuz.html',   'readme' => 'OKUBENI.md'],
            'en' => ['docs' => 'guide.html',     'readme' => 'README.md'],
            'de' => ['docs' => 'handbuch.html',  'readme' => 'LESEMICH.md'],
            'fr' => ['docs' => 'guide_fr.html',  'readme' => 'LISEZMOI.md'],
            'es' => ['docs' => 'guia.html',      'readme' => 'LEAME.md']
        ];
        
        $docNames = $langMap[$lang] ?? $langMap['en'];

        if (file_exists($targetDir . '/docs/' . $lang . '.html')) {
            copy($targetDir . '/docs/' . $lang . '.html', $targetDir . '/' . $docNames['docs']);
        }
        if (file_exists($targetDir . '/readme/' . $lang . '.md')) {
            copy($targetDir . '/readme/' . $lang . '.md', $targetDir . '/' . $docNames['readme']);
        }

        $this->removeDirectory($targetDir . '/docs');
        $this->removeDirectory($targetDir . '/readme');
        if (file_exists($targetDir . '/README.md') && $lang !== 'en') {
            unlink($targetDir . '/README.md'); // Remove default README if copied directly from core-stubs root
        }
        if (file_exists($targetDir . '/kilavuz.html') && $lang !== 'tr') {
            unlink($targetDir . '/kilavuz.html'); // Remove default kilavuz.html if copied directly from core-stubs root
        }
    }

    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) return;
        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($items as $item) {
            $item->isDir() ? rmdir($item->getRealPath()) : unlink($item->getRealPath());
        }
        rmdir($dir);
    }

    // ─── Dinamik Dosya Üretici ────────────────────────────────

    private function createDynamicFiles(string $targetDir): void
    {
        $licenseHeader = "<?php\n/**\n * ArtiFrame Core Engine\n *\n * @package     ArtiFrame\n * @author      Artilingo\n * @license     AGPLv3 (Attribution-ShareAlike Required)\n * @link        https://artiframe.org\n *\n * NOTICE: This file is part of the ArtiFrame ecosystem.\n * Any derivative works or patches MUST retain this original copyright notice\n * and remain open-source under the AGPLv3 license.\n */\n";

        // composer.json
        $composerJson = <<<JSON
{
  "name": "artiframe/project",
  "description": "ArtiFrame Core PHP Application",
  "type": "project",
  "license": "AGPL-3.0-or-later",
  "require": {
    "php": ">=8.1",
    "phpmailer/phpmailer": "^6.9",
    "aws/aws-sdk-php": "^3.316",
    "predis/predis": "^2.2",
    "ramsey/uuid": "^4.7"
  },
  "autoload": {
    "psr-4": {
      "Bin\\\\": "bin/",
      "App\\\\": "app/",
      "Src\\\\": "src/"
    }
  },
  "authors": [
    {
      "name": "Artilingo",
      "homepage": "https://artilingo.com"
    }
  ],
  "config": {
    "optimize-autoloader": true,
    "preferred-install": "dist",
    "sort-packages": true
  }
}
JSON;
        file_put_contents($targetDir . '/composer.json', $composerJson);
        $this->tick('composer.json');

        // config/app-version.php
        file_put_contents(
            $targetDir . '/config/app-version.php',
            $licenseHeader . "\n// APP_ENV: 0 = Canlı (Prod), 1 = Geliştirici (Debug)\ndefine('APP_ENV', 1);\ndefine('APP_VERSION', '1.0.0');\n"
        );
        $this->tick('config/app-version.php');

        // config/central-control.php
        file_put_contents($targetDir . '/config/central-control.php', $licenseHeader . "\n// Central Control Configurations\n");
        $this->tick('config/central-control.php');

        // config/api-security.php
        file_put_contents($targetDir . '/config/api-security.php', $licenseHeader . "\n// API Security Rules\n");
        $this->tick('config/api-security.php');

        // .env & .env.example
        $envStub = implode("\n", [
            "# ================================================",
            "# ArtiFrame Environment Configuration",
            "# Hassas bilgileri buraya girin. Bu dosyayı asla",
            "# git reposuna eklemeyin! Bkz: .gitignore",
            "# ================================================",
            "",
            "# Uygulama",
            "APP_NAME=ArtiFrame",
            "APP_URL=http://localhost",
            "",
            "# Veritabanı (MySQL / MariaDB)",
            "DB_HOST=localhost",
            "DB_USER=root",
            "DB_PASS=",
            "DB_NAME=artiframe",
            "",
            "# SMTP (PHPMailer)",
            "MAIL_HOST=smtp.example.com",
            "MAIL_PORT=587",
            "MAIL_USERNAME=",
            "MAIL_PASSWORD=",
            "MAIL_FROM_ADDRESS=noreply@example.com",
            "MAIL_FROM_NAME=ArtiFrame",
            "MAIL_ENCRYPTION=tls",
            "",
            "# Cloudflare R2 (AWS S3 Uyumlu Depolama)",
            "R2_ACCOUNT_ID=",
            "R2_ACCESS_KEY=",
            "R2_SECRET_KEY=",
            "R2_BUCKET_NAME=",
            "R2_PUBLIC_URL=",
            "",
            "# Redis",
            "REDIS_HOST=127.0.0.1",
            "REDIS_PORT=6379",
            "REDIS_PASSWORD=",
            "REDIS_DB=0",
        ]) . "\n";
        file_put_contents($targetDir . '/.env', $envStub);
        $this->tick('.env');
        file_put_contents($targetDir . '/.env.example', $envStub);
        $this->tick('.env.example');

        // schema.sql
        file_put_contents($targetDir . '/schema.sql', "-- ArtiFrame Initial DB Schema\n-- Created: " . date('Y-m-d') . "\n\n");
        $this->tick('schema.sql');

        // src/Email/EmailService.php
        $emailServiceContent = <<<'EMAILPHP'
<?php
/**
 * ArtiFrame Core Engine
 *
 * @package     ArtiFrame
 * @author      Artilingo
 * @license     AGPLv3 (Attribution-ShareAlike Required)
 * @link        https://artiframe.org
 */

namespace Src\Email;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    private PHPMailer $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->mailer->isSMTP();
        $this->mailer->Host       = $_ENV['MAIL_HOST'] ?? 'smtp.example.com';
        $this->mailer->SMTPAuth   = true;
        $this->mailer->Username   = $_ENV['MAIL_USERNAME'] ?? '';
        $this->mailer->Password   = $_ENV['MAIL_PASSWORD'] ?? '';
        $this->mailer->SMTPSecure = $_ENV['MAIL_ENCRYPTION'] ?? PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port       = (int) ($_ENV['MAIL_PORT'] ?? 587);
        $this->mailer->CharSet    = 'UTF-8';
        $this->mailer->setFrom(
            $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@example.com',
            $_ENV['MAIL_FROM_NAME']    ?? 'ArtiFrame'
        );
    }

    /**
     * Tek alıcıya HTML e-posta gönderir.
     */
    public function send(string $toEmail, string $toName, string $subject, string $body): bool
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($toEmail, $toName);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $body;
            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            $isDev = (defined('APP_ENV') && APP_ENV == 1);
            error_log('EmailService Hatası: ' . $e->getMessage());
            if ($isDev) {
                error_log('PHPMailer Detay: ' . $this->mailer->ErrorInfo);
            }
            return false;
        }
    }
}
EMAILPHP;
        file_put_contents($targetDir . '/src/Email/EmailService.php', $emailServiceContent);
        $this->tick('EmailService.php');

        // src/Service/RedisService.php
        $redisServiceContent = <<<'REDISPHP'
<?php
/**
 * ArtiFrame Core Engine
 *
 * @package     ArtiFrame
 * @author      Artilingo
 * @license     AGPLv3 (Attribution-ShareAlike Required)
 * @link        https://artiframe.org
 */

namespace Src\Service;

use Predis\Client as RedisClient;

class RedisService
{
    private static ?RedisClient $instance = null;

    public static function getInstance(): RedisClient
    {
        if (self::$instance === null) {
            self::$instance = new RedisClient([
                'scheme'   => 'tcp',
                'host'     => $_ENV['REDIS_HOST']     ?? '127.0.0.1',
                'port'     => (int) ($_ENV['REDIS_PORT']     ?? 6379),
                'password' => $_ENV['REDIS_PASSWORD'] ?: null,
                'database' => (int) ($_ENV['REDIS_DB']       ?? 0),
            ]);
        }
        return self::$instance;
    }

    /** Değer yazar. Opsiyonel TTL (saniye cinsinden). */
    public static function set(string $key, mixed $value, int $ttl = 0): void
    {
        $client  = self::getInstance();
        $encoded = is_array($value) ? json_encode($value) : $value;
        $ttl > 0 ? $client->setex($key, $ttl, $encoded) : $client->set($key, $encoded);
    }

    /** Değer okur. JSON ise otomatik çözümler, yoksa null döner. */
    public static function get(string $key): mixed
    {
        $value = self::getInstance()->get($key);
        if ($value === null) return null;
        $decoded = json_decode($value, true);
        return (json_last_error() === JSON_ERROR_NONE) ? $decoded : $value;
    }

    /** Anahtarı siler. */
    public static function delete(string $key): void
    {
        self::getInstance()->del($key);
    }

    /** Anahtarın var olup olmadığını kontrol eder. */
    public static function exists(string $key): bool
    {
        return (bool) self::getInstance()->exists($key);
    }
}
REDISPHP;
        file_put_contents($targetDir . '/src/Service/RedisService.php', $redisServiceContent);
        $this->tick('RedisService.php');
        file_put_contents($targetDir . '/LICENSE', "GNU AFFERO GENERAL PUBLIC LICENSE Version 3\n");
        $this->tick('LICENSE');

        // public/index.php — localized welcome page
        $lang    = $this->translator->getLang();
        $docFile = [
            'tr' => ['file' => 'kilavuz.html',  'installed' => 'ArtiFrame Başarıyla Kuruldu!',        'guide' => 'Başlamak için'],
            'en' => ['file' => 'guide.html',     'installed' => 'ArtiFrame Installed Successfully!',   'guide' => 'To get started, see'],
            'de' => ['file' => 'handbuch.html',  'installed' => 'ArtiFrame Erfolgreich Installiert!',  'guide' => 'Für den Einstieg, siehe'],
            'fr' => ['file' => 'guide_fr.html',  'installed' => 'ArtiFrame Installé avec Succès !',    'guide' => 'Pour commencer, consultez'],
            'es' => ['file' => 'guia.html',       'installed' => '¡ArtiFrame Instalado Exitosamente!',  'guide' => 'Para comenzar, vea'],
        ][$lang] ?? ['file' => 'guide.html', 'installed' => 'ArtiFrame Installed Successfully!', 'guide' => 'To get started, see'];

        $indexContent = "<?php\nrequire_once __DIR__ . '/../app/ViewControl.php';\n?>\n"
            . "<!DOCTYPE html>\n"
            . "<html lang=\"{$lang}\" data-theme=\"default\" data-mode=\"light\">\n"
            . "<head>\n"
            . "    <?php require_once __DIR__ . '/includes/head.php'; ?>\n"
            . "    <title>ArtiFrame | Welcome</title>\n"
            . "</head>\n"
            . "<body>\n"
            . "    <main style=\"display:flex;justify-content:center;align-items:center;height:100vh;flex-direction:column;font-family:'Inter',sans-serif;\">\n"
            . "        <h1 style=\"background:linear-gradient(to right,#3b82f6,#8b5cf6);-webkit-background-clip:text;-webkit-text-fill-color:transparent;\">\n"
            . "            {$docFile['installed']}\n"
            . "        </h1>\n"
            . "        <p>{$docFile['guide']} <a href=\"/{$docFile['file']}\">{$docFile['file']}</a>.</p>\n"
            . "    </main>\n"
            . "</body>\n"
            . "</html>\n";
        file_put_contents($targetDir . '/public/index.php', $indexContent);
        $this->tick('public/index.php');

        // public/.htaccess
        $htaccessContent = <<<HTACCESS
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
HTACCESS;
        file_put_contents($targetDir . '/public/.htaccess', $htaccessContent);
        $this->tick('public/.htaccess');

        // public/site.webmanifest
        $manifestContent = <<<MANIFEST
{
  "name": "ArtiFrame App",
  "short_name": "ArtiFrame",
  "start_url": "/",
  "display": "standalone",
  "background_color": "#ffffff",
  "theme_color": "#3b82f6"
}
MANIFEST;
        file_put_contents($targetDir . '/public/site.webmanifest', $manifestContent);
        $this->tick('public/site.webmanifest');

        // .agents/AGENTS.md
        file_put_contents($targetDir . '/.agents/AGENTS.md', "# AI Agent Context Rules\n\nArtiFrame proje standartları...");
        $this->tick('.agents/AGENTS.md');
    }

    // ─── Composer Install ─────────────────────────────────────

    private function runComposerInstall(string $targetDir): void
    {
        $command = "cd " . escapeshellarg($targetDir) . " && composer install";
        passthru($command);
    }
}
