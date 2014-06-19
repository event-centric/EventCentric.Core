<?php

namespace EventCentric\Aggregates\Repository;

use EventCentric\Contract;
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

    public function __construct(Contract $contract, Identity $identity)
    {
        $this->contract = $contract;
        $this->identity = $identity;
    }

    /**
     * @return Contract
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