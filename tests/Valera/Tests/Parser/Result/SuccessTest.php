<?php

namespace Valera\Tests\Parser\Result;

use Valera\Parser\Result\Success;
use Valera\Resource;
use Valera\Source;

/**
 * @covers \Valera\Parser\Result\Success
 */
class SuccessTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function resourcesAreStored()
    {
        $referrer = new Resource('http://example.com');

        $u1 = 'http://example1.com';
        $u2 = 'http://example2.com';

        $success = new Success();
        $success->addSource('product', $u1, $referrer);
        $success->addSource('product', $u2, $referrer);

        $r1 = new Resource($u1, $referrer);
        $r2 = new Resource($u2, $referrer);
        $s1 = new Source('product', $r1);
        $s2 = new Source('product', $r2);

        $sources = $success->getSources();
        $this->assertCount(2, $sources);
        $this->assertContains($s1, $sources, '', false, false);
        $this->assertContains($s2, $sources, '', false, false);
    }
}
