<?php
namespace ArtiFrame\Cli\Commands;

use ArtiFrame\Cli\Services\Translator;

class IssuesCommand
{
    private Translator $translator;
    const REPO = 'utkuthecoder/artiframe-cli';

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function execute(array $args = []): void
    {
        $home = getenv('HOME') ?: getenv('USERPROFILE');
        $configPath = $home . \DIRECTORY_SEPARATOR . '.artiframe' . \DIRECTORY_SEPARATOR . 'config.json';

        if (!file_exists($configPath)) {
            echo "\033[1;31m[-] " . $this->translator->get('SUGGEST_NOT_AUTH') . "\033[0m" . PHP_EOL;
            echo "Run: \033[1;32martiframe auth\033[0m" . PHP_EOL;
            return;
        }

        $config = json_decode(file_get_contents($configPath), true);
        $token = $config['github_token'] ?? null;
        $username = $config['github_user'] ?? null;

        if (!$token || !$username) {
            echo "\033[1;31m[-] " . $this->translator->get('SUGGEST_NOT_AUTH') . "\033[0m" . PHP_EOL;
            echo "Run: \033[1;32martiframe auth\033[0m" . PHP_EOL;
            return;
        }

        $issueId = $args[0] ?? null;

        if ($issueId) {
            $this->showIssueDetails($token, $username, $issueId);
        } else {
            $this->listIssues($token, $username);
        }
    }

    private function listIssues(string $token, string $username): void
    {
        echo PHP_EOL . "\033[1;36mFetching issues for @{$username}...\033[0m" . PHP_EOL;

        $url = 'https://api.github.com/repos/' . self::REPO . '/issues?creator=' . urlencode($username) . '&state=all';
        $response = $this->githubApiRequest($url, $token);
        
        if (!$response) {
            echo "\033[1;31m[-] Failed to fetch issues from GitHub.\033[0m" . PHP_EOL;
            return;
        }

        $issues = json_decode($response, true);

        if (isset($issues['message'])) {
            echo "\033[1;31m[-] GitHub Error: " . $issues['message'] . "\033[0m" . PHP_EOL;
            return;
        }

        if (empty($issues)) {
            echo "\033[1;33mYou haven't opened any issues yet.\033[0m" . PHP_EOL;
            return;
        }

        echo "\033[1;37m" . str_pad("ID", 6) . str_pad("STATUS", 10) . str_pad("COMMENTS", 10) . "TITLE\033[0m" . PHP_EOL;
        echo str_repeat("-", 80) . PHP_EOL;

        foreach ($issues as $issue) {
            if (isset($issue['pull_request'])) {
                continue; // Skip PRs if they show up in issue search
            }
            
            $id = "#" . $issue['number'];
            $state = $issue['state']; // open or closed
            $comments = $issue['comments'];
            $title = strlen($issue['title']) > 50 ? substr($issue['title'], 0, 47) . '...' : $issue['title'];

            $stateColor = $state === 'open' ? "\033[1;32m" : "\033[1;31m"; // Green for open, red for closed
            $displayState = ucfirst($state);

            echo str_pad($id, 6) . 
                 $stateColor . str_pad($displayState, 10) . "\033[0m" . 
                 str_pad((string)$comments, 10) . 
                 $title . PHP_EOL;
        }
        
        echo PHP_EOL . "\033[38;5;240mTip: To view comments for a specific issue, run `artiframe issues <ID>`\033[0m" . PHP_EOL;
    }

    private function showIssueDetails(string $token, string $username, string $issueId): void
    {
        $issueId = ltrim($issueId, '#');
        echo PHP_EOL . "\033[1;36mFetching details for Issue #{$issueId}...\033[0m" . PHP_EOL;

        // Fetch Issue Details
        $url = 'https://api.github.com/repos/' . self::REPO . '/issues/' . $issueId;
        $response = $this->githubApiRequest($url, $token);
        
        if (!$response) {
            echo "\033[1;31m[-] Failed to fetch issue details.\033[0m" . PHP_EOL;
            return;
        }

        $issue = json_decode($response, true);

        if (isset($issue['message'])) {
            echo "\033[1;31m[-] GitHub Error: " . $issue['message'] . "\033[0m" . PHP_EOL;
            return;
        }

        $stateColor = $issue['state'] === 'open' ? "\033[1;32m" : "\033[1;31m";
        
        echo PHP_EOL;
        echo "\033[1;37m[" . $issue['state'] . "] " . $issue['title'] . "\033[0m" . PHP_EOL;
        echo "\033[38;5;240mOpened by @" . $issue['user']['login'] . " at " . date('Y-m-d H:i', strtotime($issue['created_at'])) . "\033[0m" . PHP_EOL;
        echo str_repeat("=", 80) . PHP_EOL;
        echo $this->wordWrapWithIndentation($issue['body']) . PHP_EOL;
        echo str_repeat("=", 80) . PHP_EOL;

        if ($issue['comments'] > 0) {
            echo PHP_EOL . "\033[1;36mComments ({$issue['comments']}):\033[0m" . PHP_EOL;
            
            $commentsUrl = $issue['comments_url'];
            $commentsResponse = $this->githubApiRequest($commentsUrl, $token);
            $comments = json_decode($commentsResponse, true);
            
            if (is_array($comments)) {
                foreach ($comments as $comment) {
                    echo PHP_EOL;
                    echo "\033[1;34m@" . $comment['user']['login'] . "\033[0m \033[38;5;240m(" . date('Y-m-d H:i', strtotime($comment['created_at'])) . "):\033[0m" . PHP_EOL;
                    echo str_repeat("-", 80) . PHP_EOL;
                    echo $this->wordWrapWithIndentation($comment['body']) . PHP_EOL;
                    echo str_repeat("-", 80) . PHP_EOL;
                }
            }
        } else {
            echo PHP_EOL . "\033[38;5;240mNo comments on this issue yet.\033[0m" . PHP_EOL;
        }
        echo PHP_EOL;
    }

    private function githubApiRequest(string $url, string $token): ?string
    {
        $ch = curl_init($url);
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

        return $response ?: null;
    }

    private function wordWrapWithIndentation(string $text, int $width = 80): string
    {
        $lines = explode("\n", $text);
        $wrapped = [];
        foreach ($lines as $line) {
            $wrapped[] = wordwrap(trim($line), $width, "\n");
        }
        return implode("\n", $wrapped);
    }
}
