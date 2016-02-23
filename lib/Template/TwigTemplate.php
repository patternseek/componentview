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
use PatternSeek\ComponentView\Response;
use PatternSeek\ComponentView\ViewState\ViewState;
use Puli\Repository\Api\ResourceRepository;
use Puli\TwigExtension\PuliExtension;
use Puli\TwigExtension\PuliTemplateLoader;
use Twig_Autoloader;
use Twig_Environment;
use Twig_Loader_String;
use Twig_LoaderInterface;

/**
 * Class TwigTemplate
 * @package PatternSeek\ComponentView
 */
class TwigTemplate extends AbstractTemplate
{

    /**
     * @var Twig_Environment
     */
    protected $twig;
    /**
     * @var string A Twig template path
     */
    protected $templatePath;

    /**
     * @var string A Twig template already loaded as a string
     */
    protected $templateString;

    /**
     * @var ResourceRepository
     */
    protected $repo;

    /**
     * @param AbstractViewComponent $component
     * @param string $templatePath
     * @param null $templateString
     * @param ResourceRepository $repo
     */
    public function __construct(
        AbstractViewComponent $component,
        $templatePath = null,
        $templateString = null,
        ResourceRepository $repo = null
    ){
        parent::__construct( $component );
        $this->templatePath = $templatePath;
        $this->templateString = $templateString;
        // Optional Puli repo
        $this->repo = $repo;

        Twig_Autoloader::register();
        $this->initTwig( $this->getLoader() );
    }

    /**
     * @param ViewState $state
     * @param array $props
     * @return Response
     */
    protected function doRender( ViewState $state, array $props = [] )
    {
        // If puli plugin is available then add it.
        if (class_exists( "Puli\\TwigExtension\\PuliExtension" ) && null !== $this->repo) {
            $this->twig->addExtension( new PuliExtension( $this->repo ) );
        }

        $rendered = $this->twig->render(
            $this->templateString?$this->templateString:$this->templatePath,
            [
                'state' => $state,
                'props' => $props,
                'this' => $this->component,
                'parent' => $this->component->getParent(),
                'exec' => $this->component->exec
            ] );
        return new Response( "text/html", $rendered );
    }

    /**
     * @return Twig_LoaderInterface
     */
    protected function getLoader()
    {

        if (class_exists( "Puli\\TwigExtension\\PuliExtension" ) && null !== $this->repo) {
            $loader = new \Twig_Loader_Chain( [ new PuliTemplateLoader( $this->repo ), new Twig_Loader_String() ] );
        }else {
            $loader = new Twig_Loader_String();
        }
        return $loader;
    }

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

}
