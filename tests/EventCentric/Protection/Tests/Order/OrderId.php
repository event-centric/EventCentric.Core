<?php

namespace EventCentric\Protection\Tests\Order;

use EventCentric\Identity\Identity;
use EventCentric\Identity\Uuid\UuidIdentity;

final class OrderId implements Identity
{
    use UuidIdentity;
}