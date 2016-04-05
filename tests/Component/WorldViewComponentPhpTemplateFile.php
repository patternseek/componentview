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
class WorldViewComponentPhpTemplateFile extends WorldViewComponent
{

    /**
     * Load or configure the component's template as necessary
     *
     * @return void
     */
    protected function initTemplate()
    {
        $this->template = new PhpTemplate( $this, __DIR__ . '/../fixtures/WorldTemplate.php', null );
    }
}