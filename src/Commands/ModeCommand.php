<?php
namespace ArtiFrame\Cli\Commands;

use ArtiFrame\Cli\Services\Translator;

class ModeCommand
{
    private Translator $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function execute(array $args): void
    {
        $mode = $args[0] ?? null;

        if ($mode !== '0' && $mode !== '1') {
            echo "❌ " . $this->translator->get('MODE_INVALID') . PHP_EOL;
            return;
        }

        $projectRoot = getcwd();
        $configFile  = $projectRoot . '/config/app-version.php';

        if (!file_exists($configFile)) {
            echo "❌ " . $this->translator->get('MODE_NOT_PROJECT') . PHP_EOL;
            return;
        }

        $content = file_get_contents($configFile);
        
        // Find current mode
        $currentMode = null;
        if (preg_match('/define\(\s*\'APP_ENV\'\s*,\s*(0|1)\s*\);/', $content, $matches)) {
            $currentMode = $matches[1];
        }

        $modeNames = [
            '0' => 'Prod',
            '1' => 'Debug'
        ];

        if ($currentMode === $mode) {
            if ($mode === '1') {
                echo "ℹ️  " . $this->translator->get('MODE_ALREADY_DEBUG') . PHP_EOL;
            } else {
                echo "ℹ️  " . $this->translator->get('MODE_ALREADY_PROD') . PHP_EOL;
            }
            return;
        }

        // Replace the mode
        if ($currentMode !== null) {
            $newContent = preg_replace(
                '/define\(\s*\'APP_ENV\'\s*,\s*(0|1)\s*\);/', 
                "define('APP_ENV', {$mode});", 
                $content
            );
            file_put_contents($configFile, $newContent);

            $oldName = $modeNames[$currentMode] ?? 'Bilinmeyen';
            $newName = $modeNames[$mode] ?? 'Bilinmeyen';

            echo "✅ " . $this->translator->get('MODE_CHANGED', ['old' => $oldName, 'new' => $newName]) . PHP_EOL;
        } else {
            echo "❌ config/app-version.php içinde APP_ENV tanımı bulunamadı." . PHP_EOL;
        }
    }
}
