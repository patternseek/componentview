<?php
/*
 * This file is part of the Patternseek ComponentView library.
 *
 * (c)2014 Tolan Blundell <tolan@patternseek.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PatternSeek\ComponentView\Template;

use PatternSeek\ComponentView\AbstractViewComponent;
use PatternSeek\ComponentView\ViewComponentResponse;
use PatternSeek\ComponentView\ViewState\ViewState;
use Twig_Autoloader;
use Twig_Environment;
use Twig_Loader_String;
use Twig_LoaderInterface;

/**
 * Class TwigTemplateString
 * @package PatternSeek\ComponentView
 */
abstract class AbstractTwigTemplate extends AbstractTemplate{

    /**
     * @var Twig_Environment
     */
    protected $twig;
    /**
     * @var string A Twig template
     */
    protected $templateNameOrContents;

    /**
     * @param AbstractViewComponent $component
     * @param string $templateNameOrContents
     */
    public function __construct( AbstractViewComponent $component, $templateNameOrContents ){
        parent::__construct( $component );
        Twig_Autoloader::register();
        $this->initTwig( $this->getLoader() );
        $this->templateNameOrContents = $templateNameOrContents;
    }

    /**
     * @param array|ViewState $state
     * @param array $componentOutputs
     * @return string
     * @internal param \PatternSeek\ComponentView\AbstractViewComponent[] $components
     */
    protected function doRender( ViewState $state, array $componentOutputs )
    {
        $this->addToTwig( $componentOutputs );

        $rendered = $this->twig->render(
            $this->templateNameOrContents,
            [
                'state' => $state,
                'this' => $this->component,
                'parent' => $this->component->getParent()
            ] );
        return new ViewComponentResponse( "text/html", $rendered );
    }

    /**
     * @return Twig_LoaderInterface
     */
    abstract protected function getLoader();

    /**
     * @param $loader
     */
    protected function initTwig( $loader )
    {
        $config = [ 'autoescape' => false ];
        if (defined( "TWIG_CACHE_DIR" )) {
            $config[ 'cache' ] = TWIG_CACHE_DIR;
        }
        $this->twig = new Twig_Environment( $loader, $config );
    }

    /**
     * @param array $componentOutputs
     */
    protected function addToTwig( array $componentOutputs )
    {
        // This is defined here as this is where $components is available. It would be better in the superclass.
        $componentRenderFunc =
            function ( $name ) use ( $componentOutputs ){
                return $componentOutputs[ $name ];
            };

        $this->twig->addFunction( 'component', new \Twig_Function_Function( $componentRenderFunc ) );
    }
}
