<?php

namespace Valera\Tests\Parser\Result;

use Valera\Parser\Result\Success;
use Valera\Resource;

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
        $success->addResource($u1, $referrer);
        $success->addResource($u2, $referrer);

        $r1 = new Resource($u1, $referrer);
        $r2 = new Resource($u2, $referrer);

        $resources = $success->getResources();
        $this->assertCount(2, $resources);
        $this->assertContains($r1, $resources, '', false, false);
        $this->assertContains($r2, $resources, '', false, false);
    }
}
