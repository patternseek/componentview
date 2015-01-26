<?php
/*
 * This file is part of the Patternseek ComponentView library.
 *
 * (c)2015 Tolan Blundell <tolan@patternseek.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PatternSeek\ComponentView;

use PatternSeek\StructClass\StructClass;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ViewState
 * @package PatternSeek\ComponentView
 */
class ViewState extends StructClass
{

    /**
     * @var bool initialised
     *
     * @Assert\Type(type="boolean")
     */
    public $initialised = false;

}