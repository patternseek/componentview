<?php
namespace PatternSeek\ComponentView\Test\Component;

use PatternSeek\ComponentView\AbstractViewComponent;
use PatternSeek\ComponentView\ExecHelper;
use PatternSeek\ComponentView\Response;
use PatternSeek\ComponentView\Template\PhpTemplate;
use PatternSeek\ComponentView\Template\TwigTemplate;
use PatternSeek\ComponentView\Test\ViewState\WorldState;

/**
 * Class WorldViewComponent
 * @package PatternSeek\ComponentView\Test
 */
class WorldViewComponentPhpTemplateString extends WorldViewComponent
{

    /**
     * Load or configure the component's template as necessary
     *
     * @return void
     */
    protected function initTemplate()
    {
        /** @var WorldState $state */
        /** @var ExecHelper $exec */
        $tplPhp = <<<EOS
World. From: <?=\$state->name?>

Exec URL: <?=\$exec->url( 'someExec', ['a'=>1] )?><?php \$formBody = "<input type=\"text\" name=\"someInput\" value=\"2\">\n"?>

Exec Form:
<?=\$exec->wrapForm( 'otherExec', 'POST', \$formBody ) ?>
EOS;
        $this->template = new PhpTemplate( $this, null, $tplPhp );
    }
}