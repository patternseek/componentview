<?php
/*
 * This file is part of the Patternseek ComponentView library.
 *
 * (c)Tolan Blundell <tolan@patternseek.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PatternSeek\ComponentView\Test;

use PatternSeek\ComponentView\AbstractViewComponent;
use PatternSeek\ComponentView\ExecHelper;
use PatternSeek\ComponentView\Response;
use PatternSeek\ComponentView\Test\Component\HelloViewComponent;
use PatternSeek\DependencyInjector\DependencyInjector;
use Pimple\Container;
use PHPUnit\Framework\TestCase;


/**
 * Class SomeClass
 * @package PatternSeek\ComponentView\Test
 */
class SomeClass{}

/**
 * Class AbstractViewComponentTest
 * @package PatternSeek\ComponentView\Test
 */
class AbstractViewComponentTest extends TestCase {

    protected function setUp() : void
    {
        DependencyInjector::init( new Container() );
    }

    function testForceResponse()
    {

        // Optional off
        $props =
            [
                'doForceResponse' => true,
            ];
        $log = new MemoryLogger();
        $view = new HelloViewComponent( null, null, null, $log );
        $view->updateView( $props );
        $outObj = $view->render();

        $expected = new Response( "redirect", 301 );

        $this->assertEquals( $expected, $outObj );
    }

    function testForceResponseInChild()
    {

        $props =
            [
                'doForceResponseWorld' => true,

                'anyTypeRequired' => 1,
                'anyTypeRequired2' => 2,
                'boolRequired' => true,
                'boolRequired2' => false,
                'intRequired' => 4,
                'doubleRequired' => 1.1,
                'floatRequired' => 1.2,
                'stringRequired' => 'string',
                'name' => 'someone',
                'arrayRequired' => [ ],
                'objectRequired' => new SomeClass(),
                'resourceRequired' => fopen( "/tmp", 'r' ),
                'callableRequired' => function (){
                },
                'SomeClassRequired' => new SomeClass()
            ];
        $log = new MemoryLogger();
        $view = new HelloViewComponent( null, null, null, $log );
        $view->updateView( $props );
        $outObj = $view->render();

        $expected = new Response( "redirect", 302 );

        $this->assertEquals( $expected, $outObj );
    }
    
    function testRender(){
        
        // Optional off
        $props =
            [
                'anyTypeRequired'=>1,
                'anyTypeRequired2'=>2,
                #'anyTypeOptional'=>,
                'boolRequired'=>true,
                'boolRequired2'=>false,
                #'intOptional'=>3,
                'intRequired'=>4,
                'doubleRequired'=>1.1,
                'floatRequired'=>1.2,
                'stringRequired'=>'string',
                'name'=>'someone',
                'arrayRequired'=>[],
                'objectRequired'=>new SomeClass(),
                'resourceRequired'=>fopen( "/tmp", 'r' ),
                'callableRequired'=>function(){},
                'SomeClassRequired'=>new SomeClass()
                #'SomeClassOptional'=>,
                #'SomeClassWithPrebuiltDefault'=>,
            ];
        $log = new MemoryLogger();
        $view = new HelloViewComponent( null, null, null, $log );
        $view->updateView( $props );
        $view->execFormHelper = function (){
        };
        $outObj = $view->render();

        $expected = <<<EOS
Hello World. From: someone
Exec URL: ?a=1&exec=world.someExec
Exec Form:
<form method="POST" action="" enctype="application/x-www-form-urlencoded">
    <input type="hidden" name="exec" value="world.otherExec">
    <input type="text" name="someInput" value="2">

</form>
An exec url ?w1=w1&exec=someExec
EOS;
        file_put_contents( "/tmp/out", $outObj->content );
        $this->assertEquals( $expected, $outObj->content );
    }

