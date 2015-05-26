<?php
namespace PatternSeek\ComponentView\Test\Component;

use PatternSeek\ComponentView\AbstractViewComponent;
use PatternSeek\ComponentView\Template\TwigTemplateString;
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
    protected $state;

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
Hello {{component( 'world' )}}
An exec url {{ this.exec.url( "someExec", {'w1':'w1'} ) }}
EOS;

        $this->template = new TwigTemplateString( $this, $tplTwig );
    }

    /**
     * @param $initConfig
     * @throws \Exception
     */
    protected function initComponent( $initConfig )
    {
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
            $initConfig
        );
        $this->state->name = $initConfig[ 'name' ];
        $this->state->intRequired = $initConfig[ 'intRequired' ];
    }

    /**
     * Using $props and $this->state, optionally update state, optionally create child components via addOrUpdateChild()
     * @param $props
     * @return void
     */
    protected function doUpdateState( $props )
    {


        $this->addOrUpdateChild(
            "world", "\\PatternSeek\\ComponentView\\Test\\Component\\WorldViewComponent",
            [ ],
            [   // This is just passing specific parent inputs to the child
                'name' => $this->state->name,
                'intRequired' => $this->state->intRequired

            ]
        );
        // Normally there might be processing here and $this->state might be populated or modified, but not in this case

    }
}