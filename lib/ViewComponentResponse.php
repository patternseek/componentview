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

/**
 * Class ViewComponentResponse
 */
class ViewComponentResponse
{

    /**
     * @var string
     */
    public $mime;
    /**
     * @var string
     */
    public $content;

    /**
     * @param $mime
     * @param $content
     */
    function __construct( $mime, $content )
    {
        $this->mime = $mime;
        $this->content = $content;
    }
}