    function testPhpTemplateString()
    {

        // Optional off
        $props =
            [
                'anyTypeRequired' => 1,
                'anyTypeRequired2' => 2,
                'boolRequired' => true,
                'boolRequired2' => false,
                'intRequired' => 4,
                'doubleRequired' => 1.1,
                'floatRequired' => 1.2,
                'stringRequired' => 'string',
                'name' => 'someone',
                'arrayRequired' => [ ],
                'objectRequired' => new SomeClass(),
                'resourceRequired' => fopen( "/tmp", 'r' ),
                'callableRequired' => function (){
                },
                'SomeClassRequired' => new SomeClass(),

                'alternateChildComponent' => "\\PatternSeek\\ComponentView\\Test\\Component\\WorldViewComponentPhpTemplateString"
                // Load alternate child component
            ];
        $log = new MemoryLogger();
        $view = new HelloViewComponent( null, null, null, $log );
        $view->updateView( $props );
        $view->execFormHelper = function (){
        };
        $outObj = $view->render();
        $expected = <<<EOS
Hello World. From: someone
Exec URL: ?a=1&exec=world.someExec
Exec Form:
<form method="POST" action="" enctype="application/x-www-form-urlencoded">
    <input type="hidden" name="exec" value="world.otherExec">
    <input type="text" name="someInput" value="2">

</form>
An exec url ?w1=w1&exec=someExec
EOS;
        file_put_contents( "/tmp/out", $outObj->content );
        $this->assertEquals( $expected, $outObj->content );
    }

    function testPhpTemplateFile()
    {

        // Optional off
        $props =
            [
                'anyTypeRequired' => 1,
                'anyTypeRequired2' => 2,
                'boolRequired' => true,
                'boolRequired2' => false,
                'intRequired' => 4,
                'doubleRequired' => 1.1,
                'floatRequired' => 1.2,
                'stringRequired' => 'string',
                'name' => 'someone',
                'arrayRequired' => [ ],
                'objectRequired' => new SomeClass(),
                'resourceRequired' => fopen( "/tmp", 'r' ),
                'callableRequired' => function (){
                },
                'SomeClassRequired' => new SomeClass(),

                'alternateChildComponent' => "\\PatternSeek\\ComponentView\\Test\\Component\\WorldViewComponentPhpTemplateFile"
                // Load alternate child component
            ];
        $log = new MemoryLogger();
        $view = new HelloViewComponent( null, null, null, $log );
        $view->updateView( $props );
        $view->execFormHelper = function (){
        };
        $outObj = $view->render();
        $expected = <<<EOS
Hello World. From: someone
Exec URL: ?a=1&exec=world.someExec
Exec Form:
<form method="POST" action="" enctype="application/x-www-form-urlencoded">
    <input type="hidden" name="exec" value="world.otherExec">
    <input type="text" name="someInput" value="2">

</form>
An exec url ?w1=w1&exec=someExec
EOS;
        file_put_contents( "/tmp/out", $outObj->content );
        $this->assertEquals( $expected, $outObj->content );
    }

    function testNestedExecJSON(){

        // Optional off
        $props =
            [
                'anyTypeRequired'=>1,
                'anyTypeRequired2'=>2,
                #'anyTypeOptional'=>,
                'boolRequired'=>true,
                'boolRequired2'=>false,
                #'intOptional'=>3,
                'intRequired'=>4,
                'doubleRequired'=>1.1,
                'floatRequired'=>1.2,
                'stringRequired'=>'string',
                'name'=>'someone',
                'arrayRequired'=>[],
                'objectRequired'=>new SomeClass(),
                'resourceRequired'=>fopen( "/tmp", 'r' ),
                'callableRequired'=>function(){},
                'SomeClassRequired'=>new SomeClass()
                #'SomeClassOptional'=>,
                #'SomeClassWithPrebuiltDefault'=>,
            ];
        $log = new MemoryLogger();
        $view = new HelloViewComponent( null, null, null, $log );
        $view->updateView( $props );
        $outObj = $view->render( "world.jsonMultiply", ['multiplier'=>3] ); // Multiplies multiplier * props['intRequired']

        //print_r( $log->messages );
        
        $expected = json_encode( ['result'=>12 ] );
        $this->assertEquals( $expected, $outObj->content );
    }

