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
use PatternSeek\DependencyInjector\DependencyInjector;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Class AbstractViewComponent
 * @package PatternSeek\ComponentView
 */
abstract class AbstractViewComponent implements \JsonSerializable
{

    /**
     * @var ExecHelper
     */
    public $exec;
    /**
     * Message to display when rendering component. Won't be serialised to will only be displayed once.
     * @var string
     */
    public $flashMessage;
    /**
     * Error to display when rendering component. Won't be serialised to will only be displayed once.
     * @var string
     */
    public $flashError;
    /**
     * If we have a parent in $parent, $handle is the parent's handle/identifier for us
     * @var string
     */
    public $handle;
    /**
     * @var ViewState An object containing state elements
     */
    protected $state;
    /**
     * @var AbstractViewComponent
     */
    protected $parent;
    /**
     * @var AbstractViewComponent[]
     */
    public $childComponents = [ ];

    /**
     * @var AbstractTemplate
     */
    protected $template;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var array
     */
    protected $props;

    /**
     * If set the render() will skip any processing and immediately return this response
     *
     * @var Response
     */
    private $forceResponse;

    /**
     * @param null $handle
     * @param AbstractViewComponent $parent
     * @param ExecHelper $execHelper
     * @param LoggerInterface $logger
     * @internal param array $initConfig
     */
    public function __construct(
        $handle = null,
        AbstractViewComponent $parent = null,
        ExecHelper $execHelper = null,
        LoggerInterface $logger = null
    ){
        // Null means we are root
        $this->parent = $parent;

        // Null means we are root
        $this->handle = $handle;

        if (null === $execHelper) {
            $execHelper = new ExecHelper();
        }
        $this->setExec( $execHelper );

        $this->handleDependencyInjection();
        
        $this->setLogger( $logger );

        // Set up the state container
        $this->initState();
    }

    /**
     * @param string $message
     * @param string $level A constant from LogLevel
     */
    protected function log( $message, $level, $context = [] ){
        if( isset( $this->logger ) ){
            $class = get_class( $this );
            $message = "[{$class}] {$message}";
            if( ! is_array($context) ){
                $context = [$context];
            }
            $this->logger->log( $level, $message, $context );
        }
    }

    /**
     * User this to serialise ViewComponents as extra steps may be added later.
     * Note that we have implemented __sleep() so not all members are serialised.
     * @return string
     */
    public function dehydrate(){
        $ser = serialize($this);
        // We have to unserialise the serialised object here because it's stripped down to only the required properties by __sleep()
        $this->log( "Dehydrating", LogLevel::DEBUG, [unserialize($ser)] );
        return $ser;
    }

    /**
     * Use this to unserialise ViewComponents
     * Note that we have implemented __sleep() so not all members are serialised.
     * @param $serialised
     * @param ExecHelper $execHelper
     * @param LoggerInterface $logger
     * @return AbstractViewComponent
     */
    public static function rehydrate( $serialised, ExecHelper $execHelper, LoggerInterface $logger = null ){
        /** @var AbstractViewComponent $view */
        $view = unserialize( $serialised );
        if( null !== $logger ){
            $logger->log( LogLevel::DEBUG, "Rehydrating", [$view] );
        }
        $view->setExec( $execHelper );
        $view->handleDependencyInjection();
        $view->setLogger( $logger );
        return $view;
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        return [
            'childComponents',
            'handle',
            'parent',
            'state'
        ];
    }

    /**
     * Implement JsonSerializable interface, for logging mainly
     * @return mixed
     */
    public function jsonSerialize() {
        $ret = [];
        // Return the same fields as __sleep() specifies for serialize()
        foreach ( $this->__sleep() as $member ){
            // Skip parent because recursion isn't supported
            if( $member == 'parent'){
                continue;
            }
            $ret[$member] = $this->$member;
        }
        return $ret;
    }

