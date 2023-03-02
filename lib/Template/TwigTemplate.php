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
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Loader\ChainLoader;
use Twig\LoaderInterface;

/**
 * Class TwigTemplate
 * @package PatternSeek\ComponentView
 */
class TwigTemplate extends AbstractTemplate
{

    /**
     * @var Environment
     */
    protected Environment $twig;
    /**
     * @var ?string A Twig template path
     */
    protected ?string $templatePath;

    /**
     * @var string|null A Twig template already loaded as a string
     */
    protected ?string $templateString;

    /**
     * @var ResourceRepository|null
     */
    protected ?ResourceRepository $repo;

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

        $this->initTwig( $this->getLoader() );
    }

    /**
     * @param ViewState $state
     * @param array $props
     * @return Response
     */
    protected function doRender( ViewState $state, array $props = [] ): Response
    {
        // If puli plugin is available then add it.
        if (class_exists( "Puli\\TwigExtension\\PuliExtension" ) && null !== $this->repo) {
            $this->twig->addExtension( new PuliExtension( $this->repo ) );
        }
        if( $this->templateString ){
            $template = $this->twig->createTemplate($this->templateString);
        }
        
        $rendered = $this->twig->render(
            $template??$this->templatePath,
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
    protected function getLoader(): ChainLoader|ArrayLoader|LoaderInterface
    {

        if (class_exists( "Puli\\TwigExtension\\PuliExtension" ) && null !== $this->repo) {
            $loader = new ChainLoader( [ new PuliTemplateLoader( $this->repo ), new Twig_Loader_String() ] );
        }else {
            $loader = new ArrayLoader();
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
        $this->twig = new Environment( $loader, $config );
    }

}
