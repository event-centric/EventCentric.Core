<?php

namespace EventCentric\Tests\Identifiers;

use EventCentric\Identifiers\StringIdentifier;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;

final class StringIdentifierTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_should_have_equality()
    {

        $id1 = TestId1::fromString('my_id');
        $id2 = new TestId1('my_id');
        $id3 = TestId1::fromString('other_id');

        $this->assertTrue($id1->equals($id2));
        $this->assertTrue(!$id1->equals($id3));
    }

    /**
     * @test
     */
    public function same_value_but_different_types_should_not_be_considered_equal()
    {
        $id1 = TestId1::fromString('my_id');
        $id2 = TestId2::fromString('my_id');
        $this->assertFalse($id1->equals($id2));
    }

    /**
     * @test
     */
    public function it_should_prevent_instantiation_from_integer()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        TestId1::fromString(123);
    }
}

final class TestId1 extends StringIdentifier {}
final class TestId2 extends StringIdentifier {}