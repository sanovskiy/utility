<?php

declare(strict_types=1);

namespace Unit;

use PHPUnit\Framework\TestCase;
use Sanovskiy\Utility\Repository;

class RepositoryTest extends TestCase
{
    public function testConstructorAndArrayAccess()
    {
        $data = ['key1' => 'value1', 'key2' => 'value2'];
        $repo = new Repository($data);

        $this->assertEquals('value1', $repo['key1']);
        $this->assertEquals('value2', $repo['key2']);
        $repo['key3'] = 'value3';
        $this->assertEquals('value3', $repo['key3']);
        $this->assertTrue(isset($repo['key1']));
        $this->assertFalse(isset($repo['nonexistent']));
        unset($repo['key1']);
        $this->assertFalse(isset($repo['key1']));
    }

    public function testCountable()
    {
        $data = ['a' => 1, 'b' => 2, 'c' => 3];
        $repo = new Repository($data);

        $this->assertCount(3, $repo);
        $repo['d'] = 4;
        $this->assertCount(4, $repo);
    }

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

    public function testMagicGetSet()
    {
        $repo = new Repository([]);
        $repo->testKey = 'testValue';

        $this->assertEquals('testValue', $repo->testKey);
        $this->assertTrue(isset($repo->testKey));
        $this->assertNull($repo->nonexistent);
    }

    public function testDotNotationGet()
    {
        $data = ['a' => ['b' => ['c' => 'value']]];
        $repo = new Repository($data);

        $this->assertEquals('value', $repo->get('a.b.c'));
        $this->assertNull($repo->get('a.b.nonexistent'));
        $this->assertEquals('default', $repo->get('a.b.nonexistent', 'default'));
        $this->assertNull($repo->get('nonexistent'));
    }

    public function testDotNotationSet()
    {
        $repo = new Repository([]);
        $repo->set('a.b.c', 'value');

        $this->assertEquals('value', $repo->get('a.b.c'));
        $this->assertEquals(['a' => ['b' => ['c' => 'value']]], $repo->toArray());
    }

    public function testDotNotationHas()
    {
        $data = ['a' => ['b' => ['c' => 'value']]];
        $repo = new Repository($data);

        $this->assertTrue($repo->has('a.b.c'));
        $this->assertFalse($repo->has('a.b.nonexistent'));
        $this->assertFalse($repo->has('nonexistent'));
    }

    public function testChainedPropertyAccess()
    {
        $data = ['a' => ['b' => ['c' => 'value']]];
        $repo = new Repository($data);

        $this->assertInstanceOf(Repository::class, $repo->a);
        $this->assertInstanceOf(Repository::class, $repo->a->b);
        $this->assertEquals('value', $repo->a->b['c']);
    }

    public function testNestedRepository()
    {
        $nested = new Repository(['subkey' => 'subvalue', 'sub' => ['array' => [1, 2]]]);
        $repo = new Repository(['nested' => $nested]);

        $this->assertTrue($repo->has('nested.subkey'));
        $this->assertEquals('subvalue', $repo->get('nested.subkey'));
        $this->assertEquals([1, 2], $repo->get('nested.sub.array'));
        $this->assertFalse($repo->has('nested.nonexistent'));
        $this->assertNull($repo->get('nested.nonexistent'));

        $repo->set('nested.newkey', 'newvalue');
        $this->assertEquals('newvalue', $repo->get('nested.newkey'));
        $this->assertEquals(['subkey' => 'subvalue', 'sub' => ['array' => [1, 2]], 'newkey' => 'newvalue'], $nested->toArray());
    }

    public function testDeeplyNestedArrays()
    {
        $data = ['a' => ['b' => ['c' => ['d' => ['e' => 'value']]]]];
        $repo = new Repository($data);

        $this->assertEquals('value', $repo->get('a.b.c.d.e'));
        $this->assertInstanceOf(Repository::class, $repo->a);
        $this->assertInstanceOf(Repository::class, $repo->a->b);
        $this->assertInstanceOf(Repository::class, $repo->a->b->c);
        $this->assertEquals('value', $repo->a->b->c->d['e']);
    }

    public function testKeys()
    {
        $data = ['a' => 1, 'b' => 2, 'c' => 3];
        $repo = new Repository($data);

        $this->assertEquals(['a', 'b', 'c'], $repo->keys());
    }

    public function testToArray()
    {
        $data = ['a' => 1, 'b' => ['c' => 2]];
        $repo = new Repository($data);

        $this->assertEquals($data, $repo->toArray());

        $nested = new Repository(['c' => 2]);
        $repo = new Repository(['a' => 1, 'b' => $nested]);
        $this->assertEquals(['a' => 1, 'b' => ['c' => 2]], $repo->toArray());
    }

    public function testDebugInfo()
    {
        $data = ['a' => 1, 'b' => ['c' => 2]];
        $repo = new Repository($data);

        $this->assertEquals($data, $repo->__debugInfo());
    }

    public function testEmptyKey()
    {
        $repo = new Repository(['' => 'empty_key_value']);

        $this->assertEquals('empty_key_value', $repo->get(''));
        $this->assertTrue($repo->has(''));
        $this->assertEquals(['' => 'empty_key_value'], $repo->toArray());
    }

    public function testNumericKeys()
    {
        $data = [0 => 'zero', 1 => 'one'];
        $repo = new Repository($data);

        $this->assertEquals('zero', $repo->get(0));
        $this->assertEquals('one', $repo->get(1));
        $this->assertEquals('zero', $repo[0]);
        $this->assertEquals('one', $repo[1]);
    }

    public function testDotNotationWithLiteralDotKeys()
    {
        $data = ['r.v' => 'foo', 'r' => ['v' => 2]];
        $repo = new Repository($data);

        $this->assertEquals('foo', $repo['r.v']);
        $this->assertEquals(2, $repo->get('r.v')); // Dot notation takes precedence
        $this->assertEquals('foo', $repo->get('r.v', null, true)); // Literal key with explicit flag
        $repo->set('new.dot.key', 'bar');
        $this->assertEquals('bar', $repo->get('new.dot.key'));
    }

    public function testSetInvalidKey()
    {
        $repo = new Repository([]);

        $this->expectException(\InvalidArgumentException::class);
        $repo->{''} = 'value';
    }

    public function testOverwriteNonArray()
    {
        $repo = new Repository(['a' => 'scalar']);
        $repo->set('a.b', 'new');

        $this->assertEquals(['b' => 'new'], $repo->get('a'));
        $this->assertEquals(['a' => ['b' => 'new']], $repo->toArray());
    }
}