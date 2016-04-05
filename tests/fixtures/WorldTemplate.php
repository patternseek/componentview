<?php
/**
 *
 * © 2016 Tolan Blundell.  All rights reserved.
 * <tolan@patternseek.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
use PatternSeek\ComponentView\ExecHelper;
use PatternSeek\ComponentView\Test\ViewState\WorldState;

/** @var WorldState $state */
/** @var ExecHelper $exec */
// Following line's instruction to PHPStorm must be enabled here: Settings -> Editor -> Code style -> Formatter control
// @formatter:off 
?>
World. From: <?= $state->name ?>

Exec URL: <?= $exec->url( 'someExec',
[ 'a' => 1 ] ) ?><?php $formBody = "<input type=\"text\" name=\"someInput\" value=\"2\">\n" ?>

Exec Form:
<?= $exec->wrapForm( 'otherExec', 'POST', $formBody ) ?>