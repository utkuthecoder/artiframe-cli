<?php
namespace ArtiFrame\Cli\Commands;

use ArtiFrame\Cli\Services\Translator;

class GoCommand
{
    private Translator $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function execute(array $args): void
    {
        $target = $args[0] ?? null;

        if (!$target) {
            echo "
  ❌  " . $this->translator->get('GO_MISSING_DIR') . "

";
            return;
        }

        if ($target === 'back') {
            $target = '..';
        }

        if (!is_dir($target)) {
            $notFound = str_replace(':dir', $target, $this->translator->get('GO_NOT_FOUND'));
            echo "
  ❌  " . $notFound . "

";
            return;
        }

        chdir($target);
        echo "
  ✅  " . $this->translator->get('GO_SUCCESS') . "
";
        echo "      " . getcwd() . "

";
    }
}
