<?php

declare(strict_types=1);

/*
 * This file is part of the overtrue/phplint package
 *
 * (c) overtrue
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Overtrue\PHPLint\Event;

/**
 * @author Laurent Laville
 * @since Release 9.0.0
 */
interface BeforeLintFileInterface
{
    /**
     * Called before a file has been checked
     *
     * @param BeforeLintFileEvent<string, string> $event
     */
    public function beforeLintFile(BeforeLintFileEvent $event): void;
}
