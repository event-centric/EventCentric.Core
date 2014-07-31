<?php

namespace EventCentric\Identifiers;

abstract class UuidIdentifier implements Identifier, GeneratesIdentifier
{
    /**
     * @var string
     */
    private $uuid;

    private function __construct($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * Generates a UUID v4 Identifier
     * @return static
     */
    public static function generate()
    {
        return new static(self::uuid4());
    }


    /**
     * Creates an identifier object from a string representation
     * @param $string
     * @return static
     */
    public static function fromString($string)
    {
        self::guardUuid($string);
        return new static($string);
    }

    /**
     * Returns a string that can be parsed by fromString()
     * @return string
     */
    public function __toString()
    {
        return (string) $this->uuid;
    }

    /**
     * Compares the object to another IdentifiesAggregate object. Returns true if both have the same type and value.
     * @param Identifier $other
     * @return boolean
     */
    public function equals(Identifier $other)
    {
        return (string) $this == (string) $other;
    }

    /**
     * Returns a version 4 UUID
     * Borrowed from https://github.com/ramsey/uuid
     * @return string
     */
    private static function uuid4()
    {
        $bytes =
            function_exists('openssl_random_pseudo_bytes')
                ? openssl_random_pseudo_bytes(16)
                : self::generateBytes(16);


        $hash = bin2hex($bytes);

        // Set the version number
        $timeHi = hexdec(substr($hash, 12, 4)) & 0x0fff;
        $timeHi &= ~(0xf000);
        $timeHi |= 4 << 12;

        // Set the variant to RFC 4122
        $clockSeqHi = hexdec(substr($hash, 16, 2)) & 0x3f;
        $clockSeqHi &= ~(0xc0);
        $clockSeqHi |= 0x80;

        $fields = [
            'time_low' => substr($hash, 0, 8),
            'time_mid' => substr($hash, 8, 4),
            'time_hi_and_version' => sprintf('%04x', $timeHi),
            'clock_seq_hi_and_reserved' => sprintf('%02x', $clockSeqHi),
            'clock_seq_low' => substr($hash, 18, 2),
            'node' => substr($hash, 20, 12),
        ];

        return vsprintf(
            '%08s-%04s-%04s-%02s%02s-%012s',
            $fields
        );
    }

    private static function generateBytes($length)
    {
        $bytes = '';
        foreach (range(1, $length) as $i) {
            $bytes = chr(mt_rand(0, 256)) . $bytes;
        }

        return $bytes;
    }

    private static function guardUuid($string)
    {
        $pattern = '/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i';
        if(!preg_match($pattern, $string)) {
            throw new \InvalidArgumentException("UUID of the form nnnnnnnn-nnnn-nnnn-nnnn-nnnnnnnnnnnn expected");
        }
    }
}
