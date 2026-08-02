<?php
namespace ArtiFrame\Cli\Commands;

use ArtiFrame\Cli\Services\Translator;

class UpgradeCommand
{
    private Translator $translator;
    private array $changes = [
        'added' => [],
        'modified' => []
    ];

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function execute(array $args): void
    {
        $targetDir = getcwd();

        if (!file_exists($targetDir . '/composer.json') || !is_dir($targetDir . '/app') || !is_dir($targetDir . '/bin')) {
            echo "❌ " . $this->translator->get('ERROR_RUN_FROM_ROOT') . PHP_EOL;
            return;
        }

        // ── KURAL 1: INDEX.PHP KORUMASI ────────────────────────────────
        $indexFile = $targetDir . '/public/index.php';
        if (file_exists($indexFile)) {
            $indexContent = file_get_contents($indexFile);
            if (strpos($indexContent, 'routeLink(') === false) {
                echo PHP_EOL . "❌ " . $this->translator->get('UPGRADE_INDEX_ABORT') . PHP_EOL;
                return;
            }
        }

        // ── KURAL 2: ÇEKİRDEK (BIN) DEĞİŞİKLİK KONTROLÜ ────────────────
        $coreStubsBinDir = \ARTIFRAME_CLI_ROOT . '/core-stubs/bin';
        $projectBinDir = $targetDir . '/bin';

        $isCoreModified = false;
        $filesToCheck = ['SystemMethod.php', 'ViewMethod.php'];

        foreach ($filesToCheck as $file) {
            $stubPath = $coreStubsBinDir . '/' . $file;
            $projPath = $projectBinDir . '/' . $file;

            if (file_exists($stubPath) && file_exists($projPath)) {
                if (md5_file($stubPath) !== md5_file($projPath)) {
                    $isCoreModified = true;
                    break;
                }
            }
        }

        if ($isCoreModified) {
            echo PHP_EOL . "⚠️  " . $this->translator->get('UPGRADE_CORE_WARNING') . PHP_EOL;
            // The prompt asks to press Enter to continue.
            $choice = trim(fgets(STDIN));
            if (strtolower($choice) === 'n' || strtolower($choice) === 'h') {
                echo $this->translator->get('ABORTED') . PHP_EOL;
                return;
            }
        }

        echo PHP_EOL . "🚀 " . $this->translator->get('PHASE_COPYING') . PHP_EOL;

        // ── KURAL 3 & ANA İŞLEM: KOPYALAMA ──────────────────────────────
        $this->copyDirectory(\ARTIFRAME_CLI_ROOT . '/core-stubs/app', $targetDir . '/app');
        $this->copyDirectory(\ARTIFRAME_CLI_ROOT . '/core-stubs/bin', $targetDir . '/bin');

        // Docs
        $docsSource = \ARTIFRAME_CLI_ROOT . '/core-stubs/docs';
        $docsTarget = $targetDir . '/public/docs';
        if (is_dir($docsSource)) {
            if (!is_dir($docsTarget)) {
                mkdir($docsTarget, 0755, true);
            }
            $this->copyDirectory($docsSource, $docsTarget);
        }

        // ── KURAL 4: SERVICE STUB EZİLMESİ ──────────────────────────────
        $servicesDir = $targetDir . '/src/Service';
        $stubsServicesDir = \ARTIFRAME_CLI_ROOT . '/stubs/services';

        if (is_dir($servicesDir) && is_dir($stubsServicesDir)) {
            $iterator = new \DirectoryIterator($servicesDir);
            foreach ($iterator as $fileinfo) {
                if ($fileinfo->isFile() && $fileinfo->getExtension() === 'php') {
                    $filename = $fileinfo->getFilename();
                    
                    $stubIterator = new \DirectoryIterator($stubsServicesDir);
                    foreach ($stubIterator as $stubInfo) {
                        if ($stubInfo->isFile() && $stubInfo->getExtension() === 'stub') {
                            $stubName = $stubInfo->getBasename('.stub');
                            $expectedServiceName = ucfirst($stubName) . 'Service.php';
                            
                            if ($filename === $expectedServiceName) {
                                $oldContent = file_get_contents($fileinfo->getPathname());
                                $content = file_get_contents($stubInfo->getPathname());
                                if (md5($oldContent) !== md5($content)) {
                                    $oldFunctions = $this->getFunctionsFromFile($fileinfo->getPathname());
                                    $newFunctions = $this->getFunctionsFromFile($stubInfo->getPathname());
                                    $addedFunctions = array_values(array_diff($newFunctions, $oldFunctions));
                                    
                                    file_put_contents($fileinfo->getPathname(), $content);
                                    $this->changes['modified']['src/Service/' . $filename] = [
                                        'added_functions' => $addedFunctions
                                    ];
                                }
                            }
                        }
                    }
                }
            }
        }

        if (empty($this->changes['added']) && empty($this->changes['modified'])) {
            echo PHP_EOL . "✅ " . $this->translator->get('UPGRADE_SUCCESS') . " (Sistem zaten güncel)" . PHP_EOL . PHP_EOL;
        } else {
            echo PHP_EOL . "✅ " . $this->translator->get('UPGRADE_SUCCESS') . PHP_EOL . PHP_EOL;
            
            echo "📊 Değişiklik Raporu:" . PHP_EOL;
            echo str_repeat('-', 40) . PHP_EOL;
            
            if (!empty($this->changes['added'])) {
                echo "\e[32m[EKLENDİ]\e[0m" . PHP_EOL;
                foreach ($this->changes['added'] as $add) {
                    echo "  + $add" . PHP_EOL;
                }
                echo PHP_EOL;
            }
            
            if (!empty($this->changes['modified'])) {
                echo "\e[33m[GÜNCELLENDİ]\e[0m" . PHP_EOL;
                foreach ($this->changes['modified'] as $mod => $details) {
                    echo "  ~ $mod" . PHP_EOL;
                    if (!empty($details['added_functions'])) {
                        foreach ($details['added_functions'] as $func) {
                            echo "      \e[36m[YENİ FONKSİYON]\e[0m $func() - Lütfen dokümantasyonu inceleyin." . PHP_EOL;
                        }
                    }
                }
                echo PHP_EOL;
            }
        }
    }

