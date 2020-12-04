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
use Psr\Log\LoggerInterface;
use Safe\Exceptions\JsonException;

use function Safe\json_decode;
use function Safe\json_encode;

/**
 * Class AbstractClient
 *
 * Exception code range: 2000-2049
 *
 *
 * @author Romain Cottard
 */
class AbstractClient
{
    /** @var string BASE_URL */
    private const BASE_URL = 'https://mtgmelee.com';

    /** @var ClientInterface $client */
    private ClientInterface $client;

    /** @var RequestFactoryInterface $requestFactory */
    private RequestFactoryInterface $requestFactory;

    /** @var UriFactoryInterface $uriFactory */
    private UriFactoryInterface $uriFactory;

    /** @var StreamFactoryInterface $streamFactory */
    private StreamFactoryInterface $streamFactory;

    /** @var LoggerInterface $logger */
    private LoggerInterface $logger;

    /**
     * AbstractClient constructor.
     *
     * @param ClientInterface $client
     * @param RequestFactoryInterface $requestFactory
     * @param UriFactoryInterface $uriFactory
     * @param StreamFactoryInterface $streamFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        ClientInterface $client,
        RequestFactoryInterface $requestFactory,
        UriFactoryInterface $uriFactory,
        StreamFactoryInterface $streamFactory,
        LoggerInterface $logger
    ) {
        $this->client         = $client;
        $this->logger         = $logger;
        $this->requestFactory = $requestFactory;
        $this->uriFactory     = $uriFactory;
        $this->streamFactory  = $streamFactory;
    }

    /**
     * @param string $path
     * @param FormatterInterface $formatter
     * @param string $method
     * @param array $params
     * @return mixed
     * @throws MtgMeleeClientException
     */
    final protected function fetchResult(
        string $path,
        FormatterInterface $formatter,
        string $method = 'GET',
        array $params = []
    ) {
        $response    = null;
        $data        = null;
        $decodedData = null;

        try {
            $request  = $this->getRequest($path, $method, $params);
            $response = $this->client->sendRequest($request);

            $data = $response->getBody()->getContents();

            if (!empty($data)) {
                $decodedData = json_decode($data);
            }

            if ($response->getStatusCode() >= 400) {
                throw new MtgMeleeClientException();
            }
        } catch (MtgMeleeClientException $exception) {
            $code    = $this->getErrorCode($decodedData, $response);
            $message = $this->getErrorMessage($decodedData, $response, $code);

            throw new MtgMeleeClientException($message, $code, $exception);
        } catch (JsonException $exception) {
            throw new MtgMeleeClientException('[CLI-2001] Unable to decode json response!', 2001, $exception);
        } catch (ClientExceptionInterface $exception) {
            throw new MtgMeleeClientException('[CLI-2000] ' . $exception->getMessage(), 2000, $exception);
        } finally {
            if (!empty($exception) && $exception instanceof \Exception) {
                $this->getLogger()->notice($exception->getMessage(), [
                    'type'      => 'component.mtgmelee.client.fetch',
                    'exception' => $exception,
                ]);
            }
        }

        return $decodedData !== null ? $formatter->format($decodedData) : $decodedData;
    }

    /**
     * @param string $path
     * @param string $method
     * @param array $params
     * @return RequestInterface
     * @throws JsonException
     */
    protected function getRequest(string $path, string $method = 'GET', array $params = []): RequestInterface
    {
        $uri     = $this->uriFactory->createUri(self::BASE_URL . $path);
        $request = $this->requestFactory->createRequest($method, $uri);

        if (in_array($method, ['POST', 'PUT']) && isset($params['body'])) {
            $body = is_string($params['body']) ? $params['body'] : json_encode($params['body']);
            $request = $request->withBody($this->streamFactory->createStream($body));
        }

        //~ Add header
        return $request
            ->withAddedHeader('Accept', 'application/json')
        ;

    }

    /**
     * @return LoggerInterface
     */
    final protected function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @param mixed $data
     * @param ResponseInterface|null $response
     * @return int
     */
    private function getErrorCode($data, ?ResponseInterface $response): int
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
     * @param $data
     * @param ResponseInterface $response
     * @param int $internalCode
     * @return string
     */
    private function getErrorMessage($data, ResponseInterface $response, int $internalCode): string
    {
        $error = !empty($data->errors) && is_array($data->errors) ? reset($data->errors) : null;

        $prefix = '[CLI-' . $internalCode . '] ';

        //~ Override default prefix
        if (!empty($error->code)) {
            $prefix = '[API-' . $error->code . '] ';
        } elseif ($response->getStatusCode() >= 400) {
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
