<?php
/*
 * This file is part of the Patternseek ECommerce library.
 *
 * (c)2015 Tolan Blundell <tolan@patternseek.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PatternSeek\ComponentView\Test\ViewState;

use PatternSeek\ComponentView\ViewState\ViewState;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class WorldState
 * @package PatternSeek\ComponentView\Test
 */
class HelloState extends ViewState
{

    /**
     * @var bool
     *
     * @Assert\Type(type="integer")
     */
    public $intRequired;

    /**
     * @var string
     *
     * @Assert\Type(type="string")
     */
    public $name;

}