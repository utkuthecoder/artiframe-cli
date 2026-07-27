<?php
namespace ArtiFrame\Cli\Commands;

use ArtiFrame\Cli\Services\Translator;
use ArtiFrame\Cli\Services\Safeguard;

class MakeViewCommand
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
            echo "❌ " . $this->translator->get('ERROR_VIEW_PATH_REQUIRED') . PHP_EOL;
            return;
        }

        // Normalize path
        $target = ltrim(str_replace('\\', '/', $target), '/');

        // Ensure .php extension
        if (!str_ends_with($target, '.php')) {
            $target .= '.php';
        }

        $projectRoot = getcwd();
        $viewPath = $projectRoot . '/public/' . $target;

        if (!$this->safeguard->checkTarget($viewPath)) {
            return;
        }

        // Determine hierarchy for assets
        $pathParts      = explode('/', $target);
        $fileNameWithExt = array_pop($pathParts);
        $fileName       = pathinfo($fileNameWithExt, PATHINFO_FILENAME);

        $subDir = count($pathParts) > 0 ? implode('/', $pathParts) : 'root';

        $cssRelative = '/assets/css/' . $subDir . '/' . $fileName . '.css';
        $jsRelative  = '/assets/js/' . $subDir . '/' . $fileName . '.js';

        $cssPath = $projectRoot . '/public' . $cssRelative;
        $jsPath  = $projectRoot . '/public' . $jsRelative;

        // Stub path
        $stubPath = \ARTIFRAME_CLI_ROOT . '/stubs/make/view.stub';
        if (!file_exists($stubPath)) {
            echo "❌ " . $this->translator->get('ERROR_STUB_NOT_FOUND', ['path' => $stubPath]) . PHP_EOL;
            echo $this->translator->get('ERROR_RUN_FROM_ROOT') . PHP_EOL;
            return;
        }

        // Generate Files
        $this->ensureDirectoryExists(dirname($viewPath));
        $this->ensureDirectoryExists(dirname($cssPath));
        $this->ensureDirectoryExists(dirname($jsPath));

        // Format Title
        $title = ucfirst(str_replace(['-', '_'], ' ', $fileName));

        // Read and parse stub
        $lang    = $this->translator->getLang();
        $content = file_get_contents($stubPath);
        $content = str_replace(
            ['{{LANG}}', '{{VIEW_TITLE}}', '{{CSS_PATH}}', '{{JS_PATH}}'],
            [$lang, $title, $cssRelative, $jsRelative],
            $content
        );

        file_put_contents($viewPath, $content);
        file_put_contents($cssPath, "/* CSS for {$title} */\n");
        file_put_contents($jsPath, "/* JS for {$title} */\n");

        echo $this->translator->get('SUCCESS_VIEW', ['path' => $target]) . PHP_EOL;
        echo $this->translator->get('SUCCESS_CSS', ['path' => $cssRelative]) . PHP_EOL;
        echo $this->translator->get('SUCCESS_JS',  ['path' => $jsRelative]) . PHP_EOL;
    }

    private function ensureDirectoryExists(string $path): void
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }
}