    function testNestedSetAndGetStateExec(){
        // Optional off
        $props =
            [
                'anyTypeRequired'=>1,
                'anyTypeRequired2'=>2,
                #'anyTypeOptional'=>,
                'boolRequired'=>true,
                'boolRequired2'=>false,
                #'intOptional'=>3,
                'intRequired'=>4,
                'doubleRequired'=>1.1,
                'floatRequired'=>1.2,
                'stringRequired'=>'string',
                'name'=>'someone',
                'arrayRequired'=>[],
                'objectRequired'=>new SomeClass(),
                'resourceRequired'=>fopen( "/tmp", 'r' ),
                'callableRequired'=>function(){},
                'SomeClassRequired'=>new SomeClass()
                #'SomeClassOptional'=>,
                #'SomeClassWithPrebuiltDefault'=>,
            ];
        $execHelper = new ExecHelper();
        $log = new MemoryLogger();
        $view = new HelloViewComponent( null, null, $execHelper, $log );
        $view->updateView( $props );
        $outObj = $view->render( "world.setState", ['something'=>5] ); // Multiplies multiplier * props['intRequired']
        $this->assertEquals( "OK", $outObj->content );
        $viewSer = $view->dehydrate();
        // Next page
        $view = AbstractViewComponent::rehydrate( $viewSer, $execHelper, null, $log );
        $view->updateView( $props );
        $outObj = $view->render( "world.getState" ); // Multiplies multiplier * props['intRequired']

        //print_r( $log->messages );
        
        $this->assertEquals( 5, $outObj->content );
    }

