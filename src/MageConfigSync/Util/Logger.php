<?php

namespace MageConfigSync\Util;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerTrait;

trait Logger
{
    use LoggerAwareTrait, LoggerTrait;
    
    /**
     * @param       $level
     * @param       $message
     * @param array $context
     */
    public function log($level, $message, array $context = array())
    {
        $this->logger->log($level, $message, $context);
    }
}
