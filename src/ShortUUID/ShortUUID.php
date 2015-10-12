<?php

namespace ShortUUID;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class ShortUUID
{

    /**
     * @var string
     */
    private $alphabet;

    /**
     * @var number
     */
    private $alphabetLength;

    /**
     * divmod(a, b) -> (div, mod)
     *
     * Return the tuple ((a-a%b)/b, a%b).
     *
     * @param $a
     * @param $b
     * @return \Moontoast\Math\BigNumber[]
     */
    public static function divmod($a, $b)
    {
        if (!$a instanceof \Moontoast\Math\BigNumber) {
            $a = new \Moontoast\Math\BigNumber($a);
        }
        if (!$b instanceof \Moontoast\Math\BigNumber) {
            $b = new \Moontoast\Math\BigNumber($b);
        }
        $x = bcdiv(bcsub($a, bcmod($a, $b)), $b);
        $y = bcmod($a, $b);
        return [
            new \Moontoast\Math\BigNumber($x),
            new \Moontoast\Math\BigNumber($y)
        ];
    }

    function __construct($alphabet = null)
    {
        if (is_null($alphabet)) {
            $alphabet = str_split('23456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz');
        }
        $this->setAlphabet($alphabet);
    }

    /**
     * Convert a number to a string, using the given alphabet.
     *
     * @param \Moontoast\Math\BigNumber $number
     * @param number $padToLength
     * @return string
     */
    private function numToString($number, $padToLength = null)
    {
        $output = '';

        while ($number->compareTo('0') > 0) {
            $ret = self::divmod($number, $this->alphabetLength);
            $number = $ret[0];
            $digit = $ret[1];
            $output .= $this->alphabet[(int)$digit->getValue()];
        }
        if (!is_null($padToLength)) {
            $reminder = max($padToLength - strlen($output), 0);
            $output = $output . str_repeat($this->alphabet[0], $reminder);
        }
        return $output;
    }

    /**
     * Convert a string to a number, using the given alphabet..
     *
     * @param string $string
     * @return int
     */
    private function stringToInt($string)
    {
        $number = new \Moontoast\Math\BigNumber(0);
        foreach (array_reverse(str_split($string)) as $char) {
            $x = bcmul($number, $this->alphabetLength);
            $y = new \Moontoast\Math\BigNumber(array_keys($this->alphabet, $char)[0]);
            $number = bcadd($x, $y);
        }
        return $number;
    }

    /**
     * Encodes a UUID into a string (LSB first) according to the alphabet
     * If leftmost (MSB) bits 0, string might be shorter
     *
     * @param UuidInterface $uuid
     * @return string
     */
    public function encode($uuid)
    {
        $padLength = $this->encodedLength(strlen($uuid->getBytes()));
        return $this->numToString($uuid->getInteger(), $padLength);
    }

    /**
     * Decodes a string according to the current alphabet into a UUID
     * Raises ValueError when encountering illegal characters
     * or too long string
     * If string too short, fills leftmost (MSB) bits with 0.
     *
     * @param $string
     * @return Uuid
     */
    public function decode($string)
    {
        return Uuid::fromInteger($this->stringToInt($string));
    }

    /**
     * Generate and return a UUID.
     *
     * If the $name parameter is provided, set the namespace to the provided
     * name and generate a UUID.
     *
     * @param string $name
     * @return string
     */
    public function uuid($name = null)
    {
        if (is_null($name)) {
            $uuid = Uuid::uuid4();
        } else if (stristr($name, 'http') == false) {
            $uuid = Uuid::uuid5(Uuid::NAMESPACE_DNS, $name);
        } else {
            $uuid = Uuid::uuid5(Uuid::NAMESPACE_URL, $name);
        }
        return $this->encode($uuid);
    }

    public function random($length = 22)
    {
        throw new \Exception('Not Implemented!!');
    }

    /**
     * Return the current alphabet used for new UUIDs.
     * @return string
     */
    public function getAlphabet()
    {
        return implode('', $this->alphabet);
    }

    /**
     * @param string|string[] $alphabet
     * @throws ValueError
     */
    public function setAlphabet($alphabet)
    {
        // Turn the alphabet into a set and sort it to prevent duplicates
        // and ensure reproducibility.
        if (is_string($alphabet)) {
            $alphabet = str_split($alphabet);
        }
        $alphabet = array_values(array_unique($alphabet));
        sort($alphabet, SORT_NATURAL);
        $newAlphabetLength = count($alphabet);
        if ($newAlphabetLength > 1) {
            $this->alphabet = $alphabet;
            $this->alphabetLength = $newAlphabetLength;
        } else {
            throw new ValueError('Alphabet with more than one unique symbols required.');
        }
    }

    /**
     * Returns the string length of the shortened UUID.
     *
     * @param int $numBytes
     * @return int
     */
    public function encodedLength($numBytes = 16)
    {
        $factor = log(256) / log($this->alphabetLength);
        return (int)ceil($factor * $numBytes);
    }
}
