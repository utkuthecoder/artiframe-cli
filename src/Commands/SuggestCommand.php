<?php
namespace ArtiFrame\Cli\Commands;

use ArtiFrame\Cli\Services\Translator;

class SuggestCommand
{
    private Translator $translator;
    const API_ENDPOINT = 'https://api.artilingo.com/artiframe/v1/suggest.php';

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function execute(array $args): void
    {
        $home = getenv('HOME') ?: getenv('USERPROFILE');
        $configFile = $home . \DIRECTORY_SEPARATOR . '.artiframe' . \DIRECTORY_SEPARATOR . 'config.json';

        if (!file_exists($configFile)) {
            echo "\033[1;31m[-] Not authenticated. Please run 'artiframe auth' first.\033[0m" . PHP_EOL;
            return;
        }

        $config = json_decode(file_get_contents($configFile), true);
        if (!$config || !isset($config['github_token'])) {
            echo "\033[1;31m[-] Not authenticated. Please run 'artiframe auth' first.\033[0m" . PHP_EOL;
            return;
        }

        $token = $config['github_token'];
        $username = $config['github_user'] ?? 'User';

        echo "\033[1;36mHello @{$username}, welcome to ArtiFrame Issue Submitter!\033[0m" . PHP_EOL;
        echo PHP_EOL;

        // 1. Ask for Category
        $categories = [
            '1' => 'Feature Request',
            '2' => 'Bug Report',
            '3' => 'Helper Suggestion'
        ];

        echo "Please select a Category:" . PHP_EOL;
        echo "  [1] Feature Request" . PHP_EOL;
        echo "  [2] Bug Report" . PHP_EOL;
        echo "  [3] Helper Suggestion" . PHP_EOL;
        
        $categoryId = null;
        while (true) {
            echo "Select [1-3]: ";
            $input = trim(fgets(STDIN));
            if (isset($categories[$input])) {
                $categoryId = $input;
                break;
            }
            echo "\033[1;31mInvalid selection.\033[0m" . PHP_EOL;
        }

        $category = $categories[$categoryId];

        // 2. Ask for Title
        echo PHP_EOL;
        $title = '';
        while (true) {
            echo "Title (Short summary, max 120 chars): ";
            $title = trim(fgets(STDIN));
            if (strlen($title) === 0) {
                echo "\033[1;31mTitle cannot be empty.\033[0m" . PHP_EOL;
            } elseif (strlen($title) > 120) {
                echo "\033[1;31mTitle is too long. Max 120 chars.\033[0m" . PHP_EOL;
            } else {
                break;
            }
        }

        // 3. Ask for Description
        echo PHP_EOL;
        echo "Description (Detailed explanation or code sample):" . PHP_EOL;
        echo "\033[1;33m(Type 'END' on a new line and press Enter to finish)\033[0m" . PHP_EOL;
        
        $bodyLines = [];
        while (true) {
            $line = fgets(STDIN);
            if ($line === false || trim(strtoupper($line)) === 'END') {
                break;
            }
            $bodyLines[] = rtrim($line, "\r\n");
        }
        $body = implode(PHP_EOL, $bodyLines);

        if (empty(trim($body))) {
            echo "\033[1;31m[-] Description cannot be empty. Aborting.\033[0m" . PHP_EOL;
            return;
        }

        // 4. Send Payload
        echo PHP_EOL . "\033[1;36mSubmitting to ArtiFrame...\033[0m" . PHP_EOL;

        $payload = json_encode([
            'token' => $token,
            'category' => $category,
            'title' => $title,
            'body' => $body
        ]);

        $ch = curl_init(self::API_ENDPOINT);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'ArtiFrame-CLI');

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response) {
            $data = json_decode($response, true);
            if ($httpCode === 200 && isset($data['status']) && $data['status'] === 'success') {
                echo "\033[1;32m🎉 Issue successfully created!\033[0m" . PHP_EOL;
                echo "URL: " . ($data['issue_url'] ?? 'N/A') . PHP_EOL;
            } else {
                if ($data && isset($data['message'])) {
                    echo "\033[1;31m[-] Server Error: " . $data['message'] . "\033[0m" . PHP_EOL;
                } else {
                    echo "\033[1;31m[-] Server Error: Unknown response format.\033[0m" . PHP_EOL;
                    echo "HTTP Code: " . $httpCode . PHP_EOL;
                    echo "Raw Response: " . substr($response, 0, 300) . PHP_EOL;
                }
            }
        } else {
            echo "\033[1;31m[-] Failed to communicate with the server.\033[0m" . PHP_EOL;
            echo "cURL Error: " . curl_error($ch) . PHP_EOL;
        }
    }
}
