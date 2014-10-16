<?php
/*

flow:

Although this defines how the controller will use GET and POST, the views don't care

POST /?v=MyView
post=
	e=menu.something.change
	something=whatever
$v = $_GET['v'];
include $v.php
// URL params ($_GET) are always used for 'inputs' to a view
if( ! ( $view = Session::fetch( 'views.{$v}' ) ) ){
	$view = new $v();
}
// $_POST is used for executing methods, which implicitly change state
if( $_POST ){
	$view->exec( $_POST['e'], $_POST );
}
// Enforce update() not changing state?
$agileServiceManager->enforceIdempotency( true );
$out = $view->update( $_GET );
$agileServiceManager->enforceIdempotency( false );
Session::instance()->set( 'views.{$v}', $view );

*/
/*
 * This file is part of the Patternseek ComponentView library.
 *
 * (c) 2014 Tolan Blundell <tolan@patternseek.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PatternSeek\ComponentView;

// TODO MOVEME
class ViewComponentResponse{
    public $mime;
    public $content;

    function __construct( $mime, $content ){
        $this->mime = $mime;
        $this->content = $content;
    }
}


/**
 * Class AbstractViewComponent
 * @package PatternSeek\ComponentView
 */
abstract class AbstractViewComponent
{

    function __sleep(){
        return [
            'childComponents',
            'handle',
            'parent',
            'state',
            'template'
        ];
    }

    /**
     * @var array An array of named state elements
     */
    protected $state = [ ];

    /**
     * @var array An array of named input elements for this component's template
     */
    protected $templateInputs = null;


    /**
     * @var AbstractViewComponent
     */
    protected $parent;

    /**
     * If we have a parent in $parent, $handle is the parent's handle/identifier for us
     * @var string
     */
    protected $handle;

    /**
     * @var AbstractViewComponent[]
     */
    protected $childComponents = [ ];

    /**
     * @var boolean[] Used to track which children are updated when update() is called. Those that aren't are pruned.
     */
    protected $updatedChildren = [ ];

    /**
     * @var AbstractTemplate
     */
    protected $template;

    /**
     * @param null $handle
     * @param AbstractViewComponent $parent
     */
    public function __construct( $handle = null, AbstractViewComponent $parent = null )
    {
        // Null means we are root
        $this->parent = $parent;
        $this->handle = $handle;
        $this->setupTemplate();

    }

    /**
     * Using $inputs and state, optionally update state, optionally create child components via addOrUpdateChild(), return template inputs
     * @param array $inputs
     * @return array Template inputs
     */
    abstract protected function doUpdate( array $inputs );

    /**
     * Load or configure the component's template as necessary
     *
     * @return void
     */
    abstract protected function setupTemplate();

    /**
     * Does the tree have state? If so it should be persisted between requests.
     * @return bool
     */
    public function treeHasState(){
        if( count( $this->state ) > 0 ){
            return true;
        }
        foreach( $this->childComponents as $child ){
            if( $child->treeHasState() ){
                return true;
            }
        }
        return false;
    }

    /**
     * Entry point for rendering a component tree. Call update() first.
     * @param string|null $execMethodName An optional method on this or a subcomponent to execute before rendering
     * @param array|null $execInputs
     * @throws \Exception
     * @return ViewComponentResponse
     */
    public function render( $execMethodName = null, array $execInputs = null )
    {
        if( null === $this->templateInputs ){
            throw new \Exception( "AbstractComponentView::update() must be called before render()");
        }
        // If we're called with an 'exec' then run it instead of rendering the whole tree.
        // It may still render the whole tree or it may just render a portion or just return JSON
        if( null !== $execMethodName ){
            $out = $this->exec( $execMethodName, $execInputs );
        }else{
            $out = $this->template->render( $this->templateInputs, $this->childComponents );
            if( ! ( $out instanceof ViewComponentResponse ) ){
                throw new \Exception( get_class($this->template)." returned invalid response. Should have been an instance of ViewComponentResponse" );
            }
        }
        $this->templateInputs = null;
        return $out;
    }

    /**
     * Entry point for building or updating a tree. Call before render().
     * @param $inputs
     * @throws \Exception
     */
    public function update( $inputs )
    {
        // doUpdate() creates/updates children via addOrUpdateChild()
        $this->templateInputs = $this->doUpdate( $inputs );
        // Prune children no longer in use
        foreach ( array_keys( $this->childComponents ) as $handle) {
            if (! $this->updatedChildren[$handle]) {
                unset( $this->childComponents[$handle] );
            }
        }
        $this->updatedChildren = [];
    }


    /**
     * Return the this object's path in the current component hierarchy
     * @return string
     */
    public function getPath(){
        if( null !== $this->parent ){
            return $this->parent->getPath().'.'.$this->handle;
        }
    }

    /**
     * Can create a child component on this component and return it.
     *
     * @param string $handle
     * @param string $type
     * @param array $inputs
     * @return AbstractViewComponent
     */
    public function addOrUpdateChild( $handle, $type, $inputs )
    {
        if (! $this->childComponents[$handle]) {
            $this->childComponents[$handle] = new $type( $handle, $this );
        }
        $this->childComponents[$handle]->update( $inputs );
        $this->updatedChildren[$handle] = true;
        return $this->childComponents[$handle];
    }


