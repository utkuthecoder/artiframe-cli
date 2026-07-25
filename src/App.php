<?php
namespace ArtiFrame\Cli;

use ArtiFrame\Cli\Services\Translator;

class App
{
    private array $originalArgs;
    private array $args;
    private Translator $translator;

    public function __construct(array $argv)
    {
        $this->originalArgs = $argv;
        $this->args = $argv;
        $this->initTranslator();
    }

    private function initTranslator(): void
    {
        $lang = $this->getOrAskLanguage();
        $this->translator = new Translator($lang);
    }

    private function getOrAskLanguage(): string
    {
        $home = getenv('HOME') ?: getenv('USERPROFILE');
        $configFile = $home ? $home . \DIRECTORY_SEPARATOR . '.artiframe_lang' : null;

        $lang = null;
        
        // 1. Check argument --lang=
        foreach ($this->originalArgs as $arg) {
            if (strpos($arg, '--lang=') === 0) {
                $lang = strtolower(substr($arg, 7, 2));
                if ($configFile) file_put_contents($configFile, $lang);
                return $lang;
            }
        }

        // 2. Check config file
        if ($configFile && file_exists($configFile)) {
            $lang = trim(file_get_contents($configFile));
            if (in_array($lang, ['tr', 'en', 'de', 'fr', 'es'])) {
                return $lang;
            }
        }

        // 3. Fallback check system env (optional, but asking is better)
        
        // 4. Ask user
        echo PHP_EOL;
        echo "\033[1;32mWelcome to ArtiFrame! / ArtiFrame'e Hoş Geldiniz!\033[0m" . PHP_EOL;
        echo "Please select your preferred language / Lütfen dilinizi seçin:" . PHP_EOL;
        echo "  [1] 🇹🇷 Türkçe (TR)" . PHP_EOL;
        echo "  [2] 🇬🇧 English (EN)" . PHP_EOL;
        echo "  [3] 🇩🇪 Deutsch (DE)" . PHP_EOL;
        echo "  [4] 🇫🇷 Français (FR)" . PHP_EOL;
        echo "  [5] 🇪🇸 Español (ES)" . PHP_EOL;
        echo PHP_EOL;
        
        while (true) {
            echo "Select [1-5]: ";
            $choice = trim(fgets(STDIN));
            $map = [
                '1' => 'tr',
                '2' => 'en',
                '3' => 'de',
                '4' => 'fr',
                '5' => 'es'
            ];
            
            if (isset($map[$choice])) {
                $lang = $map[$choice];
                if ($configFile) file_put_contents($configFile, $lang);
                echo PHP_EOL . "Language set to: " . strtoupper($lang) . PHP_EOL;
                return $lang;
            }
            echo "Invalid choice. Please try again." . PHP_EOL;
        }
    }

    public function run(): void
    {
        $script = array_shift($this->args);
        
        // Strip --lang flag globally for args
        $this->args = array_filter($this->args, function($arg) {
            return strpos($arg, '--lang=') !== 0;
        });
        $this->args = array_values($this->args);

        // If no arguments provided, enter Interactive Mode (REPL)
        if (empty($this->args)) {
            $this->runInteractiveShell();
            return;
        }

        // One-shot mode
        $commandName = array_shift($this->args);
        $this->routeCommand($commandName, $this->args);
    }

