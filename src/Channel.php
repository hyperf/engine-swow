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

use Hyperf\Engine\Contract\ChannelInterface;
use Swow\Channel\Exception;

if (PHP_VERSION_ID > 80000) {
    class Channel extends \Swow\Channel implements ChannelInterface
    {
        protected bool $successed = true;

        public function pop($timeout = -1): mixed
        {
            $this->checkTimeout($timeout);

            try {
                $this->successed = true;
                return parent::pop($timeout == -1 ? -1 : intval($timeout * 1000));
            } catch (Exception $exception) {
                $this->successed = false;
                return false;
            }
        }

        public function push(mixed $data, $timeout = -1): bool
        {
            $this->checkTimeout($timeout);

            try {
                $this->successed = true;
                parent::push($data, $timeout == -1 ? -1 : intval($timeout * 1000));
                return true;
            } catch (Exception $exception) {
                $this->successed = false;
                return false;
            }
        }

        public function isTimeout(): bool
        {
            return ! $this->successed && $this->isAvailable();
        }

        public function isClosing(): bool
        {
            return ! $this->isAvailable();
        }

        public function close(): bool
        {
            parent::close();
            return true;
        }

        private function checkTimeout($timeout): void
        {
            if (! is_int($timeout) && ! is_float($timeout)) {
                throw new \InvalidArgumentException('timeout must be int or float.');
            }
        }
    }
} else {
    class Channel extends \Swow\Channel implements ChannelInterface
    {
        protected $successed = true;

        public function pop($timeout = -1)
        {
            $this->checkTimeout($timeout);

            try {
                $this->successed = true;
                return parent::pop($timeout == -1 ? -1 : intval($timeout * 1000));
            } catch (Exception $exception) {
                $this->successed = false;
                return false;
            }
        }

        public function push($data, $timeout = -1)
        {
            $this->checkTimeout($timeout);

            try {
                $this->successed = true;
                parent::push($data, $timeout == -1 ? -1 : intval($timeout * 1000));
                return true;
            } catch (Exception $exception) {
                $this->successed = false;
                return false;
            }
        }

        public function isTimeout()
        {
            return ! $this->successed && $this->isAvailable();
        }

        public function isClosing(): bool
        {
            return ! $this->isAvailable();
        }

        public function close(): bool
        {
            parent::close();
            return true;
        }

        private function checkTimeout($timeout): void
        {
            if (! is_int($timeout) && ! is_float($timeout)) {
                throw new \InvalidArgumentException('timeout must be int or float.');
            }
        }
    }
}
