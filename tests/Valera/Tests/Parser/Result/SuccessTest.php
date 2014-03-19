<?php

namespace Valera\Tests\Parser\Result;

use Valera\Parser\Result\Success;
use Valera\Resource;

/**
 * @covers \Valera\Parser\Result\Success
 */
class SuccessTest extends \PHPUnit_Framework_TestCase
{
    public function testResourcesAreStoredStored()
    {
        $u1 = 'http://example1.com';
        $u2 = 'http://example2.com';

        $success = new Success();
        $success->addResource(null, $u1);
        $success->addResource(null, $u2);

        $r1 = new Resource(null, $u1);
        $r2 = new Resource(null, $u2);

        $resources = $success->getResources();
        $this->assertCount(2, $resources);
        $this->assertContains($r1, $resources, '', false, false);
        $this->assertContains($r2, $resources, '', false, false);
    }
}
