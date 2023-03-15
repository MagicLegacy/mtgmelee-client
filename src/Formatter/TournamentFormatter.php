<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MagicLegacy\Component\MtgMelee\Formatter;

use MagicLegacy\Component\MtgMelee\Entity\Tournament;

/**
 * Class PairingsFormatter
 *
 * @author Romain Cottard
 */
final class TournamentFormatter implements FormatterInterface
{
    /**
     * Format data & return list of value object.
     *
     * @param string $data
     * @return Tournament|null
     */
    public function format($data): ?Tournament
    {
        if (empty($data)) {
            return null; // @codeCoverageIgnore
        }

        return $this->parse($data);
    }

    /**
     * @param string $data
     * @return Tournament|null
     */
    private function parse(string $data): ?Tournament
    {
        $page = new \DOMDocument();
        @$page->loadHTML($data);
        $div = $page->getElementById('tournament-headline-details-card');

        if ($div === null || !$div->hasChildNodes()) {
            return null; // @codeCoverageIgnore
        }

        $date = $this->parseDate($page);

        $rounds = (new RoundFormatter())->format($data);

        /** @var \DOMNode $node */
        foreach ($div->childNodes as $node) {
            if ($node->nodeName !== 'a') {
                continue;
            }

            /** @var \DOMElement $node */
            $name = trim((string) $node->nodeValue);
            $link = $node->getAttribute('href');
            $id   = (int) (substr($link, strrpos($link, '/') + 1));

            return new Tournament($id, $name, $link, $date, $rounds);
        }

        return null; // @codeCoverageIgnore
    }

    private function parseDate(\DOMDocument $page): \DateTimeImmutable
    {
        $defaultDate = new \DateTimeImmutable();

        $p = $page->getElementById('tournament-headline-start-date-field');
        if ($p === null || !$p->hasChildNodes()) {
            return $defaultDate; // @codeCoverageIgnore
        }

        /** @var \DOMNode $node */
        foreach ($p->childNodes as $node) {
            if ($node->nodeName !== 'span') {
                continue;
            }

            /** @var \DOMElement $node */
            $dateValue = $node->getAttribute('data-value');
            $date      = \DateTimeImmutable::createFromFormat('n/j/Y g:i:s A', $dateValue);

            return ($date !== false ? $date : $defaultDate);
        }

        return $defaultDate; // @codeCoverageIgnore
    }
}
