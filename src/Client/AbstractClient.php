<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MagicLegacy\Component\MtgMelee\Client;

use MagicLegacy\Component\MtgMelee\Exception\MtgMeleeClientException;
use MagicLegacy\Component\MtgMelee\Formatter\FormatterInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

/**
 * Class AbstractClient
 * Exception code range : 2000-2049
 *
 * @author Romain Cottard
 */
class AbstractClient
{
    private const BASE_URL = 'https://mtgmelee.com';

    public function __construct(
        private readonly ClientInterface $client,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly UriFactoryInterface $uriFactory,
        private readonly StreamFactoryInterface $streamFactory
    ) {
    }

    /**
     * @template TEntity
     * @phpstan-param FormatterInterface<TEntity> $formatter
     * @phpstan-param array<string, string|int|float|bool|array<string|int>> $params
     * @phpstan-return TEntity|TEntity[]|null
     * @throws MtgMeleeClientException
     */
    final protected function fetch(
        string $path,
        FormatterInterface $formatter,
        string $method = 'GET',
        array $params = []
    ) {
        $response    = null;
        $decodedData = null;

        try {
            $request  = $this->getRequest($path, $method, $params);
            $response = $this->client->sendRequest($request);

            $data = $response->getBody()->getContents();

            if (!empty($data)) {
                /** @var string|\stdClass|null $decodedData */
                $decodedData = json_decode($data, null, 512, JSON_THROW_ON_ERROR);
            }

            if ($response->getStatusCode() >= 400) {
                throw new MtgMeleeClientException();
            }
        } catch (MtgMeleeClientException $exception) {
            $code    = $this->getErrorCode($decodedData, $response);
            $message = $this->getErrorMessage($decodedData, $response, $code);

            throw new MtgMeleeClientException($message, $code, $exception);
        } catch (\JsonException $exception) {
            throw new MtgMeleeClientException('[CLI-2001] Unable to decode json response!', 2001, $exception);
        } catch (ClientExceptionInterface $exception) {
            throw new MtgMeleeClientException('[CLI-2000] ' . $exception->getMessage(), 2000, $exception);
        }

        return $decodedData !== null ? $formatter->format($decodedData) : null;
    }

    /**
     * @template TEntity
     * @phpstan-param FormatterInterface<TEntity> $formatter
     * @phpstan-param array<string, string|int|float|bool|array<string|int>> $params
     * @phpstan-return TEntity|null
     * @throws MtgMeleeClientException
     */
    final protected function fetchResult(
        string $path,
        FormatterInterface $formatter,
        string $method = 'GET',
        array $params = []
    ) {
        /** @var TEntity|null $result */
        $result = $this->fetch($path, $formatter, $method, $params);

        return $result;
    }

    /**
     * @template TEntity
     * @phpstan-param FormatterInterface<TEntity> $formatter
     * @phpstan-param array<string, string|int|float|bool|array<string|int>> $params
     * @phpstan-return list<TEntity>
     * @throws MtgMeleeClientException
     */
    final protected function fetchResults(
        string $path,
        FormatterInterface $formatter,
        string $method = 'GET',
        array $params = []
    ): array {
        /** @var list<TEntity> $result */
        $result = $this->fetch($path, $formatter, $method, $params);

        return $result;
    }

    /**
     * @template TEntity
     * @phpstan-param FormatterInterface<TEntity> $formatter
     * @phpstan-param array<string, string|int|float|bool|array<string|int>> $params
     * @phpstan-return TEntity|TEntity[]|null
     * @throws MtgMeleeClientException|\JsonException
     */
    final protected function fetchPage(
        string $path,
        FormatterInterface $formatter,
        string $method = 'GET',
        array $params = []
    ) {
        $response = null;

        try {
            $request  = $this->getRequest($path, $method, $params, false);
            $response = $this->client->sendRequest($request);

            $data = $response->getBody()->getContents();

            if ($response->getStatusCode() >= 400) {
                throw new MtgMeleeClientException(); // @codeCoverageIgnore
            }
        // @codeCoverageIgnoreStart
        } catch (MtgMeleeClientException $exception) {
            $code    = 2005;
            $message = 'Error ' . ($response !== null ? $response->getStatusCode() : ' XXX') . ' on call!';

            throw new MtgMeleeClientException($message, $code, $exception);
        } catch (ClientExceptionInterface $exception) {
            throw new MtgMeleeClientException('[CLI-2006] ' . $exception->getMessage(), 2006, $exception);
        }
        // @codeCoverageIgnoreEnd

        return $formatter->format($data);
    }

