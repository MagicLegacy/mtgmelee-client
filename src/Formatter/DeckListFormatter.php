<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MagicLegacy\Component\MtgMelee\Formatter;

use MagicLegacy\Component\MtgMelee\Entity\DeckList;

/**
 * Class DeckListFormatter
 *
 * @author Romain Cottard
 */
final class DeckListFormatter implements FormatterInterface
{
    /**
     * Format data & return list of value object.
     *
     * @param \stdClass|null $data
     * @return DeckList|null
     */
    public function format($data)
    {
        if (empty($data)) {
            return null; // @codeCoverageIgnore
        }

        return new DeckList(
            (int) $data->ID,
            (string) ($data->Name ?? ''),
            (string) ($data->ScreenshotUrl ?? ''),
            (string) ($data->ArenaDecklistString ?? '')
        );
    }
}
