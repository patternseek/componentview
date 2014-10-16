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
    protected $templateString;

    public function __construct( AbstractViewComponent $component, $templateString ){
        parent::__construct( $component );

        Twig_Autoloader::register();
        $loader = new Twig_Loader_String();
        $config = ['autoescape'=>false];
        if( defined( TWIG_CACHE_DIR ) ){
            $config['cache'] = TWIG_CACHE_DIR;
        }

        $this->twig = new Twig_Environment($loader, $config );
        $this->templateString = $templateString;
    }

    /**
     * @param array $tplInputs
     * @param \PatternSeek\ComponentView\AbstractViewComponent[] $components
     * @return string
     */
    protected function doRender( array $tplInputs, array $components )
    {
        $rendered = $this->twig->render( $this->templateString, ['inputs'=>$tplInputs, 'components'=>$components] );
        return $rendered;
    }
}