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
namespace Hyperf\Engine;

use ArrayObject;
use Hyperf\Engine\Contract\CoroutineInterface;
use Swow\Coroutine as SwowCo;

class Coroutine extends SwowCo implements CoroutineInterface
{
    /**
     * @var ArrayObject
     */
    protected $context;

    public function __construct(callable $callable, int $stackPageSize = 0, int $stackSize = 0)
    {
        parent::__construct($callable, $stackPageSize, $stackSize);
        $this->context = new ArrayObject();
    }

    public function execute(...$data)
    {
        return $this->resume(...$data);
    }

    public static function create(callable $callable, ...$data)
    {
        $coroutine = new self($callable);
        $coroutine->resume(...$data);
        return $coroutine;
    }

    public static function id()
    {
        return static::getCurrent()->getId();
    }

    public static function set(array $config)
    {
    }

    public function getContext()
    {
        return $this->context;
    }

    public static function getContextFor(?int $id = null)
    {
        if ($id === null) {
            return static::getCurrent()->getContext();
        }
        if ($coroutine = static::get($id)) {
            return $coroutine->getContext();
        }
        return null;
    }
}
