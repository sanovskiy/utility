<?php
namespace Unit;

use PHPUnit\Framework\TestCase;
use Sanovskiy\Utility\DeprecationMonitor;
use Monolog\Logger;
use Monolog\Handler\TestHandler;

class DeprecationMonitorTest extends TestCase
{
    private TestHandler $testLogHandler;
    private Logger $testLogger;

    protected function setUp(): void
    {
        $this->testLogHandler = new TestHandler();
        $this->testLogger = new Logger('test');
        $this->testLogger->pushHandler($this->testLogHandler);

        // Resetting a singleton via trait reflection
        $reflection = new \ReflectionClass(\Sanovskiy\Traits\Patterns\Singleton::class);
        $instances = $reflection->getProperty('instances');
        $instances->setAccessible(true);

        $currentInstances = $instances->getValue();
        unset($currentInstances[DeprecationMonitor::class]);
        $instances->setValue($currentInstances);
    }

    public function testSingletonInstance()
    {
        $instance1 = DeprecationMonitor::getInstance();
        $instance2 = DeprecationMonitor::getInstance();

        $this->assertSame($instance1, $instance2);
    }

    public function testSetLogger()
    {
        $monitor = DeprecationMonitor::getInstance();
        $result = $monitor->setLogger($this->testLogger);

        $this->assertInstanceOf(DeprecationMonitor::class, $result);
    }

    public function testReportFunction()
    {
        $monitor = DeprecationMonitor::getInstance();
        $monitor->setLogger($this->testLogger);

        $monitor->reportFunction('Test function deprecation');

        // В CLI режиме логи отправляются сразу
        $this->assertTrue($this->testLogHandler->hasNoticeThatContains('Function'));
    }

    public function testReportMethod()
    {
        $monitor = DeprecationMonitor::getInstance();
        $monitor->setLogger($this->testLogger);

        $monitor->reportMethod('Test method deprecation');

        $this->assertTrue($this->testLogHandler->hasNoticeThatContains('Method'));
    }

    public function testReportClass()
    {
        $monitor = DeprecationMonitor::getInstance();
        $monitor->setLogger($this->testLogger);

        $monitor->reportClass('Test class deprecation');

        $this->assertTrue($this->testLogHandler->hasNoticeThatContains('Class'));
    }

    public function testCallerReplacement()
    {
        $monitor = DeprecationMonitor::getInstance();
        $monitor->setLogger($this->testLogger);
        $monitor->registerCallerReplacement('/var/www/', '/app/');

        $monitor->reportFunction();

        // check that the replacement worked in the logs
        $records = $this->testLogHandler->getRecords();
        $this->assertStringNotContainsString('/var/www/', $records[0]['message']);
    }

    public function testDuplicateCallsNotLoggedInWebMode()
    {
        // skip the test in CLI, since it is only for web mode
        if (PHP_SAPI === 'cli') {
            $this->markTestSkipped('This test is for web environment only');
        }

        $monitor = DeprecationMonitor::getInstance();
        $monitor->setLogger($this->testLogger);

        $monitor->reportFunction('First call');
        $monitor->reportFunction('Second call');

        $this->assertCount(1, $this->testLogHandler->getRecords());
    }

    public function testAllCallsLoggedInCliMode()
    {
        // Skipping the test in non-CLI as it is CLI only
        if (PHP_SAPI !== 'cli') {
            $this->markTestSkipped('This test is for CLI environment only');
        }

        $monitor = DeprecationMonitor::getInstance();
        $monitor->setLogger($this->testLogger);

        $monitor->reportFunction('First call');
        $monitor->reportFunction('Second call');

        // В CLI режиме логируются ВСЕ вызовы
        $this->assertCount(2, $this->testLogHandler->getRecords());
    }
}