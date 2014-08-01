<?php

namespace EventCentric\Tests\Identifiers;

use EventCentric\Identifiers\UuidIdentifier;
use EventCentric\Tests\Fixtures\OrderId;
use PHPUnit_Framework_TestCase;

final class UuidIdentityTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_should_have_equality()
    {
        $id1 = TestUuid1::generate();
        $id2 = TestUuid1::fromString((string) $id1);
        $id3 = TestUuid1::generate();
        $id4 = TestUuid1::fromString('c3bc7f4c-804a-4a7c-8313-51fd1b7baf52');
        $id5 = TestUuid1::fromString('c3bc7f4c-804a-4a7c-8313-51fd1b7baf52');

        $this->assertTrue($id1->equals($id2), "Two generated UuidIdentifier objects with the same value should be equal.");
        $this->assertTrue(!$id1->equals($id3), "Two random UuidIdentifier objects should not be equal.");
        $this->assertTrue($id4->equals($id5), "Two instantiated UuidIdentifier objects with the same value should be equal.");
    }

    /**
     * @test
     */
    public function same_value_but_different_types_should_not_be_considered_equal()
    {
        $id1 = TestUuid1::generate();
        $id2 = TestId2::fromString((string) $id1);
        $this->assertFalse($id1->equals($id2));
    }

    /**
     * @test
     */
    public function it_should_generate_valid_uuids()
    {
        $id = OrderId::generate();
        $pattern = '/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i';
        $this->assertRegExp($pattern, (string) $id);
    }

    /**
     * @test
     */
    public function it_should_throw_on_malformed_uuid()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        OrderId::fromString('bad-uuid');
    }

}

final class TestUuid1 extends UuidIdentifier {}
final class TestUuid2 extends UuidIdentifier {}