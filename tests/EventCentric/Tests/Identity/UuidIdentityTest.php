<?php

namespace EventCentric\Identity\Tests;

use EventCentric\Identity\UuidIdentity;

final class OrderId extends UuidIdentity
{
}

$id1 = OrderId::generate();
$id2 = OrderId::fromString((string) $id1);
$id3 = OrderId::generate();
$id4 = OrderId::fromString('c3bc7f4c-804a-4a7c-8313-51fd1b7baf52');
$id5 = OrderId::fromString('c3bc7f4c-804a-4a7c-8313-51fd1b7baf52');

assert($id1->equals($id2), "Two generated UuidIdentity objects with the same value should be equal.");
assert(!$id1->equals($id3), "Two random UuidIdentity objects should not be equal.");
assert($id4->equals($id5), "Two instantiated UuidIdentity objects with the same value should be equal.");
assert(preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i', (string) $id1),
    "Generated uuids should have the right format");

$thrown = false;
try {
    OrderId::fromString('bad-uuid');
} catch(\InvalidArgumentException $e) {
    $thrown = true;
} finally {
    assert($thrown, "Malformed uuids should throw an exception");
}
