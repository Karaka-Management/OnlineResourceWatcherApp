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

use Modules\OnlineResourceWatcher\Models\NullInform;

/**
 * @internal
 */
final class NullInformTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers \Modules\OnlineResourceWatcher\Models\NullInform
     * @group module
     */
    public function testNull() : void
    {
        self::assertInstanceOf('\Modules\OnlineResourceWatcher\Models\Inform', new NullInform());
    }

    /**
     * @covers \Modules\OnlineResourceWatcher\Models\NullInform
     * @group module
     */
    public function testId() : void
    {
        $null = new NullInform(2);
        self::assertEquals(2, $null->id);
    }

    /**
     * @covers \Modules\OnlineResourceWatcher\Models\NullInform
     * @group module
     */
    public function testJsonSerialize() : void
    {
        $null = new NullInform(2);
        self::assertEquals(['id' => 2], $null->jsonSerialize());
    }
}
