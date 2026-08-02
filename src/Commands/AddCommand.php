<?php
namespace ArtiFrame\Cli\Commands;

use ArtiFrame\Cli\Services\Translator;

class AddCommand
{
    private Translator $translator;

    /**
     * ArtiFrame'in desteklediği paket kayıt defteri.
     *
     * 'composer'  => Composer paket adı
     * 'type'      => 'integrated' | 'direct'
     * 'service'   => Oluşturulacak sınıf dosyası yolu (integrated için)
     * 'stub'      => Stub dosya adı (integrated için)
     * 'env'       => .env dosyasına eklenecek satırlar (integrated için)
     */
    private array $registry = [
        // ── Entegrasyonlu Paketler (Composer + Servis Sınıfı + .env) ───
        'iyzico' => [
            'composer' => 'iyzico/iyzipay-php',
            'type'     => 'integrated',
            'service'  => 'src/Service/Iyzico.php',
            'stub'     => 'iyzico.stub',
            'env'      => [
                '# ── iyzico ──────────────────────',
                'IYZICO_API_KEY=',
                'IYZICO_SECRET_KEY=',
                'IYZICO_BASE_URL=https://sandbox-api.iyzipay.com',
            ],
        ],
        'stripe' => [
            'composer' => 'stripe/stripe-php',
            'type'     => 'integrated',
            'service'  => 'src/Service/Stripe.php',
            'stub'     => 'stripe.stub',
            'env'      => [
                '# ── stripe ──────────────────────',
                'STRIPE_SECRET_KEY=',
                'STRIPE_PUBLISHABLE_KEY=',
                'STRIPE_WEBHOOK_SECRET=',
            ],
        ],
        'jwt' => [
            'composer' => 'firebase/php-jwt',
            'type'     => 'integrated',
            'service'  => 'src/Auth/JwtAuth.php',
            'stub'     => 'jwt.stub',
            'env'      => [
                '# ── jwt ─────────────────────────',
                'JWT_SECRET_KEY=',
                'JWT_ALGORITHM=HS256',
                'JWT_EXPIRY=3600',
            ],
        ],
        'image' => [
            'composer' => 'intervention/image',
            'type'     => 'integrated',
            'service'  => 'src/Service/ImageProcessor.php',
            'stub'     => 'image.stub',
            'env'      => [
                '# ── image ───────────────────────',
                'IMAGE_DRIVER=gd',
                'IMAGE_QUALITY=80',
            ],
        ],
        's3' => [
            'composer' => 'aws/aws-sdk-php',
            'type'     => 'integrated',
            'service'  => 'src/Service/S3Storage.php',
            'stub'     => 's3.stub',
            'env'      => [
                '# ── aws s3 ──────────────────────',
                'AWS_ACCESS_KEY_ID=',
                'AWS_SECRET_ACCESS_KEY=',
                'AWS_DEFAULT_REGION=eu-central-1',
                'AWS_BUCKET=',
                'AWS_ENDPOINT=',
            ],
        ],
        'sentry' => [
            'composer' => 'sentry/sentry',
            'type'     => 'integrated',
            'service'  => 'src/Service/Sentry.php',
            'stub'     => 'sentry.stub',
            'env'      => [
                '# ── sentry ──────────────────────',
                'SENTRY_DSN=',
            ],
        ],
        'redis' => [
            'composer' => 'predis/predis',
            'type'     => 'integrated',
            'service'  => 'src/Service/RedisCache.php',
            'stub'     => 'redis.stub',
            'env'      => [
                '# ── redis ───────────────────────',
                'REDIS_HOST=127.0.0.1',
                'REDIS_PORT=6379',
                'REDIS_PASSWORD=',
                'REDIS_DATABASE=0',
            ],
        ],
        'pusher' => [
            'composer' => 'pusher/pusher-php-server',
            'type'     => 'integrated',
            'service'  => 'src/Service/Pusher.php',
            'stub'     => 'pusher.stub',
            'env'      => [
                '# ── pusher ──────────────────────',
                'PUSHER_APP_ID=',
                'PUSHER_APP_KEY=',
                'PUSHER_APP_SECRET=',
                'PUSHER_CLUSTER=eu',
            ],
        ],
        'twilio' => [
            'composer' => 'twilio/sdk',
            'type'     => 'integrated',
            'service'  => 'src/Service/Twilio.php',
            'stub'     => 'twilio.stub',
            'env'      => [
                '# ── twilio ──────────────────────',
                'TWILIO_SID=',
                'TWILIO_AUTH_TOKEN=',
                'TWILIO_FROM_NUMBER=',
            ],
        ],

        // ── Direkt Kullanılabilir Paketler (Sadece Composer) ───────────
        'phpmailer' => [
            'composer' => 'phpmailer/phpmailer',
            'type'     => 'integrated',
            'service'  => 'src/Service/Mailer.php',
            'stub'     => 'phpmailer.stub',
            'env'      => [
                '# ── mail (phpmailer) ──────────────────────',
                'MAIL_HOST=smtp.mailtrap.io',
                'MAIL_PORT=2525',
                'MAIL_USERNAME=',
                'MAIL_PASSWORD=',
                'MAIL_ENCRYPTION=tls',
                'MAIL_FROM_ADDRESS=hello@example.com',
                'MAIL_FROM_NAME=ArtiFrame',
            ],
        ],
        'mpdf' => [
            'composer' => 'mpdf/mpdf',
            'type'     => 'direct',
        ],
        'dompdf' => [
            'composer' => 'dompdf/dompdf',
            'type'     => 'direct',
        ],
        'spreadsheet' => [
            'composer' => 'phpoffice/phpspreadsheet',
            'type'     => 'direct',
        ],
        'qrcode' => [
            'composer' => 'endroid/qr-code',
            'type'     => 'integrated',
            'service'  => 'src/Service/QrCode.php',
            'stub'     => 'qrcode.stub',
        ],
        'guzzle' => [
            'composer' => 'guzzlehttp/guzzle',
            'type'     => 'direct',
        ],
        'uuid' => [
            'composer' => 'ramsey/uuid',
            'type'     => 'direct',
        ],
        'carbon' => [
            'composer' => 'nesbot/carbon',
            'type'     => 'direct',
        ],
        'monolog' => [
            'composer' => 'monolog/monolog',
            'type'     => 'direct',
        ],
    ];

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function execute(array $args): void
    {
        $packageAlias = strtolower($args[0] ?? '');

        // ── Parametre kontrolü ───────────────────────────────────
        if ($packageAlias === '') {
            echo PHP_EOL;
            echo "  ❌ " . $this->translator->get('ADD_MISSING_NAME') . PHP_EOL;
            echo PHP_EOL;
            $this->showAvailablePackages();
            return;
        }

        // ── "list" alt komutu ────────────────────────────────────
        if ($packageAlias === 'list') {
            $this->showAvailablePackages();
            return;
        }

        // ── Kayıt defterinde ara ─────────────────────────────────
        if (!isset($this->registry[$packageAlias])) {
            echo PHP_EOL;
            echo "  ❌ " . $this->translator->get('ADD_NOT_FOUND', ['name' => $packageAlias]) . PHP_EOL;
            echo PHP_EOL;
            $this->showAvailablePackages();
            return;
        }

        $package = $this->registry[$packageAlias];
        $composerName = $package['composer'];

        // ── Composer kontrolü ────────────────────────────────────
        if (!$this->isComposerInstalled()) {
            echo PHP_EOL;
            echo "  ❌ " . $this->translator->get('COMPOSER_MISSING_TITLE') . PHP_EOL;
            return;
        }

        // ── composer.json kontrolü ───────────────────────────────
        $projectRoot = getcwd();
        if (!file_exists($projectRoot . '/composer.json')) {
            echo PHP_EOL;
            echo "  ❌ " . $this->translator->get('ADD_NO_PROJECT') . PHP_EOL;
            return;
        }

        // ── Zaten kurulu mu? ─────────────────────────────────────
        if ($this->isAlreadyInstalled($projectRoot, $composerName)) {
            echo PHP_EOL;
            echo "  ⚠️  " . $this->translator->get('ADD_ALREADY', ['name' => $packageAlias]) . PHP_EOL;
            return;
        }

        // ── Kurulum başlasın ─────────────────────────────────────
        echo PHP_EOL;
        echo "  📦 " . $this->translator->get('ADD_INSTALLING', ['name' => $packageAlias, 'composer' => $composerName]) . PHP_EOL;
        echo PHP_EOL;

        $command = 'composer require ' . escapeshellarg($composerName);
        $exitCode = 0;
        passthru($command, $exitCode);

        if ($exitCode !== 0) {
            echo PHP_EOL;
            echo "  ❌ " . $this->translator->get('ADD_FAILED', ['name' => $packageAlias]) . PHP_EOL;
            return;
        }

        // ── Entegrasyonlu paket ise ek işlemler ──────────────────
        if ($package['type'] === 'integrated') {
            $this->copyServiceStub($projectRoot, $package, $packageAlias);
            $this->appendEnvVariables($projectRoot, $package, $packageAlias);
        }

        // ── Başarılı ─────────────────────────────────────────────
        echo PHP_EOL;
        echo "  ╔══════════════════════════════════════════════════╗" . PHP_EOL;
        echo "  ║  ✅ " . $this->translator->get('ADD_SUCCESS', ['name' => $packageAlias, 'composer' => $composerName]) . PHP_EOL;
        echo "  ╚══════════════════════════════════════════════════╝" . PHP_EOL;

        if ($package['type'] === 'integrated') {
            echo PHP_EOL;
            echo "  📄 " . $this->translator->get('ADD_SERVICE_CREATED', ['path' => $package['service']]) . PHP_EOL;
            echo "  🔑 " . $this->translator->get('ADD_ENV_UPDATED') . PHP_EOL;
            echo "  ⚙️  " . $this->translator->get('ADD_EDIT_ENV') . PHP_EOL;
        }

        echo PHP_EOL;
    }