    private function copyDirectory(string $source, string $target): void
    {
        if (!is_dir($target)) {
            mkdir($target, 0755, true);
        }

        $dir = opendir($source);
        while (($file = readdir($dir)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $srcFile = $source . '/' . $file;
            $tgtFile = $target . '/' . $file;

            if (is_dir($srcFile)) {
                $this->copyDirectory($srcFile, $tgtFile);
            } else {
                $isNew = !file_exists($tgtFile);
                $isModified = !$isNew && (md5_file($srcFile) !== md5_file($tgtFile));

                if ($isNew || $isModified) {
                    $addedFunctions = [];
                    if ($isModified && pathinfo($srcFile, PATHINFO_EXTENSION) === 'php') {
                        $oldFunctions = $this->getFunctionsFromFile($tgtFile);
                        $newFunctions = $this->getFunctionsFromFile($srcFile);
                        $addedFunctions = array_values(array_diff($newFunctions, $oldFunctions));
                    }
                    
                    copy($srcFile, $tgtFile);
                    
                    $normalizedTarget = str_replace('\\', '/', $tgtFile);
                    $normalizedCwd = str_replace('\\', '/', getcwd()) . '/';
                    $relativePath = str_replace($normalizedCwd, '', $normalizedTarget);
                    
                    if ($isNew) {
                        $this->changes['added'][] = $relativePath;
                    } else {
                        $this->changes['modified'][$relativePath] = [
                            'added_functions' => $addedFunctions
                        ];
                    }
                }
            }
        }
        closedir($dir);
    }

    private function getFunctionsFromFile(string $filepath): array
    {
        if (!file_exists($filepath)) {
            return [];
        }
        $content = file_get_contents($filepath);
        $functions = [];
        if (preg_match_all('/function\s+([a-zA-Z0-9_]+)\s*\(/i', $content, $matches)) {
            $functions = $matches[1];
        }
        return $functions;
    }
}