    /**
     * @template TEntity
     * @phpstan-param FormatterInterface<TEntity> $formatter
     * @phpstan-param array<string, string|int|float|bool|array<string|int>> $params
     * @phpstan-return TEntity|null
     * @throws MtgMeleeClientException|\JsonException
     */
    final protected function fetchPageResult(
        string $path,
        FormatterInterface $formatter,
        string $method = 'GET',
        array $params = []
    ) {
        /** @var TEntity|null $result */
        $result = $this->fetchPage($path, $formatter, $method, $params);

        return $result;
    }

    /**
     * @template TEntity
     * @phpstan-param FormatterInterface<TEntity> $formatter
     * @phpstan-param array<string, string|int|float|bool|array<string|int>> $params
     * @phpstan-return list<TEntity>
     * @throws MtgMeleeClientException|\JsonException
     */
    final protected function fetchPageResults(
        string $path,
        FormatterInterface $formatter,
        string $method = 'GET',
        array $params = []
    ): array {
        /** @var list<TEntity> $result */
        $result = $this->fetchPage($path, $formatter, $method, $params);

        return $result;
    }

    /**
     * @phpstan-param array<string, string|int|float|bool|array<string|int|float|bool>> $params
     * @throws \JsonException
     */
    protected function getRequest(
        string $path,
        string $method = 'GET',
        array $params = [],
        bool $isJsonApi = true
    ): RequestInterface {
        $uri     = $this->uriFactory->createUri(self::BASE_URL . $path);
        $request = $this->requestFactory->createRequest($method, $uri);

        if (in_array($method, ['POST', 'PUT']) && isset($params['body'])) {
            $body = is_string($params['body']) ? $params['body'] : json_encode($params['body'], JSON_THROW_ON_ERROR);
            $request = $request->withBody($this->streamFactory->createStream($body));
        }

        //~ Add header
        if ($isJsonApi) {
            $request = $request->withAddedHeader('Accept', 'application/json');
        }

        return$request;
    }

    /**
     * @param string|\stdClass|null $data
     * @param ResponseInterface|null $response
     * @return int
     */
    private function getErrorCode(string|\stdClass|null $data, ?ResponseInterface $response): int
    {
        $code = 1002;

        if (!empty($data->errors)) {
            $code = 2004;
        } elseif ($response !== null && $response->getStatusCode() >= 400) {
            $code = 2003;
        }

        return $code;
    }

    /**
     * @param string|\stdClass|null $data
     * @param ResponseInterface|null $response
     * @param int $internalCode
     * @return string
     */
    private function getErrorMessage(
        string|\stdClass|null $data,
        ?ResponseInterface $response,
        int $internalCode
    ): string {
        $error = !empty($data->errors) && is_array($data->errors) ? reset($data->errors) : null;

        $prefix = '[CLI-' . $internalCode . '] ';

        //~ Override default prefix
        if (!empty($error->code)) {
            $prefix = '[API-' . $error->code . '] ';
        } elseif ($response !== null && $response->getStatusCode() >= 400) {
            $prefix = '[HTTP-' . $response->getStatusCode() . '] ';
        }

        if (is_string($data)) {
            $message = $data;
        } else {
            $message = $error->title ?? 'An error as occurred!';
        }

        return $prefix . $message;
    }
}
