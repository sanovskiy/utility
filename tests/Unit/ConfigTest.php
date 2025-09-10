<?php
namespace Unit;

use PHPUnit\Framework\TestCase;
use Sanovskiy\Utility\Config;
use RuntimeException;

class ConfigTest extends TestCase
{
    private string $testConfigPath;

    protected function setUp(): void
    {
        $this->testConfigPath = __DIR__ . '/../test_configs';
        // Создаем временную папку для тестовых конфигов
        if (!file_exists($this->testConfigPath)) {
            mkdir($this->testConfigPath, 0777, true);
        }
    }

    protected function tearDown(): void
    {
        // Удаляем тестовые конфиги после каждого теста
        if (file_exists($this->testConfigPath)) {
            array_map('unlink', glob($this->testConfigPath . '/*.php'));
            rmdir($this->testConfigPath);
        }
    }

    public function testLoadConfigArrayBasic()
    {
        // Создаем простой конфиг
        file_put_contents($this->testConfigPath . '/test.php', '<?php return ["key" => "value"];');

        $config = Config::loadConfigArray($this->testConfigPath, 'test');

        $this->assertEquals(['key' => 'value'], $config);
    }

    public function testLoadConfigArrayWithInheritance()
    {
        // Базовый конфиг
        file_put_contents($this->testConfigPath . '/base.php', '<?php return [
            "parent_key" => "parent_value",
            "common" => ["a" => 1, "b" => 2]
        ];');

        // Дочерний конфиг
        file_put_contents($this->testConfigPath . '/child.php', '<?php return [
            "parent_env" => "base",
            "child_key" => "child_value",
            "common" => ["b" => 999, "c" => 3] // Redefine b and add c
        ];');

        $config = Config::loadConfigArray($this->testConfigPath, 'child');

        $expected = [
            'parent_key' => 'parent_value',
            'child_key' => 'child_value',
            'common' => ['a' => 1, 'b' => 999, 'c' => 3] // Recursive merge
        ];

        $this->assertEquals($expected, $config);
    }

    public function testLoadConfigArrayDeepInheritance()
    {
        file_put_contents($this->testConfigPath . '/grandparent.php', '<?php return [
            "level" => "grandparent",
            "settings" => ["a" => 1]
        ];');

        file_put_contents($this->testConfigPath . '/parent.php', '<?php return [
            "parent_env" => "grandparent",
            "level" => "parent",
            "settings" => ["b" => 2]
        ];');

        file_put_contents($this->testConfigPath . '/child.php', '<?php return [
            "parent_env" => "parent",
            "level" => "child",
            "settings" => ["c" => 3]
        ];');

        $config = Config::loadConfigArray($this->testConfigPath, 'child');

        $expected = [
            'level' => 'child',
            'settings' => ['a' => 1, 'b' => 2, 'c' => 3] // Все уровни наследования
        ];

        $this->assertEquals($expected, $config);
    }

    public function testLoadConfigArrayMissingFileThrowsException()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No config found for nonexistent');

        Config::loadConfigArray($this->testConfigPath, 'nonexistent');
    }

    public function testLoadConfigArrayInvalidPathThrowsException()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('is not a real directory');

        Config::loadConfigArray('/nonexistent/path', 'test');
    }

    public function testConfigConstructorWithNestedArrays()
    {
        $data = [
            'database' => [
                'host' => 'localhost',
                'port' => 3306
            ],
            'logging' => [
                'level' => 'debug',
                'file' => 'app.log'
            ]
        ];

        $config = new Config($data);

        // Проверяем, что вложенные массивы тоже стали Config объектами
        $this->assertInstanceOf(Config::class, $config->database);
        $this->assertInstanceOf(Config::class, $config->logging);

        $this->assertEquals('localhost', $config->database->host);
        $this->assertEquals('debug', $config->logging->level);
    }

    public function testDeprecatedMergeArrayStillWorks()
    {
        $array1 = ['a' => 1, 'b' => ['c' => 2]];
        $array2 = ['b' => ['d' => 3], 'e' => 4];

        $result = Config::mergeArray($array1, $array2);

        $expected = [
            'a' => 1,
            'b' => ['c' => 2, 'd' => 3],
            'e' => 4
        ];

        $this->assertEquals($expected, $result);
    }

    public function testToArrayWithNestedConfigObjects()
    {
        $data = [
            'level1' => new Config(['level2' => new Config(['value' => 'test'])])
        ];
        $config = new Config($data);

        $result = $config->toArray();

        $expected = [
            'level1' => [
                'level2' => [
                    'value' => 'test'
                ]
            ]
        ];

        $this->assertEquals($expected, $result);
    }
}