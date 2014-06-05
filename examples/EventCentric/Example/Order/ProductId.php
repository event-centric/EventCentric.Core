<?php

namespace EventCentric\Example\Order;

use EventCentric\Identity\Identity;
use EventCentric\Identity\Uuid\UuidIdentity;

final class ProductId implements Identity
{
    use UuidIdentity;
}