<?php
namespace Unit;

use PHPUnit\Framework\TestCase;
use Sanovskiy\Utility\VoidObject;

class VoidObjectTest extends TestCase
{
    public function testInvokeReturnsSelf()
    {
        $void = new VoidObject();
        $this->assertSame($void, $void());
    }

    public function testGetReturnsSelf()
    {
        $void = new VoidObject();
        $this->assertSame($void, $void->nonexistent);
    }

    public function testSetReturnsValue()
    {
        $void = new VoidObject();
        $this->assertEquals('value', $void->nonexistent = 'value');
    }

    public function testCallReturnsSelf()
    {
        $void = new VoidObject();
        $this->assertSame($void, $void->nonexistentMethod());
    }

    public function testStaticCallReturnsTrue()
    {
        $this->assertTrue(VoidObject::nonexistentStaticMethod());
    }

    public function testIssetReturnsTrue()
    {
        $void = new VoidObject();
        $this->assertTrue(isset($void->nonexistent));
    }

    public function testToStringReturnsEmpty()
    {
        $void = new VoidObject();
        $this->assertEquals('', (string)$void);
    }

    public function testUnsetDoesNothing()
    {
        $void = new VoidObject();
        unset($void->nonexistent);
        $this->assertTrue(isset($void->nonexistent)); // Всегда true
    }
}