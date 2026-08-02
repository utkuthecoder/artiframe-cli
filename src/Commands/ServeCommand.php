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
        $safeguard = new \ArtiFrame\Cli\Services\Safeguard($this->translator);
        $projectRoot = $safeguard->getProjectRoot();
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

        // Detect Local IP
        $localIp = 'Unknown';
        $sock = @socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        if ($sock) {
            @socket_connect($sock, "8.8.8.8", 53);
            @socket_getsockname($sock, $localIp);
            @socket_close($sock);
        }
        if ($localIp === 'Unknown' || $localIp === '127.0.0.1' || $localIp === '::1' || empty($localIp)) {
            $localIp = getHostByName(getHostName());
        }

        $localUrl = "http://localhost:{$port}";
        $networkUrl = "http://{$localIp}:{$port}";
        
        echo "
  🚀  " . $this->translator->get('SERVE_STARTING', ['url' => "0.0.0.0:{$port}"]) . "
";
        echo "      " . $this->translator->get('SERVE_STOP_INFO') . "

      Local:   {$localUrl}";
        
        if ($localIp && $localIp !== '127.0.0.1' && $localIp !== 'Unknown') {
            echo "
      Network: {$networkUrl}";
        }
        echo "\n\n";

        $os = PHP_OS_FAMILY;
        
        // Command to start PHP built-in server on all interfaces (0.0.0.0)
        $phpCmd = sprintf('php -S 0.0.0.0:%s -t %s', $port, escapeshellarg($publicDir));

        if ($os === 'Windows') {
            // Open Browser
            pclose(popen("start {$localUrl}", "r"));
            // Start Server in new visible CMD window
            pclose(popen("start \"ArtiFrame Server\" cmd /k \"{$phpCmd}\"", "r"));
        } elseif ($os === 'Darwin') {
            // Open Browser
            exec("open {$localUrl} > /dev/null 2>&1 &");
            // Start Server in new Terminal window
            $appleScript = 'tell app "Terminal" to do script "' . escapeshellcmd($phpCmd) . '"';
            exec("osascript -e " . escapeshellarg($appleScript));
        } else {
            // Open Browser
            exec("xdg-open {$localUrl} > /dev/null 2>&1 &");
            // Linux: Try common terminals, fallback to background process
            $linuxCmd = sprintf(
                'gnome-terminal -- bash -c "%s" || xterm -e "%s" || konsole -e "%s" || %s > /dev/null 2>&1 &',
                $phpCmd, $phpCmd, $phpCmd, $phpCmd
            );
            exec($linuxCmd);
        }
    }
}
