<?php
namespace ArtiFrame\Cli\Commands;

use ArtiFrame\Cli\Services\Translator;
use ArtiFrame\Cli\Services\Safeguard;

class DbStartCommand
{
    private Translator $translator;
    private Safeguard  $safeguard;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
        $this->safeguard  = new Safeguard($translator);
    }

    public function execute(array $args): void
    {
        $projectRoot = $this->safeguard->getProjectRoot();
        $dbStudioPath = dirname(__DIR__, 2) . '/db-studio';
        
        $mode = 'offline';
        if (in_array('--mode=live', $args)) {
            $mode = 'live';
        }

        if ($mode === 'live' && !file_exists($projectRoot . '/.env')) {
            echo "❌ .env dosyasi bulunamadi. Lutfen veritabani ayarlarini yapin veya bagimsiz mod icin 'artiframe db:start' kullanin.\n";
            return;
        }

        echo "🚀 ArtiFrame DB Studio baslatiliyor...\n";
        
        $os = PHP_OS_FAMILY;
        $cmd = sprintf('npm run start --prefix %s -- %s --mode=%s', escapeshellarg($dbStudioPath), escapeshellarg($projectRoot), $mode);
        
        if ($os === 'Windows') {
            pclose(popen("start \"ArtiFrame DB Studio\" cmd /c \"{$cmd}\"", "r"));
        } else {
            exec("{$cmd} > /dev/null 2>&1 &");
        }
        
        echo "✅ DB Studio arka planda acildi.\n";
    }
}
