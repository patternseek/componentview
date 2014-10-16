<?php
namespace PatternSeek\ComponentView\Test;

use PatternSeek\ComponentView\AbstractViewComponent;

/**
 * Class HelloViewComponent
 * @package PatternSeek\ComponentView\Test
 */
class HelloViewComponent extends AbstractViewComponent{

    protected function doUpdate( array $inputs ){
        $this->testInputs(
            [
                'anyTypeRequired'=>[],
                'anyTypeRequired2'=>[null],
                'anyTypeOptional'=>[null,null],
                'boolRequired'=>['bool'],
                'boolRequired2'=>['boolean'],
                'intOptional'=>['int',3],
                'intRequired'=>['integer'],
                'doubleRequired'=>['double'],
                'floatRequired'=>['float'],
                'stringRequired'=>['string'],
                'arrayRequired'=>['array'],
                'objectRequired'=>['object'],
                'resourceRequired'=>['resource'],
                'callableRequired'=>['callable'],
                'SomeClassRequired'=>['PatternSeek\ComponentView\Test\SomeClass'],
                'SomeClassOptional'=>['PatternSeek\ComponentView\Test\SomeClass',null],
                'SomeClassWithPrebuiltDefault'=>['PatternSeek\ComponentView\Test\SomeClass', new SomeClass()],
            ],
            $inputs
        );

        $this->addOrUpdateChild( "world", "\\PatternSeek\\ComponentView\\Test\\WorldViewComponent", ['intRequired'=>1] );

        // Normally there would be processing here but for this test case the state is
        // just going to equal the inputs
        $this->state = $inputs;
        $templateInputs = $inputs; // normally this would be generated
        return $templateInputs;

    }

    /**
     * Load or configure the component's template as necessary
     *
     * @return void
     */
    protected function setupTemplate()
    {
        $tplPHP = '$worldOut = $components["world"]->render( $inputs );
                   echo "Hello ";
                   echo $worldOut->content;';
        $this->template = new EvilEvalTestTemplate( $this, $tplPHP );
    }
}