    function testInputCheckerInUpdate(){

        // Optional off
        $props =
            [
                'anyTypeRequired'=>1,
                'anyTypeRequired2'=>2,
                #'anyTypeOptional'=>,
                'boolRequired'=>true,
                'boolRequired2'=>false,
                #'intOptional'=>3,
                'intRequired'=>4,
                'doubleRequired'=>1.1,
                'floatRequired'=>1.2,
                'stringRequired'=>'string',
                'name'=>'someone',
                'arrayRequired'=>[],
                'objectRequired'=>new SomeClass(),
                'resourceRequired'=>fopen( "/tmp", 'r' ),
                'callableRequired'=>function(){},
                'SomeClassRequired'=>new SomeClass()
                #'SomeClassOptional'=>,
                #'SomeClassWithPrebuiltDefault'=>,
            ];
        $log = new MemoryLogger();
        $view = new HelloViewComponent( null, null, null, $log );
        $view->updateView( $props );
        
        // Optional on
        $props =
            [
                'anyTypeRequired'=>1,
                'anyTypeRequired2'=>2,
                'anyTypeOptional'=>"hello",
                'boolRequired'=>true,
                'boolRequired2'=>false,
                'intOptional'=>3,
                'intRequired'=>4,
                'doubleRequired'=>1.1,
                'floatRequired'=>1.2,
                'stringRequired'=>'string',
                'name'=>'someone',
                'arrayRequired'=>[],
                'objectRequired'=>new SomeClass(),
                'resourceRequired'=>fopen( "/tmp", 'r' ),
                'callableRequired'=>function(){},
                'SomeClassRequired'=>new SomeClass(),
                'SomeClassOptional'=>new SomeClass(),
                'SomeClassWithPrebuiltDefault'=>new SomeClass(),
            ];
        $view = new HelloViewComponent( null, null, null, $log );
        $view->updateView( $props );

        // Failures

        $this->failConfig(
             [
                 #'anyTypeRequired'=>1, //
                 'anyTypeRequired2'=>2,
                 'anyTypeOptional'=>"hello",
                 'boolRequired'=>true,
                 'boolRequired2'=>false,
                 'intOptional'=>3,
                 'intRequired'=>4,
                 'doubleRequired'=>1.1,
                 'floatRequired'=>1.2,
                 'stringRequired'=>'string',
                 'name'=>'someone',
                 'arrayRequired'=>[],
                 'objectRequired'=>new SomeClass(),
                 'resourceRequired'=>fopen( "/tmp", 'r' ),
                 'callableRequired'=>function(){},
                 'SomeClassRequired'=>new SomeClass(),
                 'SomeClassOptional'=>new SomeClass(),
                 'SomeClassWithPrebuiltDefault'=>new SomeClass(),
             ]
        );

        $this->failConfig(
             [
                 'anyTypeRequired'=>1,
                 #'anyTypeRequired2'=>2, //
                 'anyTypeOptional'=>"hello",
                 'boolRequired'=>true,
                 'boolRequired2'=>false,
                 'intOptional'=>3,
                 'intRequired'=>4,
                 'doubleRequired'=>1.1,
                 'floatRequired'=>1.2,
                 'stringRequired'=>'string',
                 'name'=>'someone',
                 'arrayRequired'=>[],
                 'objectRequired'=>new SomeClass(),
                 'resourceRequired'=>fopen( "/tmp", 'r' ),
                 'callableRequired'=>function(){},
                 'SomeClassRequired'=>new SomeClass(),
                 'SomeClassOptional'=>new SomeClass(),
                 'SomeClassWithPrebuiltDefault'=>new SomeClass(),
             ]
        );

        $this->failConfig(
             [
                 'anyTypeRequired'=>1,
                 'anyTypeRequired2'=>2,
                 'anyTypeOptional'=>"hello",
                 'boolRequired'=>"hello", //
                 'boolRequired2'=>false,
                 'intOptional'=>3,
                 'intRequired'=>4,
                 'doubleRequired'=>1.1,
                 'floatRequired'=>1.2,
                 'stringRequired'=>'string',
                 'name'=>'someone',
                 'arrayRequired'=>[],
                 'objectRequired'=>new SomeClass(),
                 'resourceRequired'=>fopen( "/tmp", 'r' ),
                 'callableRequired'=>function(){},
                 'SomeClassRequired'=>new SomeClass(),
                 'SomeClassOptional'=>new SomeClass(),
                 'SomeClassWithPrebuiltDefault'=>new SomeClass(),
             ]
        );

        $this->failConfig(
             [
                 'anyTypeRequired'=>1,
                 'anyTypeRequired2'=>2,
                 'anyTypeOptional'=>"hello",
                 #'boolRequired'=>true, //
                 'boolRequired2'=>false,
                 'intOptional'=>3,
                 'intRequired'=>4,
                 'doubleRequired'=>1.1,
                 'floatRequired'=>1.2,
                 'stringRequired'=>'string',
                 'name'=>'someone',
                 'arrayRequired'=>[],
                 'objectRequired'=>new SomeClass(),
                 'resourceRequired'=>fopen( "/tmp", 'r' ),
                 'callableRequired'=>function(){},
                 'SomeClassRequired'=>new SomeClass(),
                 'SomeClassOptional'=>new SomeClass(),
                 'SomeClassWithPrebuiltDefault'=>new SomeClass(),
             ]
        );

        $this->failConfig(
             [
                 'anyTypeRequired'=>1,
                 'anyTypeRequired2'=>2,
                 'anyTypeOptional'=>"hello",
                 'boolRequired'=>true,
                 #'boolRequired2'=>false, //
                 'intOptional'=>3,
                 'intRequired'=>4,
                 'doubleRequired'=>1.1,
                 'floatRequired'=>1.2,
                 'stringRequired'=>'string',
                 'name'=>'someone',
                 'arrayRequired'=>[],
                 'objectRequired'=>new SomeClass(),
                 'resourceRequired'=>fopen( "/tmp", 'r' ),
                 'callableRequired'=>function(){},
                 'SomeClassRequired'=>new SomeClass(),
                 'SomeClassOptional'=>new SomeClass(),
                 'SomeClassWithPrebuiltDefault'=>new SomeClass(),
             ]
        );


        $this->failConfig(
             [
                 'anyTypeRequired'=>1,
                 'anyTypeRequired2'=>2,
                 'anyTypeOptional'=>"hello",
                 'boolRequired'=>true,
                 'boolRequired2'=>false,
                 'intOptional'=>3,
                 #'intRequired'=>4, //
                 'doubleRequired'=>1.1,
                 'floatRequired'=>1.2,
                 'stringRequired'=>'string',
                 'name'=>'someone',
                 'arrayRequired'=>[],
                 'objectRequired'=>new SomeClass(),
                 'resourceRequired'=>fopen( "/tmp", 'r' ),
                 'callableRequired'=>function(){},
                 'SomeClassRequired'=>new SomeClass(),
                 'SomeClassOptional'=>new SomeClass(),
                 'SomeClassWithPrebuiltDefault'=>new SomeClass(),
             ]
        );

        $this->failConfig(
             [
                 'anyTypeRequired'=>1,
                 'anyTypeRequired2'=>2,
                 'anyTypeOptional'=>"hello",
                 'boolRequired'=>true,
                 'boolRequired2'=>false,
                 'intOptional'=>3,
                 'intRequired'=>new SomeClass(), //
                 'doubleRequired'=>1.1,
                 'floatRequired'=>1.2,
                 'stringRequired'=>'string',
                 'name'=>'someone',
                 'arrayRequired'=>[],
                 'objectRequired'=>new SomeClass(),
                 'resourceRequired'=>fopen( "/tmp", 'r' ),
                 'callableRequired'=>function(){},
                 'SomeClassRequired'=>new SomeClass(),
                 'SomeClassOptional'=>new SomeClass(),
                 'SomeClassWithPrebuiltDefault'=>new SomeClass(),
             ]
        );

        $this->failConfig(
             [
                 'anyTypeRequired'=>1,
                 'anyTypeRequired2'=>2,
                 'anyTypeOptional'=>"hello",
                 'boolRequired'=>true,
                 'boolRequired2'=>false,
                 'intOptional'=>3,
                 'intRequired'=>4,
                 #'doubleRequired'=>1.1, //
                 'floatRequired'=>1.2,
                 'stringRequired'=>'string',
                 'name'=>'someone',
                 'arrayRequired'=>[],
                 'objectRequired'=>new SomeClass(),
                 'resourceRequired'=>fopen( "/tmp", 'r' ),
                 'callableRequired'=>function(){},
                 'SomeClassRequired'=>new SomeClass(),
                 'SomeClassOptional'=>new SomeClass(),
                 'SomeClassWithPrebuiltDefault'=>new SomeClass(),
             ]
        );

        $this->failConfig(
             [
                 'anyTypeRequired'=>1,
                 'anyTypeRequired2'=>2,
                 'anyTypeOptional'=>"hello",
                 'boolRequired'=>true,
                 'boolRequired2'=>false,
                 'intOptional'=>3,
                 'intRequired'=>4,
                 'doubleRequired'=>1, //
                 'floatRequired'=>1.2,
                 'stringRequired'=>'string',
                 'name'=>'someone',
                 'arrayRequired'=>[],
                 'objectRequired'=>new SomeClass(),
                 'resourceRequired'=>fopen( "/tmp", 'r' ),
                 'callableRequired'=>function(){},
                 'SomeClassRequired'=>new SomeClass(),
                 'SomeClassOptional'=>new SomeClass(),
                 'SomeClassWithPrebuiltDefault'=>new SomeClass(),
             ]
        );

        $this->failConfig(
             [
                 'anyTypeRequired'=>1,
                 'anyTypeRequired2'=>2,
                 'anyTypeOptional'=>"hello",
                 'boolRequired'=>true,
                 'boolRequired2'=>false,
                 'intOptional'=>3,
                 'intRequired'=>4,
                 'doubleRequired'=>1.1,
                 #'floatRequired'=>1.2, //
                 'stringRequired'=>'string',
                 'name'=>'someone',
                 'arrayRequired'=>[],
                 'objectRequired'=>new SomeClass(),
                 'resourceRequired'=>fopen( "/tmp", 'r' ),
                 'callableRequired'=>function(){},
                 'SomeClassRequired'=>new SomeClass(),
                 'SomeClassOptional'=>new SomeClass(),
                 'SomeClassWithPrebuiltDefault'=>new SomeClass(),
             ]
        );

        $this->failConfig(
             [
                 'anyTypeRequired'=>1,
                 'anyTypeRequired2'=>2,
                 'anyTypeOptional'=>"hello",
                 'boolRequired'=>true,
                 'boolRequired2'=>false,
                 'intOptional'=>3,
                 'intRequired'=>4,
                 'doubleRequired'=>1.1,
                 'floatRequired'=>"hi", //
                 'stringRequired'=>'string',
                 'name'=>'someone',
                 'arrayRequired'=>[],
                 'objectRequired'=>new SomeClass(),
                 'resourceRequired'=>fopen( "/tmp", 'r' ),
                 'callableRequired'=>function(){},
                 'SomeClassRequired'=>new SomeClass(),
                 'SomeClassOptional'=>new SomeClass(),
                 'SomeClassWithPrebuiltDefault'=>new SomeClass(),
             ]
        );

        $this->failConfig(
             [
                 'anyTypeRequired'=>1,
                 'anyTypeRequired2'=>2,
                 'anyTypeOptional'=>"hello",
                 'boolRequired'=>true,
                 'boolRequired2'=>false,
                 'intOptional'=>3,
                 'intRequired'=>4,
                 'doubleRequired'=>1.1,
                 'floatRequired'=>1.2,
                 #'stringRequired'=>'string', //
                 'name'=>'someone',
                 'arrayRequired'=>[],
                 'objectRequired'=>new SomeClass(),
                 'resourceRequired'=>fopen( "/tmp", 'r' ),
                 'callableRequired'=>function(){},
                 'SomeClassRequired'=>new SomeClass(),
                 'SomeClassOptional'=>new SomeClass(),
                 'SomeClassWithPrebuiltDefault'=>new SomeClass(),
             ]
        );

        $this->failConfig(
             [
                 'anyTypeRequired'=>1,
                 'anyTypeRequired2'=>2,
                 'anyTypeOptional'=>"hello",
                 'boolRequired'=>true,
                 'boolRequired2'=>false,
                 'intOptional'=>3,
                 'intRequired'=>4,
                 'doubleRequired'=>1.1,
                 'floatRequired'=>1.2,
                 'stringRequired'=>1, //
                 'name'=>'someone',
                 'arrayRequired'=>[],
                 'objectRequired'=>new SomeClass(),
                 'resourceRequired'=>fopen( "/tmp", 'r' ),
                 'callableRequired'=>function(){},
                 'SomeClassRequired'=>new SomeClass(),
                 'SomeClassOptional'=>new SomeClass(),
                 'SomeClassWithPrebuiltDefault'=>new SomeClass(),
             ]
        );

        $this->failConfig(
             [
                 'anyTypeRequired'=>1,
                 'anyTypeRequired2'=>2,
                 'anyTypeOptional'=>"hello",
                 'boolRequired'=>true,
                 'boolRequired2'=>false,
                 'intOptional'=>3,
                 'intRequired'=>4,
                 'doubleRequired'=>1.1,
                 'floatRequired'=>1.2,
                 'stringRequired'=>'string',
                 'name'=>'someone',
                 #'arrayRequired'=>[], //
                 'objectRequired'=>new SomeClass(),
                 'resourceRequired'=>fopen( "/tmp", 'r' ),
                 'callableRequired'=>function(){},
                 'SomeClassRequired'=>new SomeClass(),
                 'SomeClassOptional'=>new SomeClass(),
                 'SomeClassWithPrebuiltDefault'=>new SomeClass(),
             ]
        );

        $this->failConfig(
             [
                 'anyTypeRequired'=>1,
                 'anyTypeRequired2'=>2,
                 'anyTypeOptional'=>"hello",
                 'boolRequired'=>true,
                 'boolRequired2'=>false,
                 'intOptional'=>3,
                 'intRequired'=>4,
                 'doubleRequired'=>1.1,
                 'floatRequired'=>1.2,
                 'stringRequired'=>'string',
                 'name'=>'someone',
                 'arrayRequired'=>"1", //
                 'objectRequired'=>new SomeClass(),
                 'resourceRequired'=>fopen( "/tmp", 'r' ),
                 'callableRequired'=>function(){},
                 'SomeClassRequired'=>new SomeClass(),
                 'SomeClassOptional'=>new SomeClass(),
                 'SomeClassWithPrebuiltDefault'=>new SomeClass(),
             ]
        );

        $this->failConfig(
             [
                 'anyTypeRequired'=>1,
                 'anyTypeRequired2'=>2,
                 'anyTypeOptional'=>"hello",
                 'boolRequired'=>true,
                 'boolRequired2'=>false,
                 'intOptional'=>3,
                 'intRequired'=>4,
                 'doubleRequired'=>1.1,
                 'floatRequired'=>1.2,
                 'stringRequired'=>'string',
                 'name'=>'someone',
                 'arrayRequired'=>[],
                 #'objectRequired'=>new SomeClass(), //
                 'resourceRequired'=>fopen( "/tmp", 'r' ),
                 'callableRequired'=>function(){},
                 'SomeClassRequired'=>new SomeClass(),
                 'SomeClassOptional'=>new SomeClass(),
                 'SomeClassWithPrebuiltDefault'=>new SomeClass(),
             ]
        );

        $this->failConfig(
             [
                 'anyTypeRequired'=>1,
                 'anyTypeRequired2'=>2,
                 'anyTypeOptional'=>"hello",
                 'boolRequired'=>true,
                 'boolRequired2'=>false,
                 'intOptional'=>3,
                 'intRequired'=>4,
                 'doubleRequired'=>1.1,
                 'floatRequired'=>1.2,
                 'stringRequired'=>'string',
                 'name'=>'someone',
                 'arrayRequired'=>[],
                 'objectRequired'=>FALSE, //
                 'resourceRequired'=>fopen( "/tmp", 'r' ),
                 'callableRequired'=>function(){},
                 'SomeClassRequired'=>new SomeClass(),
                 'SomeClassOptional'=>new SomeClass(),
                 'SomeClassWithPrebuiltDefault'=>new SomeClass(),
             ]
        );

        $this->failConfig(
             [
                 'anyTypeRequired'=>1,
                 'anyTypeRequired2'=>2,
                 'anyTypeOptional'=>"hello",
                 'boolRequired'=>true,
                 'boolRequired2'=>false,
                 'intOptional'=>3,
                 'intRequired'=>4,
                 'doubleRequired'=>1.1,
                 'floatRequired'=>1.2,
                 'stringRequired'=>'string',
                 'name'=>'someone',
                 'arrayRequired'=>[],
                 'objectRequired'=>new SomeClass(),
                 #'resourceRequired'=>fopen( "/tmp", 'r' ), //
                 'callableRequired'=>function(){},
                 'SomeClassRequired'=>new SomeClass(),
                 'SomeClassOptional'=>new SomeClass(),
                 'SomeClassWithPrebuiltDefault'=>new SomeClass(),
             ]
        );

        $this->failConfig(
             [
                 'anyTypeRequired'=>1,
                 'anyTypeRequired2'=>2,
                 'anyTypeOptional'=>"hello",
                 'boolRequired'=>true,
                 'boolRequired2'=>false,
                 'intOptional'=>3,
                 'intRequired'=>4,
                 'doubleRequired'=>1.1,
                 'floatRequired'=>1.2,
                 'stringRequired'=>'string',
                 'name'=>'someone',
                 'arrayRequired'=>[],
                 'objectRequired'=>new SomeClass(),
                 'resourceRequired'=>"hi", //
                 'callableRequired'=>function(){},
                 'SomeClassRequired'=>new SomeClass(),
                 'SomeClassOptional'=>new SomeClass(),
                 'SomeClassWithPrebuiltDefault'=>new SomeClass(),
             ]
        );

        $this->failConfig(
             [
                 'anyTypeRequired'=>1,
                 'anyTypeRequired2'=>2,
                 'anyTypeOptional'=>"hello",
                 'boolRequired'=>true,
                 'boolRequired2'=>false,
                 'intOptional'=>3,
                 'intRequired'=>4,
                 'doubleRequired'=>1.1,
                 'floatRequired'=>1.2,
                 'stringRequired'=>'string',
                 'name'=>'someone',
                 'arrayRequired'=>[],
                 'objectRequired'=>new SomeClass(),
                 'resourceRequired'=>fopen( "/tmp", 'r' ),
                 #'callableRequired'=>function(){}, //
                 'SomeClassRequired'=>new SomeClass(),
                 'SomeClassOptional'=>new SomeClass(),
                 'SomeClassWithPrebuiltDefault'=>new SomeClass(),
             ]
        );

        $this->failConfig(
             [
                 'anyTypeRequired'=>1,
                 'anyTypeRequired2'=>2,
                 'anyTypeOptional'=>"hello",
                 'boolRequired'=>true,
                 'boolRequired2'=>false,
                 'intOptional'=>3,
                 'intRequired'=>4,
                 'doubleRequired'=>1.1,
                 'floatRequired'=>1.2,
                 'stringRequired'=>'string',
                 'name'=>'someone',
                 'arrayRequired'=>[],
                 'objectRequired'=>new SomeClass(),
                 'resourceRequired'=>fopen( "/tmp", 'r' ),
                 'callableRequired'=>null, //
                 'SomeClassRequired'=>new SomeClass(),
                 'SomeClassOptional'=>new SomeClass(),
                 'SomeClassWithPrebuiltDefault'=>new SomeClass(),
             ]
        );

        $this->failConfig(
             [
                 'anyTypeRequired'=>1,
                 'anyTypeRequired2'=>2,
                 'anyTypeOptional'=>"hello",
                 'boolRequired'=>true,
                 'boolRequired2'=>false,
                 'intOptional'=>3,
                 'intRequired'=>4,
                 'doubleRequired'=>1.1,
                 'floatRequired'=>1.2,
                 'stringRequired'=>'string',
                 'name'=>'someone',
                 'arrayRequired'=>[],
                 'objectRequired'=>new SomeClass(),
                 'resourceRequired'=>fopen( "/tmp", 'r' ),
                 'callableRequired'=>function(){},
                 #'SomeClassRequired'=>new SomeClass(), //
                 'SomeClassOptional'=>new SomeClass(),
                 'SomeClassWithPrebuiltDefault'=>new SomeClass(),
             ]
        );

        $this->failConfig(
             [
                 'anyTypeRequired'=>1,
                 'anyTypeRequired2'=>2,
                 'anyTypeOptional'=>"hello",
                 'boolRequired'=>true,
                 'boolRequired2'=>false,
                 'intOptional'=>3,
                 'intRequired'=>4,
                 'doubleRequired'=>1.1,
                 'floatRequired'=>1.2,
                 'stringRequired'=>'string',
                 'name'=>'someone',
                 'arrayRequired'=>[],
                 'objectRequired'=>new SomeClass(),
                 'resourceRequired'=>fopen( "/tmp", 'r' ),
                 'callableRequired'=>function(){},
                 'SomeClassRequired'=>new \stdClass(), //
                 'SomeClassOptional'=>new SomeClass(),
                 'SomeClassWithPrebuiltDefault'=>new SomeClass(),
             ]
        );

        $this->failConfig(
             [
                 'anyTypeRequired'=>1,
                 'anyTypeRequired2'=>2,
                 'anyTypeOptional'=>"hello",
                 'boolRequired'=>true,
                 'boolRequired2'=>false,
                 'intOptional'=>3,
                 'intRequired'=>4,
                 'doubleRequired'=>1.1,
                 'floatRequired'=>1.2,
                 'stringRequired'=>'string',
                 'name'=>'someone',
                 'arrayRequired'=>[],
                 'objectRequired'=>new SomeClass(),
                 'resourceRequired'=>fopen( "/tmp", 'r' ),
                 'callableRequired'=>function(){},
                 'SomeClassRequired'=>new SomeClass(),
                 'SomeClassOptional'=>new \stdClass(), //
                 'SomeClassWithPrebuiltDefault'=>new SomeClass(),
             ]
        );

        $this->failConfig(
             [
                 'anyTypeRequired'=>1,
                 'anyTypeRequired2'=>2,
                 'anyTypeOptional'=>"hello",
                 'boolRequired'=>true,
                 'boolRequired2'=>false,
                 'intOptional'=>3,
                 'intRequired'=>4,
                 'doubleRequired'=>1.1,
                 'floatRequired'=>1.2,
                 'stringRequired'=>'string',
                 'name'=>'someone',
                 'arrayRequired'=>[],
                 'objectRequired'=>new SomeClass(),
                 'resourceRequired'=>fopen( "/tmp", 'r' ),
                 'callableRequired'=>function(){},
                 'SomeClassRequired'=>new SomeClass(),
                 'SomeClassOptional'=>new SomeClass(),
                 'SomeClassWithPrebuiltDefault'=>new \stdClass(), //
             ]
        );

    }

    protected function failConfig( $props ){

        try{
            $log = new MemoryLogger();
            $view = new HelloViewComponent( null, null, null, $log );
            $view->updateView( $props );
            $view->render();
        }catch ( \Exception $e ){
            $this->assertTrue( true );
            return;
        }
        $this->assertTrue( false, "Configuration that should have failed succeeded." );

    }

}
