<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MagicLegacy\Component\MtgMelee\Formatter;

use MagicLegacy\Component\MtgMelee\Entity\Round;

/**
 * Class PairingsFormatter
 *
 * @author Romain Cottard
 */
final class RoundFormatter implements FormatterInterface
{
    /**
     * Format data & return list of value object.
     *
     * @param string $data
     * @return Round[]
     */
    public function format($data): array
    {
        $rounds = [];

        if (empty($data)) {
            return $rounds; // @codeCoverageIgnore
        }

        return $this->parse($data);
    }

    /**
     * @param string $data
     * @return Round[]
     */
    private function parse(string $data): array
    {
        $rounds = [];

        $page = new \DOMDocument();
        @$page->loadHTML($data);
        $div = $page->getElementById('pairings-round-selector-container');

        if ($div === null || !$div->hasChildNodes()) {
            return $rounds; // @codeCoverageIgnore
        }

        $lastNumber = 0;

        /** @var \DOMNode $node */
        foreach ($div->childNodes as $node) {
            if ($node->nodeName !== 'button') {
                continue;
            }

            /** @var \DOMElement $node */
            $id        = (int) $node->getAttribute('data-id');
            $name      = $node->getAttribute('data-name');
            $isStarted = ($node->getAttribute('data-is-started') === 'True');

            if (substr($name, 0, 5) === 'Round') {
                [, $number] = explode(' ', $name);
                $number = (int) $number;
                $isTop  = false;
            } else {
                $number = ($lastNumber + 1);
                $isTop  = true;
            }

            $rounds[] = new Round($id, $number, $name, $isStarted, $isTop);

            $lastNumber = $number;
        }

        return $rounds;
    }
}
