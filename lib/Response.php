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
 * Class Response
 */
/**
 * Class Response
 * @package PatternSeek\ComponentView
 */
class Response
{

    /**
     * Currently one of "redirect" or a valid MIME type.
     * @var string
     */
    public $type;
    /**
     * @var string
     */
    public $content;

    /**
     * @var int
     */
    public $responseCode;

    /**
     * @param string $type
     * @param string $content
     * @param int|null $responseCode
     */
    function __construct( $type, $content, $responseCode = null )
    {
        $this->type = $type;
        $this->content = $content;
        
        // Response code defaults
        if (null === $responseCode) {
            if( $type == "redirect" ){
                $responseCode = 302;
            }else{
                $responseCode = 200;
            }
        }
        $this->responseCode = $responseCode;
    }
}
