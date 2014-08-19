<?php

namespace EventCentric\Identifiers;

interface GeneratesIdentifier
{
    /**
     * @return Identifier
     */
    public static function generate();
}
