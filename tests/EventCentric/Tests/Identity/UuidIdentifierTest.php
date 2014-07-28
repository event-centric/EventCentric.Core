<?php

namespace EventCentric\Tests\Identity;

use EventCentric\Tests\Fixtures\OrderId;
use PHPUnit_Framework_TestCase;

final class UuidIdentityTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_should_have_equality()
    {
        $id1 = OrderId::generate();
        $id2 = OrderId::fromString((string) $id1);
        $id3 = OrderId::generate();
        $id4 = OrderId::fromString('c3bc7f4c-804a-4a7c-8313-51fd1b7baf52');
        $id5 = OrderId::fromString('c3bc7f4c-804a-4a7c-8313-51fd1b7baf52');

        $this->assertTrue($id1->equals($id2), "Two generated UuidIdentifier objects with the same value should be equal.");
        $this->assertTrue(!$id1->equals($id3), "Two random UuidIdentifier objects should not be equal.");
        $this->assertTrue($id4->equals($id5), "Two instantiated UuidIdentifier objects with the same value should be equal.");
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

