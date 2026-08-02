<?php
namespace ArtiFrame\Cli\Commands;

use ArtiFrame\Cli\Services\Translator;
use ArtiFrame\Cli\Services\Safeguard;

class MakeApiCommand
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
        $type   = $args[0] ?? null;
        $target = $args[1] ?? null;

        if (!$type || !in_array($type, ['standart', 'switch-case'])) {
            echo "❌ " . $this->translator->get('ERROR_API_TYPE') . PHP_EOL;
            return;
        }

        if (!$target) {
            echo "❌ " . $this->translator->get('ERROR_API_PATH_REQUIRED') . PHP_EOL;
            return;
        }

        // Normalize path
        $target = ltrim(str_replace('\\', '/', $target), '/');

        // Ensure .php extension
        if (!str_ends_with($target, '.php')) {
            $target .= '.php';
        }

        $projectRoot = $this->safeguard->getProjectRoot();
        $apiPath     = $projectRoot . '/public/api/' . $type . '/' . $target;

        if (!$this->safeguard->checkTarget($apiPath)) {
            return;
        }

        $stubName = 'api-' . $type . '.stub';
        $stubPath = \ARTIFRAME_CLI_ROOT . '/stubs/make/' . $stubName;

        if (!file_exists($stubPath)) {
            echo "❌ " . $this->translator->get('ERROR_STUB_NOT_FOUND', ['path' => $stubPath]) . PHP_EOL;
            return;
        }

        // Generate File
        $this->ensureDirectoryExists(dirname($apiPath));

        $content = file_get_contents($stubPath);
        file_put_contents($apiPath, $content);

        echo $this->translator->get('SUCCESS_API', ['type' => $type, 'path' => $target]) . PHP_EOL;
    }

    private function ensureDirectoryExists(string $path): void
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }
}
