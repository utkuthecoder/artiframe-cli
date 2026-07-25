<?php
namespace ArtiFrame\Cli\Commands;

use ArtiFrame\Cli\Services\Translator;

class LangCommand
{
    private Translator $translator;

    private const SUPPORTED = [
        '1' => ['code' => 'tr', 'label' => '🇹🇷  Türkçe      (TR)'],
        '2' => ['code' => 'en', 'label' => '🇬🇧  English     (EN)'],
        '3' => ['code' => 'de', 'label' => '🇩🇪  Deutsch     (DE)'],
        '4' => ['code' => 'fr', 'label' => '🇫🇷  Français    (FR)'],
        '5' => ['code' => 'es', 'label' => '🇪🇸  Español     (ES)'],
    ];

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function execute(array $args): void
    {
        $current = $this->translator->getLang();

        // If a language code is passed directly (e.g. `lang en`)
        if (!empty($args[0])) {
            $code = strtolower($args[0]);
            $valid = array_column(self::SUPPORTED, 'code');
            if (!in_array($code, $valid)) {
                echo "❌ " . $this->translator->get('LANG_INVALID', ['code' => $code]) . PHP_EOL;
                echo $this->translator->get('LANG_VALID_LIST') . ': tr, en, de, fr, es' . PHP_EOL;
                return;
            }
            $this->saveAndConfirm($code, $current);
            return;
        }

        // Interactive picker
        $g = "\033[38;2;0;157;108m";
        $d = "\033[38;2;100;100;100m";
        $w = "\033[1;37m";
        $r = "\033[0m";

        echo PHP_EOL;
        echo "  " . $w . $this->translator->get('LANG_CURRENT') . $r . "  " . $g . strtoupper($current) . $r . PHP_EOL;
        echo "  " . $d . $this->translator->get('LANG_SELECT') . $r . PHP_EOL;
        echo PHP_EOL;

        foreach (self::SUPPORTED as $num => $info) {
            $marker = ($info['code'] === $current) ? $g . " ✔" . $r : "  ";
            echo "  {$marker}  [{$num}] " . $info['label'] . PHP_EOL;
        }

        echo PHP_EOL;

        while (true) {
            echo "  " . $this->translator->get('LANG_PROMPT') . " ";
            $choice = trim(fgets(STDIN));

            if ($choice === '' || $choice === '0') {
                echo "  " . $d . $this->translator->get('LANG_UNCHANGED') . $r . PHP_EOL . PHP_EOL;
                return;
            }

            if (isset(self::SUPPORTED[$choice])) {
                $this->saveAndConfirm(self::SUPPORTED[$choice]['code'], $current);
                return;
            }

            echo "  ❌ " . $this->translator->get('LANG_INVALID_CHOICE') . PHP_EOL;
        }
    }

    private function saveAndConfirm(string $newCode, string $oldCode): void
    {
        $home       = getenv('HOME') ?: getenv('USERPROFILE');
        $configFile = $home ? $home . DIRECTORY_SEPARATOR . '.artiframe_lang' : null;

        if ($configFile) {
            file_put_contents($configFile, $newCode);
        }

        $g = "\033[38;2;0;157;108m";
        $r = "\033[0m";

        if ($newCode === $oldCode) {
            echo PHP_EOL . "  " . $this->translator->get('LANG_ALREADY_SET', ['lang' => strtoupper($newCode)]) . PHP_EOL . PHP_EOL;
            return;
        }

        echo PHP_EOL;
        echo "  ✅ " . $this->translator->get('LANG_CHANGED', [
            'old' => strtoupper($oldCode),
            'new' => $g . strtoupper($newCode) . $r,
        ]) . PHP_EOL;
        echo "  " . $this->translator->get('LANG_RESTART_TIP') . PHP_EOL . PHP_EOL;
    }
}
