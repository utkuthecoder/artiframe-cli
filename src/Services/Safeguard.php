<?php
namespace ArtiFrame\Cli\Services;

class Safeguard
{
    private Translator $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Checks if the target path is inside the protected /bin/ directory.
     * If so, interrupts and asks for user confirmation.
     * Returns true if safe to proceed, false if aborted.
     */
    public function checkTarget(string $targetPath): bool
    {
        // Normalize slashes
        $normalizedPath = str_replace('\\', '/', $targetPath);
        
        // Check if the path targets /bin/
        if (strpos($normalizedPath, '/bin/') !== false || strpos($normalizedPath, 'bin/') === 0) {
            echo PHP_EOL . "⚠️  " . $this->translator->get('CORE_WARNING_TITLE') . PHP_EOL;
            echo str_repeat("-", 60) . PHP_EOL;
            echo $this->translator->get('CORE_WARNING_BODY') . PHP_EOL;
            echo str_repeat("-", 60) . PHP_EOL;
            
            $prompt = $this->translator->get('CONFIRM_PROMPT');
            echo $prompt;
            
            $handle = fopen("php://stdin", "r");
            $line = fgets($handle);
            fclose($handle);
            
            $response = strtolower(trim($line));
            $approvedResponses = ['y', 'yes', 'j', 'o', 's', 'evet'];
            
            if (!in_array($response, $approvedResponses)) {
                echo PHP_EOL . "[ABORT] Operation cancelled by user." . PHP_EOL;
                return false;
            }
        }
        
        return true;
    }
}
