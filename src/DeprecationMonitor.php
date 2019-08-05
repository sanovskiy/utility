<?php namespace Sanovskiy\Utility;

use Monolog\Logger;

/**
 * Class DeprecationMonitor
 * @package Sanovskiy\Utility
 * @method void reportFunction($message = '')
 * @method void reportClass($message = '')
 * @method void reportMethod($message = '')
 */
class DeprecationMonitor
{

    protected static $instance;
    /**
     * @var Logger
     */
    protected $logger = null;
    /**
     * @var array
     */
    protected $registry = [];
    protected $replaceStringsInCallers = [];

    /**
     * DeprecationMonitor constructor.
     */
    protected function __construct()
    {
    }

    /**
     * @return DeprecationMonitor
     */
    public static function getInstance(): DeprecationMonitor
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param Logger $logger
     * @return $this
     */
    public function setLogger(Logger $logger): self
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * @param $string
     * @param $replacement
     * @return $this
     */
    public function registerCallerReplacement($string, $replacement): self
    {
        $this->replaceStringsInCallers[$string] = $replacement;
        return $this;
    }

    public function __call($name, $arguments)
    {
        $caller = debug_backtrace()[1];
        $message = $arguments[0] ?? '';
        switch ($name) {
            default:
                return;
            case 'reportClass':
                $this->report('Class ' . $caller['class'], $caller['file'] . ':' . $caller['line'], $message);
                break;
            case 'reportMethod':
                $this->report('Method ' . $caller['class'] . '::' . $caller['function'], $caller['file'] . ':' . $caller['line'], $message);
                break;
            case 'reportFunction':
                $this->report('Function ' . $caller['function'], $caller['file'] . ':' . $caller['line'], $message);
                break;
        }
    }

    /**
     * @param string $key
     * @param string $caller
     * @param string $message
     */
    protected function report(string $key, string $caller, string $message = ''): void
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
     * @param string $caller
     */
    protected function sendToLogs(string $key, array $caller): void
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