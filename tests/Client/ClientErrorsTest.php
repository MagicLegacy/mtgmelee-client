<?php

/*
 * Copyright (c) Deezer
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MagicLegacy\Component\MtgMelee\Tests\Client;

use MagicLegacy\Component\MtgMelee\Client\TournamentClient;
use MagicLegacy\Component\MtgMelee\Exception\MtgMeleeClientException;
use MagicLegacy\Component\MtgMelee\Exception\MtgMeleeComponentException;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Log\NullLogger;

/**
 * Class ClientErrorsTest
 */
class ClientErrorsTest extends TestCase
{
    /**
     * @return void
     * @throws MtgMeleeComponentException
     * @throws ClientExceptionInterface
     */
    public function testAnExceptionIsThrownWithDefaultMessageWhenResponseHaveBasicContentWithHttpErrorCode(): void
    {
        $this->expectException(MtgMeleeClientException::class);
        $this->expectExceptionCode(2003);
        $this->expectExceptionMessage('[HTTP-400] An error as occurred!');

        $this->getClient(400, $this->getMinimalResponse())->getPairings(1);
    }

    /**
     * @return void
     * @throws MtgMeleeComponentException
     * @throws ClientExceptionInterface
     */
    public function testAnExceptionIsThrownWithResponseBodyWhenResponseHaveBasicContentWithHttpErrorCode(): void
    {
        $this->expectException(MtgMeleeClientException::class);
        $this->expectExceptionCode(2003);
        $this->expectExceptionMessage('[HTTP-500] Internal Error!');

        $this->getClient(500, $this->getStringResponse())->getPairings(1);
    }

    /**
     * @return void
     * @throws MtgMeleeComponentException
     * @throws ClientExceptionInterface
     */
    public function testAnExceptionIsThrownWithResponseErrorMessageWhenResponseHaveBasicContentWithHttpErrorCode(): void
    {
        $this->expectException(MtgMeleeClientException::class);
        $this->expectExceptionCode(2004);
        $this->expectExceptionMessage('[API-8888] Not Found');

        $this->getClient(404, $this->getDetailedErrorResponse())->getPairings(1);
    }

    /**
     * @return void
     * @throws MtgMeleeComponentException
     * @throws ClientExceptionInterface
     */
    public function testAnExceptionIsThrownWhenResponseHaveInvalidJsonContent(): void
    {
        $this->expectException(MtgMeleeClientException::class);
        $this->expectExceptionCode(2001);
        $this->expectExceptionMessage('[CLI-2001] Unable to decode json response!');

        $this->getClient(200, $this->getInvalidJsonResponse())->getPairings(1);
    }

    /**
     * @return void
     * @throws MtgMeleeComponentException
     * @throws ClientExceptionInterface
     */
    public function testAnExceptionIsThrownWithSpecificCodeAndMessageWhenHttpClientThrowAnException(): void
    {
        $this->expectException(MtgMeleeClientException::class);
        $this->expectExceptionCode(2000);
        $this->expectExceptionMessage('[CLI-2000] Error in http client!');

        $mockBuilder = $this->getMockBuilder(ClientExceptionInterface::class);
        $mockBuilder->setConstructorArgs(['Error in http client!', 123]);
        $exception = $mockBuilder->getMock();
        $this->getClient(200, $this->getStringResponse(), $exception)->getPairings(1);
    }

    /**
     * @param int $status
     * @param string $body
     * @param \Throwable|null $exception
     * @return TournamentClient
     */
    private function getClient(int $status, string $body, \Throwable $exception = null): TournamentClient
    {
        $httpFactory = new Psr17Factory();
        $response = $httpFactory->createResponse($status);
        $response->getBody()->write($body);
        $response->getBody()->rewind();

        $httpClientMock = $this->createMock(ClientInterface::class);

        if (!empty($exception)) {
            $httpClientMock
                ->method('sendRequest')
                ->willThrowException($exception)
            ;
        } else {
            $httpClientMock
                ->method('sendRequest')
                ->willReturn($response);
        }

        return new TournamentClient($httpClientMock, $httpFactory, $httpFactory, $httpFactory, new NullLogger());
    }

    /**
     * @return string
     */
    private function getMinimalResponse(): string
    {
        return '{
          "meta": {
            "date": "2020-01-01",
            "version": "1.0.0+20200101"
          },
          "data": {
          },
          "errors": [
          ]
        }';
    }

    /**
     * @return string
     */
    private function getDetailedErrorResponse(): string
    {
        return '{
          "meta": {
            "date": "2020-01-01",
            "version": "1.0.0+20200101"
          },
          "data": {
          },
          "errors": [
            {
              "status": "404",
              "title": "Not Found",
              "code": "8888",
              "detail": "File not found on server!"
            }
          ]
        }';
    }

    /**
     * @return string
     */
    private function getStringResponse(): string
    {
        return '"Internal Error!"';
    }

    /**
     * @return string
     */
    private function getInvalidJsonResponse(): string
    {
        return '[';
    }
}
