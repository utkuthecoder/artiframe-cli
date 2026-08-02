<?php
namespace ArtiFrame\Cli\Commands;

use ArtiFrame\Cli\Services\Translator;
use ArtiFrame\Cli\Services\Safeguard;

class MakeClassCommand
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

        if (!$target || strpos($target, '/') === false && strpos($target, '\\') === false) {
            echo "❌ " . $this->translator->get('DIR_REQUIRED_ERROR') . PHP_EOL;
            echo "Example: /src/Service/PaymentService" . PHP_EOL;
            return;
        }

        // Normalize path
        $target = ltrim(str_replace('\\', '/', $target), '/');

        $isCustomRoot = !str_starts_with($target, 'app/') && !str_starts_with($target, 'src/');

        // Ensure .php extension
        if (!str_ends_with($target, '.php')) {
            $target .= '.php';
        }

        $projectRoot = $this->safeguard->getProjectRoot();
        $classPath   = $projectRoot . '/' . $target;

        if (!$this->safeguard->checkTarget($classPath)) {
            return;
        }

        $stubPath = \ARTIFRAME_CLI_ROOT . '/stubs/make/class.stub';
        if (!file_exists($stubPath)) {
            echo "❌ " . $this->translator->get('ERROR_STUB_NOT_FOUND', ['path' => $stubPath]) . PHP_EOL;
            return;
        }

        // Determine Namespace and Class Name
        $pathParts = explode('/', str_replace('.php', '', $target));
        $className = array_pop($pathParts);

        // Create Namespace array with proper casing
        $namespaceParts = [];
        foreach ($pathParts as $part) {
            $namespaceParts[] = ucfirst($part);
        }
        $namespace = implode('\\', $namespaceParts);

        // Generate File
        $this->ensureDirectoryExists(dirname($classPath));

        $content = file_get_contents($stubPath);
        $content = str_replace(
            ['{{NAMESPACE}}', '{{CLASS_NAME}}'],
            [$namespace, $className],
            $content
        );

        file_put_contents($classPath, $content);

        echo $this->translator->get('SUCCESS_CLASS', ['path' => $target]) . PHP_EOL;
        echo $this->translator->get('NAMESPACE_LABEL') . ' ' . $namespace . PHP_EOL;

        if ($isCustomRoot) {
            shell_exec('composer dump-autoload -q');
        }
    }

    private function ensureDirectoryExists(string $path): void
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }
}
