<?php
/**
 *
 * © 2015 Tolan Blundell.  All rights reserved.
 * <tolan@patternseek.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace PatternSeek\ComponentView\Test;

use Psr\Log\LoggerInterface;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

class MemoryLogger extends AbstractLogger implements LoggerInterface
{

    public $messages = [];
    
    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     *
     * @return null
     */
    public function log( $level, $message, array $context = array() )
    {
        $message = $this->interpolate( $message, $context );
        switch($level){
            case LogLevel::EMERGENCY:
                $this->messages[] = LogLevel::EMERGENCY." : {$message}"; 
                break;
            case LogLevel::ALERT:
                $this->messages[] = LogLevel::ALERT." : {$message}";
                break;
            case LogLevel::CRITICAL:
                $this->messages[] = LogLevel::CRITICAL." : {$message}";
                break;
            case LogLevel::ERROR:
                $this->messages[] = LogLevel::ERROR." : {$message}";
                break;
            case LogLevel::WARNING:
                $this->messages[] = LogLevel::WARNING." : {$message}";
                break;
            case LogLevel::NOTICE:
                $this->messages[] = LogLevel::NOTICE." : {$message}";
                break;
            case LogLevel::INFO:
                $this->messages[] = LogLevel::INFO." : {$message}";
                break;
            case LogLevel::DEBUG:
                $this->messages[] = LogLevel::DEBUG." : {$message}";
                break;
        }
    }

    /**
     * Interpolates context values into the message placeholders.
     */
    protected function interpolate($message, array $context = array())
    {
        if( count( $context ) < 1 ){
            return $message;
        }
        
        // Cheaply check for string interpolation markers. This is dirty but we can't afford loads of processing here
        if( false !== strstr( $message, '{' ) && false !== strstr( $message, '}' ) ){
            // build a replacement array with braces around the context keys
            $replace = array();
            foreach ($context as $key => $val) {
                // Only if it can be converted to string
                if( !( method_exists($val, '__toString') || $val === null || is_scalar($val) ) ){
                    continue;
                }
                $replace['{' . $key . '}'] = $val;
            }
            // interpolate replacement values into the message and return
            $message =  strtr($message, $replace);            
        }else{
            // Attach context for messages that don't use interpolation
            $message .= " Context: ".str_replace("\n", '\n', json_encode($context) ); // If this json encode fails it may be because there's recursion
        }
        
        return $message;
    }
    
}