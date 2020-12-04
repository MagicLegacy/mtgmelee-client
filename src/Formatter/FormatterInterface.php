<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MagicLegacy\Component\MtgMelee\Formatter;

/**
 * Interface FormatterInterface
 *
 * @author Romain Cottard
 */
interface FormatterInterface
{
    /**
     * Format data & return list of value object.
     *
     * @param mixed $data
     * @return mixed|iterable
     */
    public function format($data);
}