    private function runInteractiveShell(): void
    {
        $this->showBanner();

        while (true) {
            echo "\033[1;32martiframe\033[0m\033[38;5;240m>\033[0m ";
            $input = trim(fgets(STDIN));
            
            if ($input === '') {
                continue;
            }

            if (in_array(strtolower($input), ['exit', 'quit'])) {
                echo PHP_EOL . "\033[38;5;240m  " . $this->translator->get('SHELL_GOODBYE') . "\033[0m" . PHP_EOL . PHP_EOL;
                break;
            }

            $parts = explode(' ', $input);
            $parts = array_filter($parts, function($v) { return trim($v) !== ''; });
            $parts = array_values($parts);

            $commandName = array_shift($parts);

            // Support two-word commands: "cli v", "cli version", "make:view" etc.
            if ($commandName === 'cli' && !empty($parts)) {
                $commandName = 'cli ' . array_shift($parts);
            }
            
            try {
                $this->routeCommand($commandName, $parts);
            } catch (\Exception $e) {
                echo "\033[1;31m  ✖ " . $this->translator->get('SHELL_ERROR') . "\033[0m " . $e->getMessage() . PHP_EOL;
            }
            echo PHP_EOL;
        }
    }

    private function showBanner(): void
    {
        $g  = "\033[38;2;0;157;108m"; // #009d6c green
        $lg = "\033[38;2;0;200;140m"; // lighter green for accent
        $d  = "\033[38;2;80;80;80m";  // dim gray for decorators
        $w  = "\033[1;37m";            // white bold
        $r  = "\033[0m";               // reset

        echo PHP_EOL;
        echo $g . "  ┌─────────────────────────────────────────────────────────────┐" . $r . PHP_EOL;
        echo $g . "  │" . $r . PHP_EOL;
        echo $g . "  │" . $r . "  " . $g . "   █████╗ ██████╗ ████████╗██╗███████╗██████╗  █████╗ ███╗   ███╗███████╗" . $r . PHP_EOL;
        echo $g . "  │" . $r . "  " . $g . "  ██╔══██╗██╔══██╗╚══██╔══╝██║██╔════╝██╔══██╗██╔══██╗████╗ ████║██╔════╝" . $r . PHP_EOL;
        echo $g . "  │" . $r . "  " . $lg . "  ███████║██████╔╝   ██║   ██║█████╗  ██████╔╝███████║██╔████╔██║█████╗  " . $r . PHP_EOL;
        echo $g . "  │" . $r . "  " . $g . "  ██╔══██║██╔══██╗   ██║   ██║██╔══╝  ██╔══██╗██╔══██║██║╚██╔╝██║██╔══╝  " . $r . PHP_EOL;
        echo $g . "  │" . $r . "  " . $g . "  ██║  ██║██║  ██║   ██║   ██║██║     ██║  ██║██║  ██║██║ ╚═╝ ██║███████╗" . $r . PHP_EOL;
        echo $g . "  │" . $r . "  " . $g . "  ╚═╝  ╚═╝╚═╝  ╚═╝   ╚═╝   ╚═╝╚═╝     ╚═╝  ╚═╝╚═╝  ╚═╝╚═╝     ╚═╝╚══════╝" . $r . PHP_EOL;
        echo $g . "  │" . $r . PHP_EOL;
        echo $g . "  │" . $r . "  " . $d . "  ─────────────────────────────────────────────────────────" . $r . PHP_EOL;
        echo $g . "  │" . $r . "  " . $w . "  [A] Agile Circuit Core v" . ARTIFRAME_VERSION . $r . $d . "  │  Lightweight Native PHP Framework" . $r . PHP_EOL;
        echo $g . "  │" . $r . "  " . $d . "  ─────────────────────────────────────────────────────────" . $r . PHP_EOL;
        echo $g . "  │" . $r . PHP_EOL;
        echo $g . "  └─────────────────────────────────────────────────────────────┘" . $r . PHP_EOL;
        echo PHP_EOL;
        echo "  " . $d . $this->translator->get('SHELL_TYPE_HELP') . $r . PHP_EOL;
        echo PHP_EOL;
    }



