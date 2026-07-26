<?php
namespace ArtiFrame\Cli\Commands;

use ArtiFrame\Cli\Services\Translator;

class ServeCommand
{
    private Translator $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function execute(array $args): void
    {
        $projectRoot = getcwd();
        $publicDir   = $projectRoot . '/public';

        if (!is_dir($publicDir) || !file_exists($publicDir . '/index.php')) {
            echo "
  ❌  " . $this->translator->get('SERVE_NOT_PROJECT') . "

";
            return;
        }

        $host = 'localhost';
        $port = '8000';

        if (isset($args[0]) && is_numeric($args[0])) {
            $port = $args[0];
        }

        $url = "http://{$host}:{$port}";
        
        echo "
  🚀  " . $this->translator->get('SERVE_STARTING', ['url' => $url]) . "
";
        echo "      " . $this->translator->get('SERVE_STOP_INFO') . "

";

        $os = PHP_OS_FAMILY;
        
        // Command to start PHP built-in server
        $phpCmd = sprintf('php -S %s:%s -t %s', $host, $port, escapeshellarg($publicDir));

        if ($os === 'Windows') {
            // Open Browser
            pclose(popen("start {$url}", "r"));
            // Start Server in new visible CMD window
            pclose(popen("start \"ArtiFrame Server\" cmd /k \"{$phpCmd}\"", "r"));
        } elseif ($os === 'Darwin') {
            // Open Browser
            exec("open {$url} > /dev/null 2>&1 &");
            // Start Server in new Terminal window
            $appleScript = 'tell app "Terminal" to do script "' . escapeshellcmd($phpCmd) . '"';
            exec("osascript -e " . escapeshellarg($appleScript));
        } else {
            // Open Browser
            exec("xdg-open {$url} > /dev/null 2>&1 &");
            // Linux: Try common terminals, fallback to background process
            $linuxCmd = sprintf(
                'gnome-terminal -- bash -c "%s" || xterm -e "%s" || konsole -e "%s" || %s > /dev/null 2>&1 &',
                $phpCmd, $phpCmd, $phpCmd, $phpCmd
            );
            exec($linuxCmd);
        }
    }
}
