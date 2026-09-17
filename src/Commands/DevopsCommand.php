<?php
namespace ArtiFrame\Cli\Commands;

use ArtiFrame\Cli\Services\Translator;

class DevopsCommand
{
    private Translator $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function execute(array $args): void
    {
        $tab = isset($args[0]) ? $args[0] : 'launcher';
        $cwd = getcwd();
        
        echo "\n  🚀  ArtiFrame DevOps Studio başlatılıyor...\n";
        
        $devopsStudioPath = dirname(__DIR__, 2) . '/devops-studio';
        $os = PHP_OS_FAMILY;
        
        $cmd = sprintf('npm run start --prefix %s -- %s %s', escapeshellarg($devopsStudioPath), escapeshellarg($tab), escapeshellarg($cwd));
        
        if ($os === 'Windows') {
            pclose(popen("start \"ArtiFrame DevOps Studio\" cmd /c \"{$cmd}\"", "r"));
        } else {
            exec("{$cmd} > /dev/null 2>&1 &");
        }
        
        echo "  ✅ DevOps Studio arka planda açıldı.\n\n";
    }
}
