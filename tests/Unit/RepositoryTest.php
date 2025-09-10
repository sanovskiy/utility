<?php
namespace Unit;

use PHPUnit\Framework\TestCase;
use Sanovskiy\Utility\Repository;

class RepositoryTest extends TestCase
{
    // Test basic construction and array access
    public function testConstructorAndArrayAccess()
    {
        $data = ['key1' => 'value1', 'key2' => 'value2'];
        $repo = new Repository($data);

        $this->assertEquals('value1', $repo['key1']);
        $this->assertEquals('value2', $repo['key2']);
    }

    // Test Countable
    public function testCountable()
    {
        $data = ['a' => 1, 'b' => 2, 'c' => 3];
        $repo = new Repository($data);

        $this->assertCount(3, $repo);
    }

    // Test Iterator
    public function testIterator()
    {
        $data = ['a' => 1, 'b' => 2, 'c' => 3];
        $repo = new Repository($data);

        $iterated = [];
        foreach ($repo as $key => $value) {
            $iterated[$key] = $value;
        }

        $this->assertEquals($data, $iterated);
    }

    // Test magic getter/setter
    public function testMagicGetSet()
    {
        $repo = new Repository([]);
        $repo->testKey = 'testValue';

        $this->assertEquals('testValue', $repo->testKey);
        $this->assertTrue(isset($repo->testKey));
    }

    // Test keyExists method
    public function testKeyExists()
    {
        $data = ['existing' => 'value'];
        $repo = new Repository($data);

        $this->assertTrue($repo->keyExists('existing'));
        $this->assertFalse($repo->keyExists('non-existing'));
    }

    // Test toArray method
    public function testToArray()
    {
        $data = ['a' => 1, 'b' => ['c' => 2]];
        $repo = new Repository($data);

        $result = $repo->toArray();
        $this->assertEquals($data, $result);
    }

    // Test toArray with nested Repository objects
    public function testToArrayWithNestedRepositories()
    {
        $data = ['a' => 1, 'b' => new Repository(['c' => 2])];
        $repo = new Repository($data);

        $result = $repo->toArray();
        $this->assertEquals(['a' => 1, 'b' => ['c' => 2]], $result);
    }

    // Test debug info
    public function testDebugInfo()
    {
        $data = ['key' => 'value'];
        $repo = new Repository($data);

        $this->assertEquals($data, $repo->__debugInfo());
    }

    // Test dot notation get
    public function testGetWithDotNotation()
    {
        $data = ['a' => ['b' => ['c' => 'value']]];
        $repo = new Repository($data);

        $this->assertEquals('value', $repo->get('a.b.c'));
        $this->assertNull($repo->get('a.b.nonexistent'));
        $this->assertEquals('default', $repo->get('a.b.nonexistent', 'default'));
    }

    // Test dot notation set
    public function testSetWithDotNotation()
    {
        $repo = new Repository([]);
        $repo->set('a.b.c', 'value');

        $this->assertEquals('value', $repo->get('a.b.c'));
        $this->assertEquals(['a' => ['b' => ['c' => 'value']]], $repo->toArray());
    }

    // Test dot notation has
    public function testHasWithDotNotation()
    {
        $data = ['a' => ['b' => ['c' => 'value']]];
        $repo = new Repository($data);

        $this->assertTrue($repo->has('a.b.c'));
        $this->assertFalse($repo->has('a.b.nonexistent'));
        $this->assertFalse($repo->has('nonexistent'));
    }

    // Test keys method
    public function testKeys()
    {
        $data = ['a' => 1, 'b' => 2, 'c' => 3];
        $repo = new Repository($data);

        $this->assertEquals(['a', 'b', 'c'], $repo->keys());
    }

    // Test magic get with dot notation
    public function testMagicGetWithDotNotation()
    {
        $data = ['a' => ['b' => 'value']];
        $repo = new Repository($data);

        $this->assertNull($repo->get('a.b.c'));

        $data = ['a' => 'not_an_array'];
        $repo = new Repository($data);
        $this->assertNull($repo->get('a.b'));
    }

    // Test magic set with dot notation
    public function testMagicSetWithDotNotation()
    {
        $repo = new Repository([]);
        $repo->{'a.b.c'} = 'value';

        $this->assertEquals('value', $repo->get('a.b.c'));
    }

    // Test edge case - empty key
    public function testEmptyKey()
    {
        $repo = new Repository(['' => 'empty_key_value']);

        $this->assertEquals('empty_key_value', $repo->get(''));
        $this->assertTrue($repo->has(''));
    }

    // Test edge case - numeric keys
    public function testNumericKeys()
    {
        $data = [0 => 'zero', 1 => 'one'];
        $repo = new Repository($data);

        $this->assertEquals('zero', $repo->get(0));
        $this->assertEquals('one', $repo->get(1));
    }

    // Test magic method chaining
    public function testMagicMethodChaining()
    {
        $data = ['cache_driver' => 'redis'];
        $repo = new Repository($data);

        $this->assertEquals('redis', $repo->getCacheDriver());
        $this->assertTrue($repo->hasCacheDriver());
    }

    // Test magic method setter chaining
    public function testMagicMethodSetterChaining()
    {
        $repo = new Repository([]);
        $result = $repo->setCacheDriver('redis');

        $this->assertInstanceOf(Repository::class, $result);
        $this->assertEquals('redis', $repo->get('cache_driver'));
    }

    // Test invalid magic method
    public function testInvalidMagicMethod()
    {
        $this->expectException(\BadMethodCallException::class);

        $repo = new Repository([]);
        $repo->invalidMethod();
    }
}