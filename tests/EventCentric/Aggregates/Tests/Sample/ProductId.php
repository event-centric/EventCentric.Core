<?php

namespace EventCentric\Aggregates\Tests\Sample;

use EventCentric\Identity\Identity;
use EventCentric\Identity\UuidIdentity;

final class ProductId implements Identity
{
    use UuidIdentity;
}