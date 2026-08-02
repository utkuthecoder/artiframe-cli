<?php
namespace ArtiFrame\Cli\Commands;

use ArtiFrame\Cli\Services\Translator;
use ArtiFrame\Cli\Services\Safeguard;

class RemoveCommand
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
        $target = $args[0] ?? null;

        if (!$target) {
            echo "❌ " . $this->translator->get('REMOVE_TARGET_REQUIRED') . PHP_EOL;
            return;
        }

        $projectRoot = $this->safeguard->getProjectRoot();
        
        $targetPath = $target;
        if (!file_exists($targetPath)) {
            $targetPath = rtrim($projectRoot, '/') . '/' . ltrim($target, '/');
        }

        if (!file_exists($targetPath) && !str_ends_with($targetPath, '.php')) {
            $targetPath .= '.php';
        }

        if (!file_exists($targetPath) || is_dir($targetPath)) {
             echo "❌ " . $this->translator->get('REMOVE_FILE_NOT_FOUND', ['path' => $target]) . PHP_EOL;
             return;
        }

        echo "\n⚠️  " . $this->translator->get('REMOVE_CONFIRM_FILE', ['path' => $targetPath]) . " [y/N]: ";
        $ans = trim(fgets(STDIN));
        if (strtolower($ans) !== 'y') {
            echo "Abort.\n";
            return;
        }

        $targetPath = realpath($targetPath);
        $normalizedPath = str_replace('\\', '/', $targetPath);
        $isView = strpos($normalizedPath, '/public/') !== false && strpos($normalizedPath, '/public/api/') === false && strpos($normalizedPath, '/public/assets/') === false;
        $isClass = strpos($normalizedPath, '/src/') !== false || strpos($normalizedPath, '/app/') !== false;

        if ($isView) {
            $assetsFound = [];
            $content = file_get_contents($targetPath);
            
            // Extract CSS path from href="/assets/css/...css"
            if (preg_match('/href="(\/assets\/css\/[^"]+\.css)(?:\?[^"]*)?"/i', $content, $matches)) {
                $cssRelative = ltrim($matches[1], '/');
                $cssPath = rtrim($projectRoot, '/') . '/public/' . $cssRelative;
                if (file_exists($cssPath)) {
                    $assetsFound[] = $cssPath;
                }
            }
            
            // Extract JS path from src="/assets/js/...js"
            if (preg_match('/src="(\/assets\/js\/[^"]+\.js)(?:\?[^"]*)?"/i', $content, $matches)) {
                $jsRelative = ltrim($matches[1], '/');
                $jsPath = rtrim($projectRoot, '/') . '/public/' . $jsRelative;
                if (file_exists($jsPath)) {
                    $assetsFound[] = $jsPath;
                }
            }

            if (!empty($assetsFound)) {
                echo "\n⚠️  " . $this->translator->get('REMOVE_CONFIRM_ASSETS') . " [y/N]: ";
                $ansAssets = trim(fgets(STDIN));
                if (strtolower($ansAssets) === 'y') {
                    foreach ($assetsFound as $a) {
                        unlink($a);
                        echo "  ✅ Deleted: " . $a . "\n";
                    }
                }
            }
        } elseif ($isClass) {
            $className = pathinfo($targetPath, PATHINFO_FILENAME);
            $apiDir = $projectRoot . '/public/api';
            
            if (is_dir($apiDir)) {
                $usages = $this->searchClassUsages($apiDir, $className);
                
                if (!empty($usages)) {
                    echo "\n🚨 " . $this->translator->get('REMOVE_FOUND_APIS', ['class' => $className]) . "\n";
                    foreach ($usages as $usage) {
                        echo "   -> " . $usage['file'] . " (Lines: " . implode(', ', $usage['lines']) . ")\n";
                    }
                    echo "\n📌 " . $this->translator->get('REMOVE_API_WARNING') . "\n";
                }
            }
        }

        unlink($targetPath);
        echo "\n✅ " . $this->translator->get('REMOVE_SUCCESS', ['path' => $targetPath]) . PHP_EOL;
    }

    private function searchClassUsages(string $dir, string $className): array
    {
        $usages = [];
        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS)
        );
        foreach ($items as $item) {
            if ($item->isFile() && $item->getExtension() === 'php') {
                $lines = file($item->getRealPath());
                $foundLines = [];
                foreach ($lines as $index => $line) {
                    if (strpos($line, $className) !== false) {
                        $foundLines[] = $index + 1;
                    }
                }
                if (!empty($foundLines)) {
                    $usages[] = [
                        'file' => $item->getRealPath(),
                        'lines' => $foundLines
                    ];
                }
            }
        }
        return $usages;
    }
}
