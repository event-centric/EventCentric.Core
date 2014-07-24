<?php

namespace EventCentric\Tests\Identity;

use EventCentric\Identity\StringIdentity;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;

final class StringIdentityTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_should_have_equality()
    {

        $id1 = ProductId::fromString('my_id');
        $id2 = new ProductId('my_id');
        $id3 = ProductId::fromString('other_id');

        $this->assertTrue($id1->equals($id2));
        $this->assertTrue(!$id1->equals($id3));
    }

    /**
     * @test
     */
    public function it_should_prevent_instantiation_from_integer()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        ProductId::fromString(123);
    }
}



final class ProductId extends StringIdentity
{
}
