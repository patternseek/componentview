<?php
namespace PatternSeek\ComponentView\Test;

use PatternSeek\ComponentView\AbstractTemplate;
use PatternSeek\ComponentView\ViewComponentResponse;

/**
 * For the love of all that is good please never use this template class in non-test code.
 * For testing code however it's kind of handy.
 *
 * Class EvilEvalTestTemplate
 * @package PatternSeek\ComponentView\Test
 */
class EvilEvalTestTemplate extends AbstractTemplate{

    private $template;

    /**
     * @param \PatternSeek\ComponentView\AbstractViewComponent $parent
     * @param $template
     */
    function __construct( $parent, $template ){
        parent::__construct($parent);
        $this->template = $template;
    }

    /**
     * @param array $tplInputs
     * @param \PatternSeek\ComponentView\AbstractViewComponent[] $components
     * @return string
     */
    protected function doRender( array $tplInputs, array $components )
    {
        $that = $tplInputs['_this'];
        unset( $tplInputs['_this'] );
        ob_start();
        eval( $this->template );
        $out = ob_get_clean();
        return new ViewComponentResponse( "text/html", $out );
    }
}
