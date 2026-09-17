<?php
namespace ArtiFrame\Cli\Commands;

use ArtiFrame\Cli\Services\Translator;

class ServeCommand
{
    private Translator $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function execute(array $args): void
    {
        $safeguard = new \ArtiFrame\Cli\Services\Safeguard($this->translator);
        $projectRoot = $safeguard->getProjectRoot();
        $publicDir   = $projectRoot . '/public';

        if (!is_dir($publicDir) || !file_exists($publicDir . '/index.php')) {
            echo "\n  ❌  " . $this->translator->get('SERVE_NOT_PROJECT') . "\n\n";
            return;
        }

        $port = '9002';
        if (isset($args[0]) && is_numeric($args[0])) {
            $port = $args[0];
        }

        echo "\n  🚀  ArtiFrame Server Studio başlatılıyor...\n";
        
        $serverStudioPath = dirname(__DIR__, 2) . '/server-studio';
        $os = PHP_OS_FAMILY;
        
        $cmd = sprintf('npm run start --prefix %s -- %s %s', escapeshellarg($serverStudioPath), escapeshellarg($projectRoot), escapeshellarg($port));
        
        if ($os === 'Windows') {
            pclose(popen("start \"ArtiFrame Server Studio\" cmd /c \"{$cmd}\"", "r"));
        } else {
            exec("{$cmd} > /dev/null 2>&1 &");
        }
        
        echo "  ✅ Server Studio arka planda açıldı. Gelişmiş arayüzü kontrol edin.\n\n";
    }
}
