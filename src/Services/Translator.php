<?php
namespace ArtiFrame\Cli\Services;

class Translator
{
    private string $locale;
    private array $messages = [];

    public function __construct(string $locale)
    {
        // Support tr, en, de, es, fr. Default to en if not found.
        $supportedLocales = ['tr', 'en', 'de', 'es', 'fr'];
        
        if (!in_array($locale, $supportedLocales)) {
            $locale = 'en';
        }
        
        $this->locale = $locale;
        $this->loadMessages();
    }

    private function loadMessages(): void
    {
        $langFile = ARTIFRAME_CLI_ROOT . '/src/Lang/' . $this->locale . '.php';
        
        if (file_exists($langFile)) {
            $this->messages = require $langFile;
        } else {
            // Fallback to english
            $fallbackFile = ARTIFRAME_CLI_ROOT . '/src/Lang/en.php';
            if (file_exists($fallbackFile)) {
                $this->messages = require $fallbackFile;
            }
        }
    }

    public function get(string $key, array $replacements = []): string
    {
        $message = $this->messages[$key] ?? $key;

        foreach ($replacements as $placeholder => $value) {
            $message = str_replace(':' . $placeholder, $value, $message);
        }

        return $message;
    }

    public function getLang(): string
    {
        return $this->locale;
    }
}
