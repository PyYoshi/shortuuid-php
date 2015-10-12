<?php

namespace ShortUUID\Tests;

use ShortUUID\ShortUUID;
use Ramsey\Uuid\Uuid;

class ShortUUIDPaddingTest extends \PHPUnit_Framework_TestCase
{
    public function testPadding()
    {
        $su = new ShortUUID();
        $randomUid = Uuid::uuid4();
        $smallestUid = Uuid::fromInteger(0);

        $encodedRandom = $su->encode($randomUid);
        $encodedSmall = $su->encode($smallestUid);

        $this->assertEquals(strlen($encodedRandom), strlen($encodedSmall));
    }

    public function testDecoding()
    {
        $su = new ShortUUID();
        $randomUid = Uuid::uuid4();
        $smallestUid = Uuid::fromInteger(0);

        $encodedRandom = $su->encode($randomUid);
        $encodedSmall = $su->encode($smallestUid);

        $this->assertEquals($su->decode($encodedSmall), $smallestUid);
        $this->assertEquals($su->decode($encodedRandom), $randomUid);
    }
}
