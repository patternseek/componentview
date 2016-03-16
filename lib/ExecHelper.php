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

class ExecHelper
{

    /**
     * @var AbstractViewComponent
     */
    protected $component;

    public function setComponent( AbstractViewComponent $component )
    {
        $this->component = $component;
    }

    /**
     * Generate and return an appropriate URL or URI to call the exec handler identified by $execPath with $args
     * @param string $execMethod Component hierarchy path to an exec handler function on a component
     * @param array $args Arguments to be passed to the called handler
     * @param bool $onlyComponentOutput
     * @return string A URL or URI
     */
    public function url( $execMethod, $args = [ ], $onlyComponentOutput = false ) // $onlyComponentOutput Not used in this implementation but necessary for subclasses
    {
        // $onlyComponentOutput is not used in this implementation but may be by sub-classes
        $args[ 'exec' ] = $this->component->getExecPath( $execMethod );
        $qs = http_build_query( $args );
        return "?{$qs}";
    }

    /**
     * Generate and return a form, wrapping $formBody to call the exec handler identified by $execPath using the $method HTTP method
     * @param string $execMethod Component hierarchy path to an exec handler function on a component
     * @param string $method
     * @param string $formBody The body of an HTML form to be wrapped
     * @param bool $onlyComponentOutput
     * @param null $formID
     * @param null $onSubmit
     * @param string $encType
     * @return string HTML form
     */
    public function wrapForm(
        $execMethod,
        $method,
        $formBody,
        $onlyComponentOutput = false, // Not used in this implementation but necessary for subclasses
        $formID = null,
        $onSubmit = null,
        $encType = 'application/x-www-form-urlencoded'
    )
    {
        // $onlyComponentOutput is not used in this implementation but may be by sub-classes
        if (null !== $formID) {
            $formID = " id='{$formID}'";
        }
        if (null !== $onSubmit) {
            $onSubmit = " onsubmit='{$onSubmit}'";
        }
        return <<<EOS
<form method="{$method}" action=""{$formID}{$onSubmit} enctype="{$encType}">
    <input type="hidden" name="exec" value="{$this->component->getExecPath( $execMethod )}">
    {$formBody}
</form>
EOS;
    }

    /**
     * Helper for calling static methods
     * @param $class
     * @param $function
     * @param array $args
     * @return mixed|null
     */
    function callStatic($class, $function, $args = array())
    {
        if (class_exists($class) && method_exists($class, $function)) {
            return call_user_func_array( array( $class, $function ), $args );
        }
        return null;
    }

    /**
     * Generate a link which replaces the content of a DOM element with the output of an exec method
     * @param $execMethod
     * @param array $args
     * @param $targetDiv
     * @param $linkText
     * @param array $anchorAttrs
     * @return string
     */
    public function replaceElementUsingLink( $execMethod, $args = [ ], $targetDiv, $linkText, $anchorAttrs = [ ] )
    {
        $url = $this->url( $execMethod, $args, true );
        $attrs = [ ];
        foreach ($anchorAttrs as $k => $v) {
            $attrs[ ] = "{$k}='{$v}'";
        }
        $attrsStr = implode( ' ', $attrs );
        return <<<EOS
        <script type="application/javascript">
            if( typeof(execLink) != "function" ){
                var execLink = function( url, targetDiv ){
                    // Send the data using post
                    var posting = $.get( url, $( form ).serialize() );
                 
                    // Put the results in a div
                    posting.done(function( data ) {
                        $( "#"+targetDiv ).replaceWith( data );
                        $("body").css("cursor", "default");
                    });
    
                    // Show optional progress
                    $("body").css("cursor", "progress");
                }
            }
        </script>
        <a href="#" onclick="execLink( '{$url}', '{$targetDiv}' ); return false;" {$attrsStr}>{$linkText}</a>
EOS;
    }

    /**
     * Generate a form which replaces the content of a DOM element with the output of an exec method
     * @param $execMethod
     * @param $method
     * @param string $formBody The body of an HTML form to be wrapped
     * @param $targetDiv
     * @param $formID
     * @param string $encType
     * @return string
     */
    public function replaceElementUsingForm( $execMethod, $method, $formBody, $targetDiv, $formID, $encType = 'application/x-www-form-urlencoded' )
    {
        return <<<EOS
        {$this->wrapForm( $execMethod, $method, $formBody, true, $formID,
            "execForm( this, \"{$targetDiv}\" ); return false;", $encType )}
        <script type="application/javascript">
        if( typeof(execForm) != "function" ){
            var execForm = function( form, targetDiv ){
            
                // Send the data using post
                var posting = $.post( [location.protocol, '//', location.host, location.pathname].join(''), $( form ).serialize() );
             
                // Put the results in a div
                posting.done(function( data ) {
                    $( "#"+targetDiv ).replaceWith( data );
                    $("body").css("cursor", "default");
                });

                // Show optional progress
                $("body").css("cursor", "progress");
            }
        }
        </script>
EOS;
    }
}
