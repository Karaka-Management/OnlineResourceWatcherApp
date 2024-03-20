<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\OnlineResourceWatcher\tests\Models;

use Modules\OnlineResourceWatcher\Models\NullResource;

/**
 * @internal
 */
final class NullResourceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers \Modules\OnlineResourceWatcher\Models\NullResource
     * @group module
     */
    public function testNull() : void
    {
        self::assertInstanceOf('\Modules\OnlineResourceWatcher\Models\Resource', new NullResource());
    }

    /**
     * @covers \Modules\OnlineResourceWatcher\Models\NullResource
     * @group module
     */
    public function testId() : void
    {
        $null = new NullResource(2);
        self::assertEquals(2, $null->id);
    }

    /**
     * @covers \Modules\OnlineResourceWatcher\Models\NullResource
     * @group module
     */
    public function testJsonSerialize() : void
    {
        $null = new NullResource(2);
        self::assertEquals(['id' => 2], $null->jsonSerialize());
    }
}
