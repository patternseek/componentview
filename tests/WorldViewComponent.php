<?php
namespace PatternSeek\ComponentView\Test;

use PatternSeek\ComponentView\AbstractViewComponent;

/**
 * Class WorldViewComponent
 * @package PatternSeek\ComponentView\Test
 */
class WorldViewComponent extends AbstractViewComponent{

    protected function doUpdate( array $inputs ){

        $this->testInputs(
            [
                'intRequired'=>[],
            ],
            $inputs
        );
        // Normally there would be processing here but for this test case the state is
        // just going to equal the inputs
        $this->state = $inputs;
        $templateInputs = $inputs;
        return $templateInputs;
    }

    /**
     * Load or configure the component's template as necessary
     *
     * @return void
     */
    protected function setupTemplate()
    {
        $tplPHP = "?>World. An int input: <?=\$tplInputs['intRequired']?>";
        $this->template = new EvilEvalTestTemplate( $this, $tplPHP );
    }
}