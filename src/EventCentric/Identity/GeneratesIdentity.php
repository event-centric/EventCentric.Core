<?php

namespace EventCentric\Identity;

interface GeneratesIdentity
{
    /**
     * @return Identity
     */
    public static function generate();
} 