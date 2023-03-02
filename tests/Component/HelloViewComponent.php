<?php
namespace PatternSeek\ComponentView\Test\Component;

use PatternSeek\ComponentView\AbstractViewComponent;
use PatternSeek\ComponentView\Response;
use PatternSeek\ComponentView\Template\TwigTemplate;
use PatternSeek\ComponentView\Test\SomeClass;
use PatternSeek\ComponentView\Test\ViewState\HelloState;

/**
 * Class HelloViewComponent
 * @package PatternSeek\ComponentView\Test
 */
class HelloViewComponent extends AbstractViewComponent{

    /**
     * @var HelloState
     */
    protected \PatternSeek\ComponentView\ViewState\ViewState $state;

    /**
     * Initialise $this->state with either a new ViewState or an appropriate subclass
     * @return void
     */
    protected function initState()
    {
        $this->state = new HelloState();
    }

    /**
     * Load or configure the component's template as necessary
     *
     * @return void
     */
    protected function initTemplate()
    {
        $tplTwig = <<<EOS
Hello {{ this.renderChild( 'world' ) }}
An exec url {{ exec.url( "someExec", {'w1':'w1'} ) }}
EOS;

        $this->template = new TwigTemplate( $this, null, $tplTwig );
    }

    protected function updateState()
    {
        $props = $this->props;

        if (isset($props[ 'doForceResponse' ]) && $props[ 'doForceResponse' ] == true) {
            $this->getRootComponent()
                ->forceResponse( new Response( "redirect", 301 ) );
            return;
        }

        $this->testInputs(
            [
                'anyTypeRequired' => [ ],
                'anyTypeRequired2' => [ null ],
                'anyTypeOptional' => [ null, null ],
                'boolRequired' => [ 'bool' ],
                'boolRequired2' => [ 'boolean' ],
                'intOptional' => [ 'int', 3 ],
                'intRequired' => [ 'integer' ],
                'doubleRequired' => [ 'double' ],
                'floatRequired' => [ 'float' ],
                'stringRequired' => [ 'string' ],
                'name' => [ 'string' ],
                'arrayRequired' => [ 'array' ],
                'objectRequired' => [ 'object' ],
                'resourceRequired' => [ 'resource' ],
                'callableRequired' => [ 'callable' ],
                'SomeClassRequired' => [ 'PatternSeek\ComponentView\Test\SomeClass' ],
                'SomeClassOptional' => [ 'PatternSeek\ComponentView\Test\SomeClass', null ],
                'SomeClassWithPrebuiltDefault' => [ 'PatternSeek\ComponentView\Test\SomeClass', new SomeClass() ],
            ],
            $props
        );
        $this->state->name = $props[ 'name' ];
        $this->state->intRequired = $props[ 'intRequired' ];
        
        // Normally there might be processing here and $this->state might be populated or modified, but not in this case

        $childComponent = isset( $props[ 'alternateChildComponent' ] )?$props[ 'alternateChildComponent' ]:"\\PatternSeek\\ComponentView\\Test\\Component\\WorldViewComponent";
        
        $this->addOrUpdateChild(
            'world',
            $childComponent,
            [
                'name' => $this->state->name,
                'intRequired' => $this->state->intRequired,
                'doForceResponseWorld' => $props[ 'doForceResponseWorld' ]??null
            ]
        );
        
    }
}

