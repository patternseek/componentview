<?php
namespace PatternSeek\ComponentView\Test\Component;

use PatternSeek\ComponentView\AbstractViewComponent;
use PatternSeek\ComponentView\Response;
use PatternSeek\ComponentView\Template\TwigTemplate;
use PatternSeek\ComponentView\Test\ViewState\WorldState;

/**
 * Class WorldViewComponent
 * @package PatternSeek\ComponentView\Test
 */
class WorldViewComponent extends AbstractViewComponent{

    /**
     * @var WorldState
     */
    protected $state;

    /**
     * Initialise $this->state with either a new ViewState or an appropriate subclass
     * @return void
     */
    protected function initState()
    {
        $this->state = new WorldState();
    }

    protected function updateState()
    {
        $props = $this->props;

        if (isset( $props[ 'doForceResponseWorld' ] )) {
            $this->getRootComponent()
                ->forceResponse( new Response( "redirect", 302 ) );
            return;
        }

        $this->testInputs(
            [
                'name' => [ 'string' ],
                'intRequired' => [ 'integer' ]
            ],
            $props
        );

        // Used by jsonMultiplyHandler()
        $this->state->intRequired = $props[ 'intRequired' ];
        $this->state->name = $props[ 'name' ];
    }

    /**
     * @param $args
     * @return Response
     * @throws \Exception
     */
    protected function jsonMultiplyHandler( $args ){
        $this->testInputs( ['multiplier'=>['int']], $args );
        $resInt = $this->state->intRequired * $args[ 'multiplier' ];
        return new Response( "application/json", json_encode( ['result'=>$resInt] ) );
    }

    /**
     * @param $args
     * @return Response
     * @throws \Exception
     */
    protected function setStateHandler( $args ){
        $this->testInputs( ['something'=>['int']], $args );
        $this->state->testProp = $args[ 'something' ];
        return new Response( "text/plain", "OK" );
    }

    /**
     * @param $args
     * @return Response
     */
    protected function getStateHandler( $args ){
        return new Response( "text/plain", $this->state->testProp );
    }

    /**
     * Load or configure the component's template as necessary
     *
     * @return void
     */
    protected function initTemplate()
    {
        $tplTwig = <<<EOS
World. From: {{state.name}}
Exec URL: {{exec.url( 'someExec', {'a':1} )}}
{% set formBody %}
<input type="text" name="someInput" value="2">
{% endset %}
Exec Form:
{{ exec.wrapForm( 'otherExec', 'POST', formBody ) }}
EOS;
        $this->template = new TwigTemplate( $this, null, $tplTwig );
    }
}