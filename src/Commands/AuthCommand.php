<?php
namespace ArtiFrame\Cli\Commands;

use ArtiFrame\Cli\Services\Translator;

class AuthCommand
{
    private Translator $translator;
    
    // Placeholder Client ID as requested
    const CLIENT_ID = 'Ov23libWFmWDGtyaSEiT';

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function execute(array $args): void
    {
        echo "\033[1;36mInitializing GitHub Authentication...\033[0m" . PHP_EOL;

        // 1. Request Device Code
        $deviceCodeData = $this->requestDeviceCode();
        if (!$deviceCodeData) {
            return;
        }

        $deviceCode = $deviceCodeData['device_code'];
        $userCode = $deviceCodeData['user_code'];
        $verificationUri = $deviceCodeData['verification_uri'];
        $interval = (int)($deviceCodeData['interval'] ?? 5);

        // 2. Display instructions
        echo PHP_EOL;
        echo "\033[1;33mAction Required:\033[0m" . PHP_EOL;
        echo "1. Please open your browser to: \033[1;34m" . $verificationUri . "\033[0m" . PHP_EOL;
        echo "2. Enter the following code to authorize: \033[1;32m" . $userCode . "\033[0m" . PHP_EOL;
        echo PHP_EOL;
        echo "Waiting for authorization..." . PHP_EOL;

        // 3. Open browser automatically
        $this->openBrowser($verificationUri);

        // 4. Poll for Access Token
        $accessToken = $this->pollForToken($deviceCode, $interval);
        
        if ($accessToken) {
            // 5. Fetch user profile
            $userProfile = $this->fetchUserProfile($accessToken);
            $username = $userProfile['login'] ?? 'Unknown User';

            // 6. Save configuration
            $this->saveConfig($accessToken, $username);
            
            echo "\033[1;32m✔ Successfully authenticated as @{$username}!\033[0m" . PHP_EOL;
        }
    }

    private function requestDeviceCode(): ?array
    {
        $ch = curl_init('https://github.com/login/device/code');
        $postData = http_build_query([
            'client_id' => self::CLIENT_ID,
            'scope' => 'public_repo'
        ]);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'ArtiFrame-CLI');

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpCode !== 200 || !$response) {
            echo "\033[1;31m[-] Failed to request device code from GitHub.\033[0m" . PHP_EOL;
            curl_close($ch);
            return null;
        }
        curl_close($ch);

        return json_decode($response, true);
    }

    private function pollForToken(string $deviceCode, int $interval): ?string
    {
        $url = 'https://github.com/login/oauth/access_token';
        
        while (true) {
            sleep($interval);

            $ch = curl_init($url);
            $postData = http_build_query([
                'client_id' => self::CLIENT_ID,
                'device_code' => $deviceCode,
                'grant_type' => 'urn:ietf:params:oauth:grant-type:device_code'
            ]);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json'
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'ArtiFrame-CLI');

            $response = curl_exec($ch);
            curl_close($ch);
            
            if ($response) {
                $data = json_decode($response, true);
                
                if (isset($data['access_token'])) {
                    return $data['access_token'];
                }
                
                if (isset($data['error'])) {
                    if ($data['error'] === 'authorization_pending') {
                        // Keep waiting
                        continue;
                    } elseif ($data['error'] === 'slow_down') {
                        $interval += 5; // Slow down polling
                        continue;
                    } elseif ($data['error'] === 'expired_token') {
                        echo "\033[1;31m[-] The device code expired. Please run the auth command again.\033[0m" . PHP_EOL;
                        return null;
                    } elseif ($data['error'] === 'access_denied') {
                        echo "\033[1;31m[-] Authorization was denied.\033[0m" . PHP_EOL;
                        return null;
                    } else {
                        echo "\033[1;31m[-] An error occurred: " . $data['error'] . "\033[0m" . PHP_EOL;
                        return null;
                    }
                }
            }
        }
    }

    private function fetchUserProfile(string $token): ?array
    {
        $ch = curl_init('https://api.github.com/user');
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/vnd.github.v3+json',
            'Authorization: Bearer ' . $token,
            'User-Agent: ArtiFrame-CLI'
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            return json_decode($response, true);
        }

        return null;
    }

    private function saveConfig(string $token, string $username): void
    {
        $home = getenv('HOME') ?: getenv('USERPROFILE');
        $configDir = $home . \DIRECTORY_SEPARATOR . '.artiframe';
        
        if (!is_dir($configDir)) {
            mkdir($configDir, 0755, true);
        }

        $configFile = $configDir . \DIRECTORY_SEPARATOR . 'config.json';
        
        $config = [];
        if (file_exists($configFile)) {
            $existing = json_decode(file_get_contents($configFile), true);
            if (is_array($existing)) {
                $config = $existing;
            }
        }

        $config['github_token'] = $token;
        $config['github_user'] = $username;
        $config['logged_in_at'] = date('c');

        file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT));
    }

    private function openBrowser(string $url): void
    {
        $os = php_uname('s');
        $command = '';

        if (stripos($os, 'win') === 0) {
            $command = 'start "" "' . $url . '"';
        } elseif (stripos($os, 'darwin') === 0) {
            $command = 'open "' . $url . '"';
        } else {
            $command = 'xdg-open "' . $url . '"';
        }

        if ($command) {
            // execute command silently in background
            if (stripos($os, 'win') === 0) {
                pclose(popen($command, "r"));
            } else {
                exec($command . ' > /dev/null 2>&1 &');
            }
        }
    }
}
