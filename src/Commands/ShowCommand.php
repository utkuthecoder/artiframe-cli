<?php
namespace ArtiFrame\Cli\Commands;

use ArtiFrame\Cli\Services\Translator;

class ShowCommand
{
    private Translator $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function execute(array $args): void
    {
        echo "
  📍  " . $this->translator->get('SHOW_CURRENT_DIR') . "
";
        echo "      " . getcwd() . "

";
    }
}
