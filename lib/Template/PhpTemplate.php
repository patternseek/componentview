<?php
/*
 * This file is part of the Patternseek ComponentView library.
 *
 * (c)2016 Tolan Blundell <tolan@patternseek.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PatternSeek\ComponentView\Template;

use PatternSeek\ComponentView\AbstractViewComponent;
use PatternSeek\ComponentView\Response;
use PatternSeek\ComponentView\ViewState\ViewState;
use Puli\Repository\Api\ResourceRepository;

/**
 * This class allows the use of PHP files as templates.
 * Twig templates are generally preferred but in certain
 * cases such as porting legacy code a PHP template may
 * save a lot of work.
 *
 * Class PhpTemplate
 * @package PatternSeek\ComponentView
 */
class PhpTemplate extends AbstractTemplate
{

    /**
     * @var string A PHP template path
     */
    protected $templatePath;

    /**
     * @var string A PHP template string
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
    }

    /**
     * @param ViewState $state
     * @param array $props
     * @return Response
     * @throws \Exception
     */
    protected function doRender( ViewState $state, array $props = [ ] )
    {
        // Available variables in template file are:
        // $state
        // $props
        // $thisComponent (equivalent of 'this' in TwigTemplates)
        // $parent
        // $exec

        /** @noinspection PhpUnusedLocalVariableInspection */
        $thisComponent = $this->component;
        /** @noinspection PhpUnusedLocalVariableInspection */
        $parent = $this->component->getParent();
        /** @noinspection PhpUnusedLocalVariableInspection */
        $exec = $this->component->exec;
        if ($this->templatePath) {
            $realPath = $this->getRealTemplatePath( $this->templatePath );
            ob_start();
            /** @noinspection PhpIncludeInspection */
            include( $realPath );
            $rendered = ob_get_clean();
        }elseif ($this->templateString) {
            ob_start();
            eval( "?>" . $this->templateString );
            $rendered = ob_get_clean();
        }else {
            throw new \Exception( "Neither valid template path no valid template string passed to PhpTemplate." );
        }

        return new Response( "text/html", $rendered );
    }

    /**
     * @param $templatePath
     * @return null|string
     * @throws \Exception
     */
    private function getRealTemplatePath( $templatePath )
    {
        if ($this->repo instanceof ResourceRepository) {
            if ($this->repo->contains( $templatePath )) {
                return $this->repo->get( $templatePath )
                    ->getPath();
            }
        }
        if (file_exists( $templatePath )) {
            return $templatePath;
        }
        throw new \Exception( "Template path not found in repository or on filesystem: {$templatePath}, in PhpTemplate" );
    }

}
