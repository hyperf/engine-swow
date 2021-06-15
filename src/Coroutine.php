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
use Hyperf\Engine\Exception\CoroutineDestroyedException;
use Swow\Coroutine as SwowCo;

class Coroutine extends SwowCo implements CoroutineInterface
{
    /**
     * @var ArrayObject
     */
    protected $context;

    /**
     * @var int
     */
    protected $parentId;

    /**
     * @var callable[]
     */
    protected $deferCallbacks = [];

    /**
     * @var null|ArrayObject
     */
    protected static $mainContext;

    public function __construct(callable $callable)
    {
        parent::__construct($callable);
        $this->context = new ArrayObject();
        $this->parentId = static::getCurrent()->getId();
    }

    public function __destruct()
    {
        while (!empty($this->deferCallbacks)) {
            array_shift($this->deferCallbacks)();
        }
    }

    public function execute(...$data)
    {
        return $this->resume(...$data);
    }

    public function getContext()
    {
        return $this->context;
    }

    public function getParentId(): int
    {
        return $this->parentId;
    }

    public function addDefer(callable $callable)
    {
        array_unshift($this->deferCallbacks, $callable);
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

    public static function pid(?int $id = null)
    {
        if ($id === null) {
            $coroutine = static::getCurrent();
            if ($coroutine instanceof static) {
                return static::getCurrent()->getParentId();
            }
            return 0;
        }

        $coroutine = static::get($id);
        if (empty($coroutine)) {
            throw new CoroutineDestroyedException(sprintf('Coroutine #%d has been destroyed.', $id));
        }

        return $coroutine->getParentId();
    }

    public static function set(array $config)
    {
    }

    /**
     * @return null|\ArrayObject
     */
    public static function getContextFor(?int $id = null)
    {
        $coroutine = is_null($id) ? static::getCurrent() : static::get($id);
        if ($coroutine === null) {
            return null;
        }
        if ($coroutine instanceof static) {
            return $coroutine->getContext();
        }
        if (static::$mainContext === null) {
            static::$mainContext = new ArrayObject();
        }
        return static::$mainContext;
    }

    public static function defer(callable $callable)
    {
        $coroutine = static::getCurrent();
        if ($coroutine instanceof static) {
            $coroutine->addDefer($callable);
        }
    }
}
