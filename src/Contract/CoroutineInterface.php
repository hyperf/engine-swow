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

use Hyperf\Engine\Exception\RuntimeException;

interface CoroutineInterface
{
    /**
     * @param callable $callable [required]
     */
    public function __construct(callable $callable);

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

    /**
     * Returns the parent coroutine ID.
     * Returns 0 when running in the top level coroutine.
     * @throws RuntimeException when running in non-coroutine context
     */
    public static function pid(?int $id = null);

    public static function set(array $config);

    /**
     * @param null|int $id coroutine id
     * @return null|\ArrayObject
     */
    public static function getContextFor(?int $id = null);
}