    /**
     * Entry point for rendering a component tree. Call updateView() first.
     * @param string|null $execMethodName An optional method on this or a subcomponent to execute before rendering
     * @param array|null $execArgs
     * @throws \Exception
     * @return Response
     */
    public function render( $execMethodName = null, array $execArgs = null )
    {
        $this->state->validate();

        // updateState() on any component can call $this->getRootComponent()->forceResponse()
        // to force a particular response, usually a redirect.
        if (null !== $this->forceResponse) {
            return $this->forceResponse;
        }
        
        $this->initTemplate();

        // If we're called with an 'exec' then run it instead of rendering the whole tree.
        // It may still render the whole tree or it may just render a portion or just return JSON
        if ($execMethodName) { // Used to test for null but it could easily be an empty string
            $this->log( "Rendering with exec: {$execMethodName}, args:".var_export($execArgs, true ), LogLevel::DEBUG );
            $out = $this->execMethod( $execMethodName, $execArgs );
        }else {
            $this->log( "Rendering without exec", LogLevel::DEBUG );
            $out = $this->template->render( $this->state, $this->props );
            if (!( $out instanceof Response )) {
                throw new \Exception( get_class( $this->template ) . " returned invalid response. Should have been an instance of PatternSeek\ComponentView\Response" );
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
     * @return Response
     */
    protected function execMethod( $methodName, array $args = null )
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
                $out = $child->execMethod( $methodName, $args );
            }else {
                throw new \Exception( implode( ".", $methodName ) . " is not a valid method." );
            }
        }
        if (!( $out instanceof Response )) {
            $nameStr = is_array( $methodName )?implode( ".", $methodName ):$methodName;
            throw new \Exception( $nameStr . " returned invalid response. Should have been an instance of PatternSeek\ComponentView\Response" );
        }
        return $out;
    }

    /**
     * @param $execMethod
     * @return string
     */
    public function getExecPath( $execMethod )
    {
        $path = $this->getPath();
        return ( $path === null?$execMethod:$path . '.' . $execMethod );
    }

    /**
     * @return AbstractViewComponent
     */
    public function getParent()
    {
        return $this->parent;
    }


    /**
     * @param $string
     */
    protected function setFlashMessage( $string )
    {
        $this->flashMessage = $string;
    }

    /**
     * @param $string
     */
    protected function setFlashError( $string )
    {
        $this->flashError = $string;
    }

    /**
     * Get the root component of the hierarchy
     *
     * @return AbstractViewComponent
     */
    protected function getRootComponent()
    {
        $cur = $this;
        while ($cur->parent !== null) {
            $cur = $cur->parent;
        }
        return $cur;
    }

    /**
     * Load or configure the component's template as necessary.
     * Called just before the template is used so can depend on $this->state to select template.
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
     * @return AbstractViewComponent
     * @throws \Exception
     */
    protected function addOrUpdateChild( $handle, $type, array $props = [ ] )
    {
        $this->log( "Adding/updating child '{$handle}' of type {$type}", LogLevel::DEBUG );
        if (!isset( $this->childComponents[ $handle ] )) {
            if( ! class_exists( $type ) ){
                throw new \Exception( "Class '{$type}' for sub-component  does not exist." );
            }
            $child = new $type( $handle, $this, $this->exec, $this->logger );
            $this->childComponents[ $handle ] = $child;
        }else {
            // exec, di and logger are set recursively in rehydrate()
            $child = $this->childComponents[ $handle ];
        }
        $child->updateProps( $props );
        $child->updateState();
    }

    /**
     * Render a child component.
     *
     * @param $handle
     * @return Response
     * @throws \Exception
     */
    public function renderChild( $handle )
    {
        if (!$this->childComponents[ $handle ]) {
            $message = "Attempted to render nonexistent child component with handle '{$handle}'";
            $this->log( $message, LogLevel::CRITICAL );
            throw new \Exception( $message );
        }
        return $this->childComponents[ $handle ]->render()->content;
    }

    /**
     * Using $this->props and $this->state, optionally update state and create/update child components via addOrUpdateChild().
     * @return void
     */
    protected function updateState()
    {
        //
    }

