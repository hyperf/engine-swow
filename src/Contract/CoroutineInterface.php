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
namespace Hyperf\Engine\Contract;

interface CoroutineInterface
{
    /**
     * @param callable $callable [required]
     * @param int $stackPageSize [optional] = 0
     * @param int $stackSize [optional] = 0
     */
    public function __construct(callable $callable, int $stackPageSize = 0, int $stackSize = 0);

    /**
     * @param callable $callable [required]
     * @param mixed ...$data
     * @return $this
     */
    public static function create(callable $callable, ...$data);

    /**
     * @param mixed ...$data
     * @return $this
     */
    public function execute(...$data);

    /**
     * @return int
     */
    public function getId();

    /**
     * @return int
     */
    public static function id();
}
