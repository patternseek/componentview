<?php
/*
 * This file is part of the Patternseek ComponentView library.
 *
 * (c) 2014 Tolan Blundell <tolan@patternseek.net>
 *
 * For the full copyright and license information, please view the LICENSE
  * file that was distributed with this source code.
 */

namespace PatternSeek\ComponentView;

use PatternSeek\ComponentView\Template\AbstractTemplate;
use PatternSeek\ComponentView\ViewState\ViewState;

/**
 * Class AbstractViewComponent
 * @package PatternSeek\ComponentView
 */
abstract class AbstractViewComponent
{

    /**
     * @var ExecHelper
     */
    public $exec;
    /**
     * @var ViewState An object containing state elements
     */
    protected $state;
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
     * @param array $initConfig
     * @param ExecHelper $execHelper
     */
    public function __construct(
        $handle = null,
        AbstractViewComponent $parent = null,
        $initConfig = [ ],
        ExecHelper $execHelper = null
    ){
        // Null means we are root
        $this->parent = $parent;

        // Null means we are root
        $this->handle = $handle;

        if (null === $execHelper) {
            $execHelper = new ExecHelper();
        }

        $this->exec = clone $execHelper;
        $this->exec->setComponent( $this );

        // Set up the template
        $this->initTemplate();

        // Set up the state container
        $this->initState();

        // Perform one-time init, if implemented by subclass
        $this->initComponent( $initConfig );
    }

    /**
     * @return array
     */
    function __sleep()
    {
        return [
            'childComponents',
            'handle',
            'parent',
            'state',
            'exec'
        ];
    }

    /**
     * Fetch the root component and render it
     * @return ViewComponentResponse
     * @throws \Exception
     */
    public function renderRoot()
    {
        $cur = $this;
        while ($cur->parent !== null) {
            $cur = $cur->parent;
        }
        return $cur->render();
    }

    /**
     * Entry point for rendering a component tree. Call update() first.
     * @param string|null $execMethodName An optional method on this or a subcomponent to execute before rendering
     * @param array|null $execArgs
     * @throws \Exception
     * @return ViewComponentResponse
     */
    public function render( $execMethodName = null, array $execArgs = null )
    {
        // Test state
        $this->state->validate();
        $this->initTemplate();

        // If we're called with an 'exec' then run it instead of rendering the whole tree.
        // It may still render the whole tree or it may just render a portion or just return JSON
        if ($execMethodName) { // Used to test for null but it could easily be an empty string
            $out = $this->exec( $execMethodName, $execArgs );
        }else {
            $out = $this->template->render( $this->state, $this->childComponents );
            if (!( $out instanceof ViewComponentResponse )) {
                throw new \Exception( get_class( $this->template ) . " returned invalid response. Should have been an instance of ViewComponentResponse" );
            }
        }
        return $out;
    }

    /**
     * Execute a component method within the page or component.
     * Called first on a top level component which then passes the call down to the appropriate sub-component (or executes on itself if appropriate).
     * @param array|string $methodName A methodname in the format subComponent.anotherSubComponent.methodName. Either dotted string as described, or parts in an array. The top level page component shouldn't be included
     * @param array $args
     * @throws \Exception
     * @return ViewComponentResponse
     */
    public function exec( $methodName, array $args = null )
    {
        if (!is_array( $methodName )) {
            $methodName = explode( '.', $methodName );
        }
        if (count( $methodName ) == 1) {
            $methodName = $methodName[ 0 ] . 'Handler';
            $out = $this->$methodName( $args );
        }else {
            $childName = array_shift( $methodName );
            $child = $this->childComponents[ $childName ];
            if ($child instanceof AbstractViewComponent) {
                $out = $child->exec( $methodName, $args );
            }else {
                throw new \Exception( implode( ".", $methodName ) . " is not a valid method." );
            }
        }
        if (!( $out instanceof ViewComponentResponse )) {
            $nameStr = is_array( $methodName )?implode( ".", $methodName ):$methodName;
            throw new \Exception( $nameStr . " returned invalid response. Should have been an instance of ViewComponentResponse" );
        }
        return $out;
    }

    public function getExecPath( $execMethod )
    {
        $path = $this->getPath();
        return ( $path == null?$execMethod:$path . '.' . $execMethod );
    }

    /**
     * @return AbstractViewComponent
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Entry point for building or updating a tree. Call before render() when instantiating the component tree.
     * @param $props
     * @throws \Exception
     */
    public function update( $props = [ ] )
    {
        // doUpdate() creates/updates children via addOrUpdateChild()
        $this->doUpdate( $props );

        // Prune children no longer in use
        foreach (array_keys( $this->childComponents ) as $handle) {
            if (!$this->updatedChildren[ $handle ]) {
                unset( $this->childComponents[ $handle ] );
            }
        }
        $this->updatedChildren = [ ];
    }

    /**
     * Load or configure the component's template as necessary
     *
     * @return void
     */
    abstract protected function initTemplate();

    /**
     * Initialise $this->state with either a new ViewState or an appropriate subclass
     * @return void
     */
    abstract protected function initState();

    /**
     * @param $initConfig
     * @return void
     *
     */
    protected function initComponent( $initConfig )
    {
        // Optional override
    }

    /**
     * Return the this object's path in the current component hierarchy
     * @return string
     */
    protected function getPath()
    {
        if (null === $this->parent) {
            return null;
        }
        if (null !== ( $pPath = $this->parent->getPath() )) {
            return $pPath . '.' . $this->handle;
        }else {
            return $this->handle;
        }
    }

    /**
     * Can create a child component on this component and return it.
     *
     * @param string $handle
     * @param string $type
     * @param array $props
     * @param array $initConfig
     * @return AbstractViewComponent
     * @throws \Exception
     */
    protected function addOrUpdateChild( $handle, $type, $props, array $initConfig = null )
    {
        if (!isset( $this->childComponents[ $handle ] )) {
            $child = new $type( $handle, $this, $initConfig, $this->exec );
            $this->childComponents[ $handle ] = $child;
        }else {
            $child = $this->childComponents[ $handle ];
        }
        $child->update( $props );
        $this->updatedChildren[ $handle ] = true;
        return $this->childComponents[ $handle ];
    }

    /**
     * Using $this->state, optionally update state, optionally create child components via addOrUpdateChild().
     * @param array $props
     * @return void
     */
    abstract protected function doUpdate( $props );

    /**
     * testInputs() compares a set of named inputs (props or args) in the associative array $inputs with an input specification.
     * It MUST be used by implementations' doUpdate() and *Handler() methods to verify their input.
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
    protected function testInputs( array $inputSpec, array &$inputs )
    {
        foreach ($inputSpec as $fieldName => $fieldSpec) {
            // Required field
            if (( count( $fieldSpec ) < 2 )) {
                if (!isset( $inputs[ $fieldName ] )) {
                    throw new \Exception( $fieldName . " is a required field for " . get_class( $this ) );
                }
            }

            // Set default is unset
            if (!isset( $inputs[ $fieldName ] )) {
                $inputs[ $fieldName ] = $fieldSpec[ 1 ];
            }

            // Check type
            // Any type allowed, continue
            if (!isset( $fieldSpec[ 0 ] ) || $fieldSpec[ 0 ] === null) {
                continue;
            }
            $requiredType = $fieldSpec[ 0 ];
            $input = $inputs[ $fieldName ];
            // Specific type required
            $failed = true;
            // Null is allowed
            if (!is_null( $input )) {
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
