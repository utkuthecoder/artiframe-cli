<?php
namespace ArtiFrame\Cli\Commands;

use ArtiFrame\Cli\Services\Translator;
use ArtiFrame\Cli\Services\Safeguard;

class DbExportCommand
{
    private Translator $translator;
    private Safeguard  $safeguard;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
        $this->safeguard  = new Safeguard($translator);
    }

    private function parseEnvFile(string $path): array
    {
        $env = [];
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if (strpos($line, '#') === 0) continue;
            if (strpos($line, '=') !== false) {
                list($key, $val) = explode('=', $line, 2);
                $env[trim($key)] = trim($val, " \t\n\r\0\x0B\"'");
            }
        }
        return $env;
    }

    public function execute(array $args): void
    {
        $projectRoot = $this->safeguard->getProjectRoot();
        
        if (!file_exists($projectRoot . '/.env')) {
            echo "❌ .env dosyasi bulunamadi. Bu komut sadece projelerin icinde calisir.\n";
            return;
        }
        
        $mode = $args[0] ?? null;
        if (!in_array($mode, ['within', 'without'])) {
            echo "❌ Gecersiz parametre. Kullanim:\n";
            echo "   artiframe db:export within  (Yapi ve Verileri disari aktarir)\n";
            echo "   artiframe db:export without (Sadece tablo yapisini disari aktarir)\n";
            return;
        }

        // Read .env securely
        $env = $this->parseEnvFile($projectRoot . '/.env');
        $dbHost = $env['DB_HOST'] ?? 'localhost';
        $dbUser = $env['DB_USER'] ?? ($env['DB_USERNAME'] ?? 'root');
        $dbPass = $env['DB_PASS'] ?? ($env['DB_PASSWORD'] ?? '');
        $dbName = $env['DB_NAME'] ?? ($env['DB_DATABASE'] ?? '');
        
        if (!$dbName) {
            echo "❌ .env dosyasinda DB_NAME ayarlanmamis.\n";
            return;
        }

        echo "🔄 Veritabani disari aktariliyor: {$dbName} (" . ($mode === 'within' ? 'Yapi + Veri' : 'Sadece Yapi') . ")...\n";

        try {
            $pdo = new \PDO("mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4", $dbUser, $dbPass, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
            ]);
            
            $sql = "-- ArtiFrame Veritabani Yedegi\n";
            $sql .= "-- Tarih: " . date('Y-m-d H:i:s') . "\n";
            $sql .= "-- Veritabani: {$dbName}\n";
            $sql .= "-- Mod: {$mode}\n\n";
            $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            $tablesQuery = $pdo->query("SHOW TABLES");
            $tables = $tablesQuery->fetchAll(\PDO::FETCH_COLUMN);

            foreach ($tables as $table) {
                // Table structure
                $sql .= "-- --------------------------------------------------------\n";
                $sql .= "-- Tablo Yapisi: `{$table}`\n";
                $sql .= "-- --------------------------------------------------------\n";
                $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
                
                $createRow = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(\PDO::FETCH_ASSOC);
                $sql .= $createRow['Create Table'] . ";\n\n";

                // Table data (if within)
                if ($mode === 'within') {
                    $rows = $pdo->query("SELECT * FROM `{$table}`")->fetchAll(\PDO::FETCH_ASSOC);
                    if (count($rows) > 0) {
                        $sql .= "-- Tablo Verisi: `{$table}`\n";
                        
                        $chunks = array_chunk($rows, 100); // 100 rows per INSERT
                        foreach ($chunks as $chunk) {
                            $keys = array_keys($chunk[0]);
                            $keysList = implode("\`, \`", $keys);
                            $sql .= "INSERT INTO `{$table}` (`{$keysList}`) VALUES \n";
                            
                            $valuesLines = [];
                            foreach ($chunk as $row) {
                                $vals = array_map(function($val) use ($pdo) {
                                    if ($val === null) return 'NULL';
                                    return $pdo->quote($val);
                                }, array_values($row));
                                $valuesLines[] = "(" . implode(", ", $vals) . ")";
                            }
                            $sql .= implode(",\n", $valuesLines) . ";\n";
                        }
                        $sql .= "\n";
                    }
                }
            }
            
            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

            $filename = "export_{$mode}_{$dbName}_" . date('Ymd_His') . ".sql";
            $exportPath = $projectRoot . '/' . $filename;
            
            file_put_contents($exportPath, $sql);
            
            echo "✅ Basarili! Veritabani disa aktarildi.\n";
            echo "📂 Dosya: {$exportPath}\n";

        } catch (\PDOException $e) {
            echo "❌ Veritabani hatasi: " . $e->getMessage() . "\n";
        } catch (\Exception $e) {
            echo "❌ Hata: " . $e->getMessage() . "\n";
        }
    }
}
