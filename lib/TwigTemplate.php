<?php
/*
 * This file is part of the Patternseek ComponentView library.
 *
 * (c)2014 Tolan Blundell <tolan@patternseek.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PatternSeek\ComponentView;

use Twig_Autoloader;
use Twig_Environment;
use Twig_Loader_String;

class TwigTemplate extends AbstractTemplate{

    /**
     * @var Twig_Environment
     */
    protected $twig;
    /**
     * @var string A Twig template
     */
    protected $templateString;

    public function __construct( AbstractViewComponent $component, $templateString ){
        parent::__construct( $component );

        Twig_Autoloader::register();
        $loader = new Twig_Loader_String();
        $config = ['autoescape'=>false];
        if( defined( "TWIG_CACHE_DIR" ) ){
            $config['cache'] = TWIG_CACHE_DIR;
        }

        $this->twig = new Twig_Environment($loader, $config );
        $this->templateString = $templateString;
    }

    /**
     * @param array $props
     * @param \PatternSeek\ComponentView\AbstractViewComponent[] $components
     * @return string
     */
    protected function doRender( array $props, array $components )
    {

        // This is defined here as this is where $components is available. It would be better in the superclass.
        $componentRenderFunc =
            function( $name ) use ($components){
                return $components[$name]->render()->content;
            };

        $this->twig->addFunction('component', new \Twig_Function_Function( $componentRenderFunc ));
        $this->twig->addFunction('execURL', new \Twig_Function_Function( $this->component->execURLHelper ));
        $this->twig->addFunction('execForm', new \Twig_Function_Function( $this->component->execFormHelper ));


        $rendered = $this->twig->render( $this->templateString, ['this'=>$this, 'props'=>$props, 'components'=>$components, 'component'=>$componentRenderFunc] );
        return new ViewComponentResponse( "text/html", $rendered );
    }

}