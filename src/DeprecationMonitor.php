<?php namespace Sanovskiy\Utility;

use Monolog\Logger;
use Sanovskiy\Interfaces\Patterns\Singleton;

/**
 * Class DeprecationMonitor
 * @package Sanovskiy\Utility
 * @method void reportFunction($message = '')
 * @method void reportClass($message = '')
 * @method void reportMethod($message = '')
 */
class DeprecationMonitor implements Singleton
{
    use \Sanovskiy\Traits\Patterns\Singleton;

    /**
     * @var ?Logger
     */
    protected $logger = null;

    /**
     * @var array
     */
    protected $registry = [];

    /**
     * @var array
     */
    protected $replaceStringsInCallers = [];

    /**
     * @param Logger $logger
     * @return self
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * @param string $string
     * @param string $replacement
     * @return $this
     */
    public function registerCallerReplacement($string, $replacement)
    {
        $this->replaceStringsInCallers[$string] = $replacement;
        return $this;
    }

    public function __call($name, $arguments)
    {
        $caller = debug_backtrace()[1];
        $message = $arguments[0] ? $arguments[0] : '';
        switch ($name) {
            case 'reportClass':
                $this->report(sprintf("Class %s", $caller['class']), sprintf("%s:%s", $caller['file'], $caller['line']), $message);
                break;
            case 'reportMethod':
                $this->report(sprintf("Method %s::%s", $caller['class'], $caller['function']), sprintf("%s:%s", $caller['file'], $caller['line']), $message);
                break;
            case 'reportFunction':
                $this->report(sprintf("Function %s", $caller['function']), sprintf("%s:%s", $caller['file'], $caller['line']), $message);
                break;
            default:
        }
    }

    /**
     * @param string $key
     * @param string $caller
     * @param string $message
     */
    protected function report($key, $caller, $message = '')
    {
        if (!array_key_exists($key, $this->registry)) {
            $this->registry[$key] = [
                'callers' => [],
                'message' => $message
            ];
        }
        if (in_array($caller, $this->registry[$key]['callers'], true)) {
            return;
        }
        $_caller = $caller;
        foreach ($this->replaceStringsInCallers as $string => $replacement) {
            $_caller = str_replace($string, $replacement, $_caller);
        }

        if (PHP_SAPI === 'cli') {
            $record = $this->registry[$key];
            $record['callers'] = [$caller];
            $this->sendToLogs($key, $record);
            return;
        }
        $this->registry[$key]['callers'][] = $_caller;
    }

    /**
     * @param string $key
     * @param array $caller
     */
    protected function sendToLogs($key, array $caller)
    {
        if (!$this->logger instanceof Logger) {
            return;
        }
        $this->logger->notice($key . ' used.' . ($caller['message'] ? ' ' . $caller['message'] : ''), $caller['callers']);
    }

    public function __destruct()
    {
        if (PHP_SAPI !== 'cli') {
            if (count($this->registry) > 0) {
                foreach ($this->registry as $key => $caller) {
                    $this->sendToLogs($key, $caller);
                }
            }
        }
    }
}