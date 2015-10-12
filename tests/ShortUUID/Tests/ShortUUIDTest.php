<?php

namespace ShortUUID\Tests;

use ShortUUID\ShortUUID;
use ShortUUID\ValueError;
use Ramsey\Uuid\Uuid;

class ShortUUIDTest extends \PHPUnit_Framework_TestCase
{
    public function testGeneration()
    {
        $su = new ShortUUID();
        $a = strlen($su->uuid());
        $this->assertTrue((20 < $a) && ($a < 24));
        $b = strlen($su->uuid('http://www.example.com/'));
        $this->assertTrue((20 < $b) && ($b < 24));
        $c = strlen($su->uuid('HTTP://www.example.com/'));
        $this->assertTrue((20 < $c) && ($c < 24));
        $d = strlen($su->uuid('example.com/'));
        $this->assertTrue((20 < $d) && ($d < 24));
    }

    public function testEncoding()
    {
        $su = new ShortUUID();
        $u = Uuid::fromString('3b1f8b40-222c-4a6e-b77e-779d5a94e21c');
        $this->assertEquals($su->encode($u), 'bYRT25J5s7Bniqr4b58cXC');
    }

    public function testDecoding()
    {
        $su = new ShortUUID();
        $u = Uuid::fromString('3b1f8b40-222c-4a6e-b77e-779d5a94e21c');
        $this->assertEquals($su->decode('bYRT25J5s7Bniqr4b58cXC'), $u);
    }

    public function testAlphabet()
    {
        $alphabet = '01';
        $su1 = new ShortUUID($alphabet);
        $su2 = new ShortUUID();

        $this->assertEquals($alphabet, $su1->getAlphabet());

        $su1->setAlphabet('01010101010101');
        $this->assertEquals($alphabet, $su1->getAlphabet());

        $d = array_values(array_unique(str_split($su1->uuid())));
        sort($d, SORT_NATURAL);
        $this->assertEquals($d, str_split('01'));

        $a = strlen($su1->uuid());
        $b = strlen($su2->uuid());
        $this->assertTrue(($a > 116) && ($a < 140));
        $this->assertTrue(($b > 20) && ($b < 24));

        $u = Uuid::uuid4();
        $this->assertEquals($u, $su1->decode($su1->encode($u)));

        $u = $su1->uuid();
        $this->assertEquals($u, $su1->encode($su1->decode($u)));

        try {
            $su1->setAlphabet('1');
        } catch (ValueError $e) {
            $this->assertTrue(true);
        }

        try {
            $su1->setAlphabet('1111111');
        } catch (ValueError $e) {
            $this->assertTrue(true);
        }
    }

    public function testEncodedLength()
    {
        $su1 = new ShortUUID();
        $this->assertEquals($su1->encodedLength(), 22);

        $base64Alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';

        $su2 = new ShortUUID($base64Alphabet);
        $this->assertEquals($su2->encodedLength(), 22);

        $binaryAlphabet = '01';
        $su3 = new ShortUUID($binaryAlphabet);
        $this->assertEquals($su3->encodedLength(), 128);

        $su4 = new ShortUUID();
        $this->assertEquals($su4->encodedLength(8), 11);
    }
}