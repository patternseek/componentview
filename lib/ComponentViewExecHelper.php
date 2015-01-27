<?php
/*
 * This file is part of the Patternseek ComponentView library.
 *
 * (c)2014 Tolan Blundell <tolan@patternseek.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PatternSeek\ComponentView;

class ComponentViewExecHelper
{

    /**
     * Generate and return an appropriate URL or URI to call the exec handler identified by $execPath with $args
     * @param string $execPath Component hierarchy path to an exec handler function on a component
     * @param array $args Arguments to be passed to the called handler
     * @return string A URL or URI
     */
    function url( $execPath, $args = [ ] )
    {
        $cmd = tf_substr( get_class(), 3 );
        $qs = http_build_query( [ 'cmd' => $cmd, 'exec' => $execPath, 'args' => $args ] );
        return "?{$qs}";
    }

    /**
     * Generate and return a form, wrapping $formBody to call the exec handler identified by $execPath using the $method HTTP method
     * @param string $execPath Component hierarchy path to an exec handler function on a component
     * @param string $method
     * @param string $formBody The body of an HTML form to be wrapped
     * @return string HTML form
     */
    function wrapForm( $execPath, $method, $formBody )
    {

        return <<<EOS
				<form method="{$method}" action="">
					<input type="hidden" name="exec" value="{$execPath}">
					{$formBody}
				</form>
EOS;
    }
}
