<?php

namespace EventCentric\Protection\Tests\AggregateRoot\Order;

use EventCentric\Identity\Identity;
use EventCentric\Identity\Uuid\UuidIdentity;

final class ProductId implements Identity
{
    use UuidIdentity;
}