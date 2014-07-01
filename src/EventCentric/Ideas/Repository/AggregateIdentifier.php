<?php

namespace EventCentric\Aggregates\Repository;

use EventCentric\Contracts\Contract;
use EventCentric\Identity\Identity;

final class AggregateIdentifier
{
    /**
     * @var string
     */
    private $contract;

    /**
     * @var Identity
     */
    private $identity;

    /** @todo bucket */
    public function __construct(Contract $contract, Identity $identity)
    {
        $this->contract = $contract;
        $this->identity = $identity;
    }

    /**
     * @return \EventCentric\Contracts\Contract
     */
    public function getContract()
    {
        return $this->contract;
    }

    /**
     * @return \EventCentric\Identity\Identity
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * @return string
     */
    public function hash()
    {
        return md5($this->contract . "::" . $this->identity);
    }
} 