    private function routeCommand(string $commandName, array $commandArgs): void
    {
        switch ($commandName) {
            case 'new':
                (new \ArtiFrame\Cli\Commands\NewProjectCommand($this->translator))->execute($commandArgs);
                break;
                
            case 'make:view':
                (new \ArtiFrame\Cli\Commands\MakeViewCommand($this->translator))->execute($commandArgs);
                break;
                
            case 'make:api':
                (new \ArtiFrame\Cli\Commands\MakeApiCommand($this->translator))->execute($commandArgs);
                break;
                
            case 'make:class':
                (new \ArtiFrame\Cli\Commands\MakeClassCommand($this->translator))->execute($commandArgs);
                break;
                
            case 'lang':
                (new \ArtiFrame\Cli\Commands\LangCommand($this->translator))->execute($commandArgs);
                break;

            case 'version':
                // Always routes to project version manager
                (new \ArtiFrame\Cli\Commands\VersionCommand($this->translator))->execute($commandArgs);
                break;

            case 'cli version':
            case 'cli v':
                (new \ArtiFrame\Cli\Commands\CliVersionCommand($this->translator))->execute();
                break;

            case 'help':
            default:
                $this->showHelp();
                break;
        }
    }

    private function showHelp(): void
    {
        $g  = "\033[38;2;0;157;108m";  // #009d6c green
        $lg = "\033[38;2;0;200;140m";  // light green
        $y  = "\033[38;5;220m";         // yellow for args
        $d  = "\033[38;2;100;100;100m"; // dim
        $w  = "\033[1;37m";             // white bold
        $c  = "\033[38;5;81m";          // cyan for sub-options
        $r  = "\033[0m";                // reset

        $t  = $this->translator;
        echo PHP_EOL;
        echo "  " . $w . $t->get('HELP_COMMANDS') . $r . PHP_EOL;
        echo "  " . $d . "─────────────────────────────────────────────────────────────" . $r . PHP_EOL;
        echo PHP_EOL;

        // new
        echo "  " . $g . "new" . $r . " " . $y . "<project-path>" . $r . PHP_EOL;
        echo "  " . $d . "│" . $r . "   " . $t->get('HELP_NEW_DESC') . PHP_EOL;
        echo "  " . $d . "└── " . $r . "Example: " . $lg . "new my-app" . $r . PHP_EOL;
        echo PHP_EOL;

        // make:view
        echo "  " . $g . "make:view" . $r . " " . $y . "<path>" . $r . PHP_EOL;
        echo "  " . $d . "│" . $r . "   " . $t->get('HELP_MAKEVIEW_DESC1') . PHP_EOL;
        echo "  " . $d . "│" . $r . "   " . $t->get('HELP_MAKEVIEW_DESC2') . PHP_EOL;
        echo "  " . $d . "└── " . $r . "Example: " . $lg . "make:view pages/about.php" . $r . PHP_EOL;
        echo PHP_EOL;

        // make:api
        echo "  " . $g . "make:api" . $r . " " . $y . "<template>" . $r . " " . $y . "<path>" . $r . PHP_EOL;
        echo "  " . $d . "│" . $r . "   " . $t->get('HELP_MAKEAPI_DESC') . PHP_EOL;
        echo "  " . $d . "│" . $r . PHP_EOL;
        echo "  " . $d . "├── " . $c . "standart" . $r . "      " . $t->get('HELP_MAKEAPI_STANDART') . PHP_EOL;
        echo "  " . $d . "│               " . $r . "Example: " . $lg . "make:api standart api/user/get.php" . $r . PHP_EOL;
        echo "  " . $d . "│" . $r . PHP_EOL;
        echo "  " . $d . "└── " . $c . "switch-case" . $r . "   " . $t->get('HELP_MAKEAPI_SWITCH') . PHP_EOL;
        echo "  " . $d . "                " . $r . "Example: " . $lg . "make:api switch-case api/user/crud.php" . $r . PHP_EOL;
        echo PHP_EOL;

        // make:class
        echo "  " . $g . "make:class" . $r . " " . $y . "<path>" . $r . PHP_EOL;
        echo "  " . $d . "│" . $r . "   " . $t->get('HELP_MAKECLASS_DESC') . PHP_EOL;
        echo "  " . $d . "└── " . $r . "Example: " . $lg . "make:class app/Services/UserManager.php" . $r . PHP_EOL;
        echo PHP_EOL;

        // cli v / cli version — CLI self-version
        echo "  " . $g . "cli v" . $r . " / " . $g . "cli version" . $r . PHP_EOL;
        echo "  " . $d . "│" . $r . "   " . $t->get('HELP_CLI_VERSION_DESC') . PHP_EOL;
        echo "  " . $d . "└── " . $r . "Example: " . $lg . "cli v" . $r . PHP_EOL;
        echo PHP_EOL;

        // version upgrade/downgrade — project version manager
        echo "  " . $g . "version" . $r . " " . $y . "<action>" . $r . " " . $y . "<level>" . $r . PHP_EOL;
        echo "  " . $d . "│" . $r . "   " . $t->get('HELP_VERSION_DESC1') . PHP_EOL;
        echo "  " . $d . "│" . $r . "   " . $t->get('HELP_VERSION_FORMAT') . PHP_EOL;
        echo "  " . $d . "│" . $r . PHP_EOL;
        echo "  " . $d . "├── " . $c . "upgrade" . $r . PHP_EOL;
        echo "  " . $d . "│   ├── " . $c . "patch" . $r . "   " . $t->get('HELP_PATCH_UP') . "     " . $d . "1.0.0 → 1.0.1" . $r . PHP_EOL;
        echo "  " . $d . "│   ├── " . $c . "minor" . $r . "   " . $t->get('HELP_MINOR_UP') . " " . $d . "1.0.0 → 1.1.0" . $r . PHP_EOL;
        echo "  " . $d . "│   └── " . $c . "major" . $r . "   " . $t->get('HELP_MAJOR_UP') . "            " . $d . "1.0.0 → 2.0.0" . $r . PHP_EOL;
        echo "  " . $d . "│" . $r . PHP_EOL;
        echo "  " . $d . "└── " . $c . "downgrade" . $r . PHP_EOL;
        echo "  " . $d . "    ├── " . $c . "patch" . $r . "   " . $t->get('HELP_PATCH_DOWN') . "         " . $d . "1.0.3 → 1.0.2" . $r . PHP_EOL;
        echo "  " . $d . "    ├── " . $c . "minor" . $r . "   " . $t->get('HELP_MINOR_DOWN') . "  " . $d . "1.3.0 → 1.2.0" . $r . PHP_EOL;
        echo "  " . $d . "    └── " . $c . "major" . $r . "   " . $t->get('HELP_MAJOR_DOWN') . "  " . $d . "3.0.0 → 2.0.0" . $r . PHP_EOL;
        echo PHP_EOL;
        echo "  " . $d . "    Example: " . $lg . "version upgrade minor" . $r . PHP_EOL;
        echo "  " . $d . "    Example: " . $lg . "version downgrade patch" . $r . PHP_EOL;
        echo PHP_EOL;

        // lang
        echo "  " . $g . "lang" . $r . PHP_EOL;
        echo "  " . $d . "│" . $r . "   " . $t->get('HELP_LANG_DESC') . PHP_EOL;
        echo "  " . $d . "├── " . $r . "Example: " . $lg . "lang" . $r . $d . "        (interactive)" . $r . PHP_EOL;
        echo "  " . $d . "└── " . $r . "Example: " . $lg . "lang en" . $r . $d . "     (direct)" . $r . PHP_EOL;
        echo PHP_EOL;

        // help / exit
        echo "  " . $d . "─────────────────────────────────────────────────────────────" . $r . PHP_EOL;
        echo "  " . $g . "help" . $r . "           " . $t->get('HELP_HELP_DESC') . PHP_EOL;
        echo "  " . $g . "exit" . $r . " / " . $g . "quit" . $r . "   " . $t->get('HELP_EXIT_DESC') . PHP_EOL;
        echo PHP_EOL;
    }
}
