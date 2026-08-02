<?php
namespace ArtiFrame\Cli\Commands;

use ArtiFrame\Cli\Services\Translator;
use ArtiFrame\Cli\Services\Safeguard;

class TableCommand
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
        $tableName = $args[0] ?? null;
        $action    = $args[1] ?? null;

        if (!$tableName) {
            echo "❌ " . $this->translator->get('ERROR_TABLE_NAME_REQUIRED') . PHP_EOL;
            return;
        }

        $stubPath  = \ARTIFRAME_CLI_ROOT . '/stubs/sql.stub';

        if (!file_exists($stubPath)) {
            echo "❌ " . $this->translator->get('ERROR_STUB_NOT_FOUND') . PHP_EOL;
            return;
        }

        $stubContent = file_get_contents($stubPath);

        // table list
        if ($tableName === 'list') {
            echo "\033[1;36m" . $this->translator->get('TABLE_LIST_HEADER') . "\033[0m" . PHP_EOL;
            preg_match_all('/-- \[TABLE:(.*?)\]/s', $stubContent, $matches);
            
            if (!empty($matches[1])) {
                foreach ($matches[1] as $table) {
                    $descKey = 'TABLE_DESC_' . strtoupper($table);
                    $desc = $this->translator->get($descKey);
                    if ($desc === $descKey) {
                        $desc = '-';
                    }
                    echo "  \033[0;32m" . str_pad($table, 20) . "\033[0m : " . $desc . PHP_EOL;
                }
            }
            return;
        }

        $pattern = "/-- \[TABLE:{$tableName}\](.*?)-- \[\/TABLE:{$tableName}\]/s";

        if (!preg_match($pattern, $stubContent, $matches)) {
            echo "❌ " . sprintf($this->translator->get('ERROR_TABLE_NOT_IN_STUB'), $tableName) . PHP_EOL;
            return;
        }

        $tableContent = trim($matches[1]);

        // table:name check
        if ($action === 'check') {
            echo "\033[1;33m--- SQL FOR `{$tableName}` ---\033[0m" . PHP_EOL;
            echo $tableContent . PHP_EOL;
            echo "\033[1;33m---------------------------\033[0m" . PHP_EOL;
            return;
        }

        $safeguard = new \ArtiFrame\Cli\Services\Safeguard($this->translator);
        $schemaPath = $safeguard->getProjectRoot() . '/schema.sql';

        if (!file_exists($schemaPath)) {
            file_put_contents($schemaPath, "-- ArtiFrame DB Schema\n-- Created: " . date('Y-m-d') . "\n");
        }

        file_put_contents($schemaPath, "\n" . $tableContent . "\n", FILE_APPEND);

        echo "✅ " . sprintf($this->translator->get('SUCCESS_TABLE_ADDED'), $tableName) . PHP_EOL;
    }
}