    // ─── Servis Sınıfı Kopyalama ─────────────────────────────────

    /**
     * CLI'daki stub dosyasını projenin içine kopyalar.
     */
    private function copyServiceStub(string $projectRoot, array $package, string $alias): void
    {
        $stubPath = \ARTIFRAME_CLI_ROOT . '/stubs/service/' . $package['stub'];
        $targetPath = $projectRoot . '/' . $package['service'];

        if (!file_exists($stubPath)) {
            echo "  ⚠️  " . $this->translator->get('ADD_STUB_MISSING', ['name' => $alias]) . PHP_EOL;
            return;
        }

        // Hedef dizini oluştur
        $targetDir = dirname($targetPath);
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        // Zaten varsa üzerine yazma
        if (file_exists($targetPath)) {
            echo "  ⚠️  " . $this->translator->get('ADD_SERVICE_EXISTS', ['path' => $package['service']]) . PHP_EOL;
            return;
        }

        copy($stubPath, $targetPath);
    }

    // ─── .env Değişkenleri Ekleme ────────────────────────────────

    /**
     * .env ve .env.example dosyalarına gerekli anahtarları ekler.
     */
    private function appendEnvVariables(string $projectRoot, array $package, string $alias): void
    {
        if (empty($package['env'])) {
            return;
        }

        $envBlock = PHP_EOL . implode(PHP_EOL, $package['env']) . PHP_EOL;

        // .env dosyasına ekle
        $envFile = $projectRoot . '/.env';
        if (file_exists($envFile)) {
            // Zaten eklenmişse tekrar ekleme
            $currentContent = file_get_contents($envFile);
            if (str_contains($currentContent, $package['env'][0])) {
                return; // Başlık satırı zaten var, atla
            }
            file_put_contents($envFile, $envBlock, FILE_APPEND);
        }

        // .env.example dosyasına ekle
        $envExample = $projectRoot . '/.env.example';
        if (file_exists($envExample)) {
            $currentContent = file_get_contents($envExample);
            if (!str_contains($currentContent, $package['env'][0])) {
                file_put_contents($envExample, $envBlock, FILE_APPEND);
            }
        }
    }

