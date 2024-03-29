<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MagicLegacy\Component\MtgMelee\Formatter;

use MagicLegacy\Component\MtgMelee\Entity\Raw;

/**
 * Interface FormatterInterface
 *
 * @author Romain Cottard
 *
 * @template TEntity
 */
interface FormatterInterface
{
    /**
     * Format data & return list of value object.
     *
     * @phpstan-param \stdClass|string $data
     * @phpstan-return TEntity|TEntity[]|null
     */
    public function format(mixed $data);
}