    /**
     * Execute a component method within the page or component.
     * Called first on a top level component which then passes the call down to the appropriate sub-component (or executes on itself if appropriate).
     * @param array|string $methodName A methodname in the format subComponent.anotherSubComponent.methodName. Either dotted string as described, or parts in an array. The top level page component shouldn't be included
     * @param array $inputs
     * @throws \Exception
     * @return ViewComponentResponse
     */
    public function exec( $methodName, array $inputs )
    {
        if (! is_array( $methodName )) {
            $methodName = explode( '.', $methodName );
        }
        if (count( $methodName ) == 1) {
            $methodName = $methodName[0] . 'Handler';
            $out = $this->$methodName( $inputs );
        } else {
            $childName = array_shift( $methodName );
            $child = $this->childComponents[$childName];
            if ($child instanceof AbstractViewComponent) {
                $out = $child->exec( $methodName, $inputs );
            }else{
                throw new \Exception( implode(".", $methodName )." is not a valid method." );
            }
        }
        if( ! ( $out instanceof ViewComponentResponse ) ){
            throw new \Exception( implode(".", $methodName )." returned invalid response. Should have been an instance of ViewComponentResponse" );
        }
        return $out;
    }

    /**
     * testInputs() compares a set of named inputs in the associative array $this->inputs with an input specification.
     * It MUST be used by implementations' update() and *Handler() methods to verify their input.
     *
     * $inputSpec is an array describing allowed inputs with a similar design to php method sigs.
     * The keys are field names, the values are 0 to 2 entry arrays with the following entries: [type,default].
     * Type can be set to null to allow any type, or if there is no default it can be left empty.
     * If default is not set then the field is required. If default is null then that us used as the default value.
     * As defaults can be any value, it's possible to create an object or callable to use as a default.
     * Type can be any of the types described at http://www.php.net/manual/en/function.gettype.php except null or unknown type. In addition it can be any class name, callable, float, bool or int.
     * E.g.
     *      [
     *          'anyTypeRequired'=>[],
     *          'anyTypeRequired2'=>[null],
     *          'anyTypeOptional'=>[null,null],
     *          'boolRequired'=>['bool'],
     *          'boolRequired2'=>['boolean'],
     *          'intOptional'=>['int',3],
     *          'intRequired'=>['integer'],
     *          'doubleRequired'=>['double'],
     *          'floatRequired'=>['float'],
     *          'stringRequired'=>['string'],
     *          'arrayRequired'=>['array'],
     *          'objectRequired'=>['object'],
     *          'resourceRequired'=>['resource'],
     *          'callableRequired'=>['callable'],
     *          'SomeClassRequired'=>['SomeClass'],
     *          'SomeClassOptional'=>['SomeClass',null],
     *          'SomeClassWithPrebuiltDefault'=>['SomeClass', new SomeClass( 'something' )],
     *      ]
     * @param array $inputSpec See above
     * @param array $inputs
     * @throws \Exception
     */
    protected function testInputs( array $inputSpec, array $inputs )
    {
        foreach ($inputSpec as $fieldName => $fieldSpec) {
            // Required field
            if (( count( $fieldSpec ) < 2 )) {
                if (! isset( $inputs[$fieldName] )) {
                    throw new \Exception( $fieldName . " is a required field for " . get_class( $this ) );
                }
            }

            // Set default is unset
            if (! isset( $inputs[$fieldName] )) {
                $inputs[$fieldName] = $fieldSpec[1];
            }

            // Check type
            $requiredType = $fieldSpec[0];
            // Any type allowed, continue
            if (! isset( $requiredType ) || $requiredType === null) {
                continue;
            }
            $input = $inputs[$fieldName];
            // Specific type required
            $failed = true;
            // Null is allowed
            if (! is_null( $input )) {
                switch ($requiredType) {
                    case "boolean":
                    case "bool":
                        if (is_bool( $input )) {
                            $failed = false;
                        }
                        break;
                    case "integer":
                    case "int":
                        if (is_int( $input )) {
                            $failed = false;
                        }
                        break;
                    case "double":
                        if (is_double( $input )) {
                            $failed = false;
                        }
                        break;
                    case "float":
                        if (is_float( $input )) {
                            $failed = false;
                        }
                        break;
                    case "string":
                        if (is_string( $input )) {
                            $failed = false;
                        }
                        break;
                    case "array":
                        if (is_array( $input )) {
                            $failed = false;
                        }
                        break;
                    case "object":
                        if (is_object( $input )) {
                            $failed = false;
                        }
                        break;
                    case "resource":
                        if (is_resource( $input )) {
                            $failed = false;
                        }
                        break;
                    case "callable":
                        if (is_callable( $input )) {
                            $failed = false;
                        }
                        break;
                    default:
                        if (get_class( $input ) == $requiredType) {
                            $failed = false;
                        }
                }
                if ($failed) {
                    throw new \Exception( $fieldName . " should be of type " . $requiredType );
                }
            }
        }
    }
}