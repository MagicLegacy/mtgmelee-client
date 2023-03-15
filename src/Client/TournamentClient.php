<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MagicLegacy\Component\MtgMelee\Client;

use MagicLegacy\Component\MtgMelee\Entity\DeckList;
use MagicLegacy\Component\MtgMelee\Entity\Pairing;
use MagicLegacy\Component\MtgMelee\Entity\Round;
use MagicLegacy\Component\MtgMelee\Entity\Tournament;
use MagicLegacy\Component\MtgMelee\Exception\MtgMeleeClientException;
use MagicLegacy\Component\MtgMelee\Formatter\DeckListFormatter;
use MagicLegacy\Component\MtgMelee\Formatter\PairingsFormatter;
use MagicLegacy\Component\MtgMelee\Formatter\RoundFormatter;
use MagicLegacy\Component\MtgMelee\Formatter\TournamentFormatter;

/**
 * Class MtgMeleeClient
 *
 * @author Romain Cottard
 */
class TournamentClient extends AbstractClient
{
    /**
     * @return Tournament|null
     * @throws MtgMeleeClientException|\JsonException
     */
    public function getTournament(int $tournamentId): ?Tournament
    {
        return $this->fetchPageResult('/Tournament/View/' . $tournamentId, new TournamentFormatter());
    }

    /**
     * @return Round[]
     * @phpstan-return list<Round>
     * @throws MtgMeleeClientException|\JsonException
     */
    public function getRounds(int $tournamentId): array
    {
        return $this->fetchPageResult('/Tournament/View/' . $tournamentId, new RoundFormatter());
    }

    /**
     * @param int $id
     * @param int $nbResults
     * @param int $start
     * @return array<Pairing>
     * @throws MtgMeleeClientException
     */
    public function getPairings(int $id, int $nbResults = 500, int $start = 0): array
    {
        $params = [
            'body' => $this->getRoundPairingsBody($nbResults, $start),
        ];

        return $this->fetchResult('/Tournament/GetRoundPairings/' . $id, new PairingsFormatter(), 'POST', $params);
    }

    /**
     * @param int $id
     * @return DeckList
     * @throws MtgMeleeClientException
     */
    public function getDeckList(int $id): DeckList
    {
        return $this->fetchResult('/Decklist/GetDecklistDetails?id=' . $id, new DeckListFormatter());
    }

    /**
     * @param int $nbResults
     * @param int $start
     * @return string
     */
    private function getRoundPairingsBody(int $nbResults, int $start): string
    {
        $query = [
            'draw'                      => 1,
            'columns[0][data]'          => 'TableNumber',
            'columns[0][name]'          => 'TableNumber',
            'columns[0][searchable]'    => 'true',
            'columns[0][orderable]'     => 'true',
            'columns[0][search][value]' => '',
            'columns[0][search][regex]' => 'false',
            'columns[1][data]'          => 'Player1',
            'columns[1][name]'          => 'Player1',
            'columns[1][searchable]'    => 'true',
            'columns[1][orderable]'     => 'true',
            'columns[1][search][value]' => '',
            'columns[1][search][regex]' => 'false',
            'columns[2][data]'          => 'Player1Decklist',
            'columns[2][name]'          => 'Player1Decklist',
            'columns[2][searchable]'    => 'true',
            'columns[2][orderable]'     => 'true',
            'columns[2][search][value]' => '',
            'columns[2][search][regex]' => 'false',
            'columns[3][data]'          => 'Player2',
            'columns[3][name]'          => 'Player2',
            'columns[3][searchable]'    => 'true',
            'columns[3][orderable]'     => 'true',
            'columns[3][search][value]' => '',
            'columns[3][search][regex]' => 'false',
            'columns[4][data]'          => 'Player2Decklist',
            'columns[4][name]'          => 'Player2Decklist',
            'columns[4][searchable]'    => 'true',
            'columns[4][orderable]'     => 'true',
            'columns[4][search][value]' => '',
            'columns[4][search][regex]' => 'false',
            'columns[5][data]'          => 'Result',
            'columns[5][name]'          => 'Result',
            'columns[5][searchable]'    => 'false',
            'columns[5][orderable]'     => 'false',
            'columns[5][search][value]' => '',
            'columns[5][search][regex]' => 'false',
            'order[0][column]'          => '0',
            'order[0][dir]'             => 'asc',
            'start'                     => $start,
            'length'                    => $nbResults,
            'search[value]'             => '',
            'search[regex]'             => 'false',
        ];

        return http_build_query($query);
    }
}
