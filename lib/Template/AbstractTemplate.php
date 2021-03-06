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

/**
 * AbstractTemplates wrap and provide tools to the an actual implementation-specific template.
 * Typically there would be one subclass for each templating technology supported. Currently only Twig.
 * AbstractTemplates are held by ViewComponents. They wrap the implementation specific functionality.
 * Update and rendering of a component tree happens via the root Component's template, which in turn
 * contains various calls to AbstractTemplate::component() which will instantiate and/or update
 * the various sub-components in the tree. Unused components will be pruned (currently, this may change)
 *
 * Class AbstractTemplate
 * @package PatternSeek\ComponentView
 */
abstract class AbstractTemplate{

    /**
     * @var AbstractViewComponent
     */
    protected $component;

    /**
     * @param AbstractViewComponent $component
     */
    public function __construct( AbstractViewComponent $component ){
        $this->component = $component;
    }

    /**
     *
     * @param ViewState $state
     * @param array $props
     * @return Response
     */
    public function render( ViewState $state, array $props = [] )
    {
        return $this->doRender( $state, $props );
    }

    /**
     * @param array|ViewState $state
     * @param array $props
     * @return Response
     */
    abstract protected function doRender( ViewState $state, array $props = [] );

}
