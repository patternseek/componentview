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

use Twig_Autoloader;
use Twig_Environment;
use Twig_Loader_String;

/**
 * Class TwigTemplateString
 * @package PatternSeek\ComponentView
 */
class TwigTemplateString extends AbstractTwigTemplate{

    /**
     * @return Twig_Loader_String
     */
    protected function getLoader()
    {
        $loader = new Twig_Loader_String();
        return $loader;
    }

}
