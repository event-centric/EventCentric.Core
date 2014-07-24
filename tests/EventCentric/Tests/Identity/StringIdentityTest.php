<?php

namespace EventCentric\Identity\Tests;

use EventCentric\Identity\StringIdentity;

final class ProductId extends StringIdentity
{
}

$id1 = ProductId::fromString('my_id');
$id2 = new ProductId('my_id');
$id3 = ProductId::fromString('other_id');

assert($id1->equals($id2));
assert(!$id1->equals($id3));

$thrown = false;
try {
    ProductId::fromString(123);
} catch(\InvalidArgumentException $e) {
    $thrown = true;
} finally {
    assert($thrown);
}
