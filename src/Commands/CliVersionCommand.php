<?php
namespace ArtiFrame\Cli\Commands;

use ArtiFrame\Cli\Services\Translator;

/**
 * CliVersionCommand
 *
 * Shows the installed CLI version, checks npm registry for the latest,
 * and optionally self-updates via `npm install -g`.
 */
class CliVersionCommand
{
    private Translator $translator;

    private const PACKAGE    = '@artilingo/artiframe-cli';
    private const REGISTRY   = 'https://registry.npmjs.org/%40artilingo%2Fartiframe-cli/latest';

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function execute(): void
    {
        $t = $this->translator;

        $g = "\033[38;2;0;157;108m";
        $w = "\033[1;37m";
        $d = "\033[38;2;100;100;100m";
        $y = "\033[38;5;220m";
        $r = "\033[0m";

        $current = $this->getCurrentVersion();

        echo PHP_EOL;
        echo "  {$w}" . $t->get('VERSION_CURRENT') . ":{$r}  {$g}v{$current}{$r}" . PHP_EOL;
        echo "  {$d}" . $t->get('VERSION_CHECKING') . "{$r}";
        flush();

        $latest = $this->fetchLatestVersion();

        // Overwrite the "checking..." line
        echo "\r" . str_repeat(' ', 70) . "\r";

        if ($latest === null) {
            echo "  {$y}" . $t->get('VERSION_NETWORK_ERROR') . "{$r}" . PHP_EOL . PHP_EOL;
            return;
        }

        echo "  {$w}" . $t->get('VERSION_LATEST') . ":{$r}  {$g}v{$latest}{$r}" . PHP_EOL;
        echo PHP_EOL;

        if (version_compare($latest, $current, '<=')) {
            echo "  " . $t->get('VERSION_UP_TO_DATE') . PHP_EOL . PHP_EOL;
            return;
        }

        // ── Update available ─────────────────────────────────────
        echo "  {$y}" . $t->get('VERSION_UPDATE_AVAILABLE') . "{$r}"
           . "  {$d}v{$current}{$r} → {$g}v{$latest}{$r}" . PHP_EOL;
        echo PHP_EOL;
        echo "  " . $t->get('VERSION_UPDATE_PROMPT') . " ";
        flush();

        $answer  = strtolower(trim(fgets(STDIN)));
        $yesKeys = array_map('trim', explode(',', $t->get('VERSION_YES_KEYS')));

        if (!in_array($answer, $yesKeys, true)) {
            echo PHP_EOL . "  {$d}" . $t->get('VERSION_UPDATE_CANCELLED') . "{$r}" . PHP_EOL . PHP_EOL;
            return;
        }

        echo PHP_EOL;
        echo "  " . $t->get('VERSION_UPDATING') . PHP_EOL;
        echo PHP_EOL;

        passthru('npm install -g ' . self::PACKAGE . '@latest --prefer-online', $exitCode);

        echo PHP_EOL;
        if ($exitCode === 0) {
            echo "  {$g}" . $t->get('VERSION_UPDATE_SUCCESS') . "{$r}" . PHP_EOL;
        } else {
            echo "  " . $t->get('VERSION_UPDATE_FAILED') . PHP_EOL;
        }
        echo PHP_EOL;
    }

    // ── Helpers ──────────────────────────────────────────────────

    private function getCurrentVersion(): string
    {
        $pkgFile = ARTIFRAME_CLI_ROOT . '/package.json';
        if (file_exists($pkgFile)) {
            $pkg = json_decode(file_get_contents($pkgFile), true);
            if (!empty($pkg['version'])) {
                return $pkg['version'];
            }
        }
        return defined('ARTIFRAME_VERSION') ? ARTIFRAME_VERSION : '?.?.?';
    }

    private function fetchLatestVersion(): ?string
    {
        $context = stream_context_create([
            'http' => [
                'method'          => 'GET',
                'header'          => "Accept: application/json\r\nUser-Agent: artiframe-cli\r\n",
                'timeout'         => 6,
                'ignore_errors'   => true,
            ],
            'ssl'  => [
                'verify_peer'      => true,
                'verify_peer_name' => true,
            ],
        ]);

        $raw = @file_get_contents(self::REGISTRY, false, $context);
        if ($raw === false) {
            return null;
        }

        $data = json_decode($raw, true);
        return isset($data['version']) && is_string($data['version'])
            ? $data['version']
            : null;
    }
}
