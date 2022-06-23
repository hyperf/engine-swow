<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace HyperfTest\Cases;

use Swow\Buffer;

/**
 * @internal
 * @coversNothing
 */
class BufferTest extends AbstractTestCase
{
    public function testBufferToString()
    {
        $buffer = new Buffer(0);
        $buffer->write($data = uniqid());
        $this->assertSame('', $buffer->getContents());
        $this->assertSame($data, (string) $buffer);
        $this->assertSame('', $buffer->getContents());
        $this->assertSame($data, $buffer->rewind()->getContents());
    }
}