    /**
     * testInputs() compares a set of named inputs (props or args) in the associative array $inputs with an input specification.
     * It MUST be used by implementations' doUpdateState() and *Handler() methods to verify their input.
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
                    $calledFunc = debug_backtrace()[1]['function'];
                    $callerFunc = debug_backtrace()[2]['function'];
                    $callerClass = debug_backtrace()[2]['class'];
                    $parentText = '';
                    if( $this->parent !== null ){
                        $parentText = " (parent component is ".get_class($this->parent).")";
                    }
                    throw new \Exception( $fieldName . " is a required field for " . get_class( $this )."::{$calledFunc}() called from {$callerClass}::{$callerFunc}(){$parentText}" );
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
            // Null is allowed
            if (!is_null( $input )) {
                switch ($requiredType) {
                    case "boolean":
                    case "bool":
                    $failed = !is_bool( $input );
                        break;
                    case "integer":
                    case "int":
                    $failed = !is_int( $input+0 );
                        break;
                    case "double":
                        $failed = !is_double( $input+0 );
                        break;
                    case "float":
                        $failed = !is_float( $input+0 );
                        break;
                    case "string":
                        $failed = !is_string( $input );
                        break;
                    case "array":
                        $failed = !is_array( $input );
                        break;
                    case "object":
                        $failed = !is_object( $input );
                        break;
                    case "resource":
                        $failed = !is_resource( $input );
                        break;
                    case "callable":
                        $failed = !is_callable( $input );
                        break;
                    default:
                        $failed = !( $input instanceof $requiredType );
                }
                if ($failed) {
                    $calledFunc = debug_backtrace()[1]['function'];
                    $callerFunc = debug_backtrace()[2]['function'];
                    $callerClass = debug_backtrace()[2]['class'];
                    $parentText = '';
                    if( $this->parent !== null ){
                        $parentText = " (parent component is ".get_class($this->parent).")";
                    }
                    throw new \Exception( $fieldName . " should be of type " . $requiredType . "in " . get_class( $this )."::{$calledFunc}() called from {$callerClass}::{$callerFunc}(){$parentText}" );
                }
            }
        }
    }

    /**
     * Update the full component view tree.
     *
     * @var array $props
     */
    public function updateView( $props )
    {
        $this->updateProps( $props );
        $this->updateState();
    }

    /**
     * Update the component's properties ('input') array
     *
     * @var array $props
     */
    protected function updateProps( $props )
    {
        $this->log( "Storing new props: ", LogLevel::DEBUG,  var_export( $props, true ) );
        $this->props = $props;   
    }

    protected function forceResponse( Response $response )
    {
        $this->forceResponse = $response;
    }

    /**
     * @param ExecHelper $execHelper
     */
    private function setExec( ExecHelper $execHelper )
    {
        $this->exec = clone $execHelper;
        $this->exec->setComponent( $this );
        foreach( $this->childComponents as $child ){
            $child->setExec( $execHelper );
        }
    }

    /**
     *
     */
    private function handleDependencyInjection()
    {
        // It's a little strange that the object injects its own
        // dependencies but it means that callers don't need to do
        // it manually and you still get the advantage that the deps
        // are specified in the optional injectDependencies() method's
        // signature
        $this->log( "Dependency injection...", LogLevel::DEBUG );
        DependencyInjector::instance()->injectIntoMethod( $this );
        foreach( $this->childComponents as $child ){
            $child->handleDependencyInjection();
        }
    }

    /**
     * @param LoggerInterface $logger
     */
    private function setLogger( LoggerInterface $logger = null )
    {
        if( null !== $logger ){
            $this->logger = $logger;
            /** @var AbstractViewComponent $child */
            foreach( $this->childComponents as $child ){
                $child->setLogger( $logger );
            }
        }
    }

    /**
     * @return LoggerInterface
     */
    protected function getLogger(){
        return $this->logger;
    }
}
