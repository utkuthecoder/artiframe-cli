<?php
namespace ArtiFrame\Cli\Commands;

use ArtiFrame\Cli\Services\Translator;

class VersionCommand
{
    private Translator $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function execute(array $args): void
    {
        $action = $args[0] ?? null; // upgrade or downgrade
        $level  = $args[1] ?? null; // major, minor, or patch

        if (!$action || !in_array($action, ['upgrade', 'downgrade'])) {
            echo "❌ " . $this->translator->get('ERROR_VERSION_ACTION') . PHP_EOL;
            return;
        }

        if (!$level || !in_array($level, ['major', 'minor', 'patch'])) {
            echo "❌ " . $this->translator->get('ERROR_VERSION_LEVEL') . PHP_EOL;
            return;
        }

        $projectRoot = getcwd();
        $versionFile = $projectRoot . '/config/app-version.php';

        if (!file_exists($versionFile)) {
            echo "❌ " . $this->translator->get('ERROR_VERSION_FILE', ['path' => $versionFile]) . PHP_EOL;
            return;
        }

        $content = file_get_contents($versionFile);

        // Match define('APP_VERSION', 'x.y.z');
        if (!preg_match('/define\(\'APP_VERSION\',\s*\'(\d+)\.(\d+)\.(\d+)\'\);/', $content, $matches)) {
            echo "❌ " . $this->translator->get('ERROR_VERSION_PARSE') . PHP_EOL;
            return;
        }

        $major      = (int)$matches[1];
        $minor      = (int)$matches[2];
        $patch      = (int)$matches[3];
        $oldVersion = "{$major}.{$minor}.{$patch}";

        if ($action === 'upgrade') {
            if ($level === 'major') {
                $major++;
                $minor = 0;
                $patch = 0;
            } elseif ($level === 'minor') {
                $minor++;
                $patch = 0;
            } elseif ($level === 'patch') {
                $patch++;
            }
        } else { // downgrade
            if ($level === 'major') {
                $major = max(1, $major - 1);
                $minor = 0;
                $patch = 0;
            } elseif ($level === 'minor') {
                $minor = max(0, $minor - 1);
                $patch = 0;
            } elseif ($level === 'patch') {
                $patch = max(0, $patch - 1);
            }
        }

        $newVersion = "{$major}.{$minor}.{$patch}";

        if ($oldVersion === $newVersion) {
            echo $this->translator->get('WARN_VERSION_UNCHANGED', ['version' => $newVersion]) . PHP_EOL;
            return;
        }

        $newContent = preg_replace(
            '/define\(\'APP_VERSION\',\s*\'\d+\.\d+\.\d+\'\);/',
            "define('APP_VERSION', '{$newVersion}');",
            $content
        );

        file_put_contents($versionFile, $newContent);

        echo $this->translator->get('SUCCESS_VERSION', ['old' => $oldVersion, 'new' => $newVersion]) . PHP_EOL;
    }
}
