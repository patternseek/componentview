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


class SomeClass{}

/**
 * Class AbstractViewComponentTest
 * @package PatternSeek\ComponentView\Test
 */
class AbstractViewComponentTest extends \PHPUnit_Framework_TestCase {

    function testRender(){

        // Optional off
        $inputs =
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
        $view = new HelloViewComponent();
        $view->update( $inputs );
        $outObj = $view->render();

#        print_r( $out );
#        print_r( $view );
#        $frozen = serialize( $view );
#        $defrosted = unserialize( $frozen );
#        print_r( $defrosted );

        $expected = "Hello World. From: someone";
        $outStr = str_replace(' ', '', $outObj->content);
#echo "hihi\n\n".$outStr;
        $expected = str_replace(' ', '', $expected);
        $this->assertEquals( $expected, $outStr );

    }

    function testNestedExecJSON(){

        // Optional off
        $inputs =
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
        $view = new HelloViewComponent();
        $view->update( $inputs );
        $outObj = $view->render( "world.jsonMultiply", ['multiplier'=>3] ); // Multiplies multiplier * inputs['intRequired']
        $expected = json_encode( ['result'=>12 ] );
        $this->assertEquals( $expected, $outObj->content );
    }

    function testNestedSetAndGetStateExec(){
        // Optional off
        $inputs =
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
        $view = new HelloViewComponent();
        $view->update( $inputs );
        $outObj = $view->render( "world.setState", ['something'=>5] ); // Multiplies multiplier * inputs['intRequired']
        $this->assertEquals( "OK", $outObj->content );
        $viewSer = serialize( $view );
        // Next page
        $view = unserialize( $viewSer );
        $view->update( $inputs );
        $outObj = $view->render( "world.getState" ); // Multiplies multiplier * inputs['intRequired']
        $this->assertEquals( 5, $outObj->content );
    }

    function testNestedGetStateExec(){

    }



    function testUpdate(){

        // Optional off
        $inputs =
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
        $view = new HelloViewComponent( $inputs );
        $view->update( $inputs );

        // Optional on
        $inputs =
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
        $view = new HelloViewComponent( $inputs );
        $view->update( $inputs );


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
                 'intOptional'=>FALSE, //
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

    protected function failConfig( $inputs ){

        try{
            $view = new HelloViewComponent();
            $view->update( $inputs );
        }catch ( \Exception $e ){
            $this->assertTrue( true );
            return;
        }
        $this->assertTrue( false, "Configuration that should have failed succeeded." );

    }

}