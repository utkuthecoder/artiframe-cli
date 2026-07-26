<?php
namespace ArtiFrame\Cli\Commands;

use ArtiFrame\Cli\Services\Translator;

class ListCommand
{
    private Translator $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function execute(array $args): void
    {
        $dir = getcwd();
        $projectName = basename($dir);
        
        echo "
  📂 " . $projectName . "/
";

        $priorityOrder = [
            'bin', 'config', 'src', 'app', 'public', '.agents',
            'vendor', '.env', '.env.example', '.gitignore',
            'composer.json', 'composer.lock', 'schema.sql',
            'README.md', 'OKUBENI.md', 'LESEMICH.md', 'LISEZMOI.md', 'LEAME.md',
            'kilavuz.html', 'guide.html', 'handbuch.html', 'guide_fr.html', 'guia.html', 'LICENSE',
        ];

        $entries = array_filter(scandir($dir), fn($e) => $e !== '.' && $e !== '..');

        usort($entries, function ($a, $b) use ($priorityOrder) {
            $ia = array_search($a, $priorityOrder);
            $ib = array_search($b, $priorityOrder);
            $ia = ($ia === false) ? 99 : $ia;
            $ib = ($ib === false) ? 99 : $ib;
            return $ia <=> $ib;
        });

        $entries = array_values($entries);
        $total   = count($entries);

        foreach ($entries as $i => $entry) {
            $isLast    = ($i === $total - 1);
            $connector = $isLast ? '└──' : '├──';
            $fullPath  = $dir . DIRECTORY_SEPARATOR . $entry;

            if (is_dir($fullPath)) {
                $subItems = array_filter(scandir($fullPath), fn($e) => $e !== '.' && $e !== '..');
                $subCount = count($subItems);
                $countStr = $subCount > 0 ? '  ' . $this->translator->get('DIR_ITEM_COUNT', ['count' => $subCount]) : '';
                echo "  │  {$connector} 📂 {$entry}/{$countStr}
";
            } else {
                $size    = filesize($fullPath);
                $sizeStr = $size > 1024 ? round($size / 1024, 1) . ' KB' : $size . ' B';
                echo "  │  {$connector} 📄 {$entry}  [{$sizeStr}]
";
            }
        }
        echo "  │

";
    }
}
