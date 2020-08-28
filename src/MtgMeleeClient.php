<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MagicLegacy\Component\MtgMelee\Client;

use MagicLegacy\Component\MtgMelee\Client\Exception\ClientException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class Standing
 *
 * @author Romain Cottard
 */
class MtgMeleeClient
{
    /** @var ClientInterface $client */
    private $client;

    /** @var RequestFactoryInterface $requestFactory */
    private $requestFactory;

    /** @var UriFactoryInterface $uriFactory */
    private $uriFactory;

    /** @var StreamFactoryInterface $streamFactory */
    private $streamFactory;

    /**
     * Client constructor.
     *
     * @param ClientInterface $client
     * @param RequestFactoryInterface $requestFactory
     * @param UriFactoryInterface $uriFactory
     * @param StreamFactoryInterface $streamFactory
     */
    public function __construct(
        ClientInterface $client,
        RequestFactoryInterface $requestFactory,
        UriFactoryInterface $uriFactory,
        StreamFactoryInterface $streamFactory
    ) {
        $this->client         = $client;
        $this->requestFactory = $requestFactory;
        $this->uriFactory     = $uriFactory;
        $this->streamFactory  = $streamFactory;
    }

    /**
     * @param int $id
     * @param int $nbResults
     * @param int $start
     * @return \stdClass
     * @throws ClientException
     */
    public function getRoundPairings(int $id, int $nbResults = 500, int $start = 0): \stdClass
    {
        $request = $this->getRequest($id, $nbResults, $start);

        try {
            $response = $this->client->sendRequest($request);
            $json     = $response->getBody()->getContents();
            $data     = json_decode($json);

            if (empty($data)) {
                throw new ClientException('Unable to decode json response (not json ?)');
            }

        } catch (\Exception | ClientExceptionInterface $exception) {
            throw new ClientException(
                'Error from client: "[' . $exception->getCode() . '] ' . $exception->getMessage() . '"'
            );
        }

        return $data;
    }

    /**
     * @param int $id
     * @param int $nbResults
     * @param int $start
     * @return RequestInterface
     */
    private function getRequest(int $id, int $nbResults, int $start): RequestInterface
    {
        $request = $this->requestFactory->createRequest('POST', $this->getUri($id));

        $request = $request
            ->withAddedHeader('Accept', 'application/json')
            ->withAddedHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->withAddedHeader('X-Requested-With', 'XMLHttpRequest')
        ;

        $request->getBody()->write($this->getRoundPairingsBody($nbResults, $start));

        return $request;
    }

    /**
     * @param int $id
     * @return UriInterface
     */
    private function getUri(int $id): UriInterface
    {
        return $this->uriFactory->createUri('https://mtgmelee.com/Tournament/GetRoundPairings/' . $id);
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
            'columns[0][data]'          => 'Player1',
            'columns[0][name]'          => 'Player1',
            'columns[0][searchable]'    => 'true',
            'columns[0][orderable]'     => 'true',
            'columns[0][search][value]' => '',
            'columns[0][search][regex]' => 'false',
            'columns[1][data]'          => 'Player1Decklist',
            'columns[1][name]'          => 'Player1Decklist',
            'columns[1][searchable]'    => 'true',
            'columns[1][orderable]'     => 'true',
            'columns[1][search][value]' => '',
            'columns[1][search][regex]' => 'false',
            'columns[2][data]'          => 'Player2',
            'columns[2][name]'          => 'Player2',
            'columns[2][searchable]'    => 'true',
            'columns[2][orderable]'     => 'true',
            'columns[2][search][value]' => '',
            'columns[2][search][regex]' => 'false',
            'columns[3][data]'          => 'Player2Decklist',
            'columns[3][name]'          => 'Player2Decklist',
            'columns[3][searchable]'    => 'true',
            'columns[3][orderable]'     => 'true',
            'columns[3][search][value]' => '',
            'columns[3][search][regex]' => 'false',
            'columns[4][data]'          => 'Result',
            'columns[4][name]'          => 'Result',
            'columns[4][searchable]'    => 'false',
            'columns[4][orderable]'     => 'false',
            'columns[4][search][value]' => '',
            'columns[4][search][regex]' => 'false',
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