    // ─── Yardımcı Metodlar ───────────────────────────────────────

    /**
     * Paketin zaten kurulu olup olmadığını kontrol eder.
     */
    private function isAlreadyInstalled(string $projectRoot, string $composerName): bool
    {
        $lockFile = $projectRoot . '/composer.lock';
        if (!file_exists($lockFile)) {
            return false;
        }
        $lockContent = file_get_contents($lockFile);
        return str_contains($lockContent, '"' . $composerName . '"');
    }

    /**
     * Kayıtlı tüm paketleri kategorili bir şekilde listeler.
     */
    private function showAvailablePackages(): void
    {
        $g  = "\033[38;2;0;157;108m";  // green
        $y  = "\033[38;5;220m";         // yellow
        $d  = "\033[38;2;100;100;100m"; // dim
        $c  = "\033[38;5;81m";          // cyan
        $w  = "\033[1;37m";             // white bold
        $r  = "\033[0m";                // reset

        echo PHP_EOL;
        echo "  " . $w . $this->translator->get('ADD_LIST_TITLE') . $r . PHP_EOL;
        echo "  " . $d . "─────────────────────────────────────────────────────────────" . $r . PHP_EOL;
        echo PHP_EOL;

        // Entegrasyonlu Paketler
        echo "  " . $y . "⚡ " . $this->translator->get('ADD_CAT_INTEGRATED') . $r . PHP_EOL;
        echo PHP_EOL;

        foreach ($this->registry as $alias => $info) {
            if ($info['type'] === 'integrated') {
                $service = $info['service'] ?? '';
                echo "  " . $g . "  add " . $alias . $r . $d . str_repeat(' ', max(1, 16 - strlen($alias))) . $info['composer'] . "  →  " . $service . $r . PHP_EOL;
            }
        }

        echo PHP_EOL;

        // Direkt Paketler
        echo "  " . $c . "📦 " . $this->translator->get('ADD_CAT_DIRECT') . $r . PHP_EOL;
        echo PHP_EOL;

        foreach ($this->registry as $alias => $info) {
            if ($info['type'] === 'direct') {
                echo "  " . $g . "  add " . $alias . $r . $d . str_repeat(' ', max(1, 16 - strlen($alias))) . $info['composer'] . $r . PHP_EOL;
            }
        }

        echo PHP_EOL;
    }

    private function isComposerInstalled(): bool
    {
        $check = shell_exec('composer --version 2>&1');
        return $check !== null && str_contains($check, 'Composer');
    }
}
