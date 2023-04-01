<?php

namespace Ryantxr\Slack;

use Psr\Log\LoggerInterface;

trait CanLog
{
    /**
     * @var LoggerInterface $logger The logger instance
     */
    protected $logger;
    
    /**
     * Sets the logger instance.
     *
     * @param LoggerInterface $logger The logger instance
     * 
     * @return void
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Logs a debug message.
     *
     * @param string $message The message to log
     * @param array $context The context array
     * 
     * @return void
     */
    public function debug($message, array $context = [])
    {
        if ( $this->logger instanceof LoggerInterface ) {
            $this->logger->debug($message, $context);
        }
    }

    /**
     * Logs an info message.
     *
     * @param string $message The message to log
     * @param array $context The context array
     * 
     * @return void
     */
    public function info($message, array $context = [])
    {
        if ( $this->logger instanceof LoggerInterface ) {
            $this->logger->info($message, $context);
        }
    }

    /**
     * Logs a notice message.
     *
     * @param string $message The message to log
     * @param array $context The context array
     * 
     * @return void
     */
    public function notice($message, array $context = [])
    {
        if ( $this->logger instanceof LoggerInterface ) {
            $this->logger->notice($message, $context);
        }
    }

    /**
     * Logs a warning message.
     *
     * @param string $message The message to log
     * @param array $context The context array
     * 
     * @return void
     */
    public function warning($message, array $context = [])
    {
        if ( $this->logger instanceof LoggerInterface ) {
            $this->logger->warning($message, $context);
        }
    }

    /**
     * Logs an error message.
     *
     * @param string $message The message to log
     * @param array $context The context array
     * 
     * @return void
     */
    public function error($message, array $context = [])
    {
        if ( $this->logger instanceof LoggerInterface ) {
            $this->logger->error($message, $context);
        }
    }

    /**
     * Logs a critical message.
     *
     * @param string $message The message to log
     * @param array $context The context array
     * 
     * @return void
     */
    public function critical($message, array $context = [])
    {
        if ( $this->logger instanceof LoggerInterface ) {
            $this->logger->critical($message, $context);
        }
    }

    /**
     * Logs an alert message.
     *
     * @param string $message The message to log
     * @param array $context The context array
     * 
     * @return void
     */
    public function alert($message, array $context = [])
    {
        if ( $this->logger instanceof LoggerInterface ) {
            $this->logger->alert($message, $context);
        }
    }

    /**
     * Logs an emergency message.
     *
     * @param string $message The message to log
     * @param array $context The context array
     * 
     * @return
     * void
     */
    public function emergency($message, array $context = [])
    {
        if ( $this->logger instanceof LoggerInterface ) {
            $this->logger->emergency($message, $context);
        }
    }
}