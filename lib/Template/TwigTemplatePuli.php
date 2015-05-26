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
use Puli\Repository\Api\ResourceRepository;
use Puli\TwigExtension\PuliExtension;
use Puli\TwigExtension\PuliTemplateLoader;
use Twig_Autoloader;
use Twig_Environment;
use Twig_Loader_String;

/**
 * Class TwigTemplateString
 * @package PatternSeek\ComponentView
 */
class TwigTemplatePuli extends AbstractTwigTemplate{

    /**
     * @param AbstractViewComponent $component
     * @param string $templateNameOrContents
     * @param ResourceRepository $repo
     */
    public function __construct( AbstractViewComponent $component, $templateNameOrContents, ResourceRepository $repo ){
        $this->repo = $repo;
        parent::__construct( $component, $templateNameOrContents );
    }

    /**
     * @param array $componentOutputs
     */
    protected function addToTwig( array $componentOutputs ){
        $this->twig->addExtension(new PuliExtension($this->repo));
        parent::addToTwig( $componentOutputs );
    }

    /**
     * @return Twig_Loader_String
     */
    protected function getLoader()
    {
        $loader = new PuliTemplateLoader( $this->repo );
        return $loader;
    }

}
