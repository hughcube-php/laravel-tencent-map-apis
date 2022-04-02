<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/4/20
 * Time: 4:19 下午.
 */

namespace HughCube\Laravel\Tencent\Map\Api;

use Closure;
use Exception;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\RequestOptions;
use HughCube\GuzzleHttp\Client as HttpClient;
use HughCube\GuzzleHttp\HttpClientTrait;
use HughCube\GuzzleHttp\LazyResponse;
use HughCube\PUrl\HUrl;
use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;

class Client
{
    use HttpClientTrait;

    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return array
     */
    protected function getKeys(): array
    {
        return $this->config['keys'] ?? [];
    }

    /**
     * @throws Exception
     */
    protected function randomKey()
    {
        $keys = $this->getKeys();
        if (empty($keys)) {
            return null;
        }

        return array_values($keys)[random_int(0, (count($keys) - 1))];
    }

    protected function createHttpClient(): HttpClient
    {
        $config = array_merge(
            ['base_uri' => 'https://apis.map.qq.com'],
            ($this->config['keys'] ?? [])
        );

        $config['handler'] = $handler = HandlerStack::create();
        $handler->push($this->signHandler());

        return new HttpClient($config);
    }

    protected function signHandler(): Closure
    {
        return function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                $key = $this->randomKey();
                if (!isset($key['key'])) {
                    throw new InvalidArgumentException('The correct keys must be set!');
                }

                $uri = HUrl::instance($request->getUri())->withQueryValue('key', $key['key']);

                /** 无需签名(白名单模式) */
                if (empty($key['sk'])) {
                    return $handler($request->withUri($uri), $options);
                }

                /** 组合签名 */
                $rawQuery = $uri->withSortQuery(SORT_ASC, SORT_STRING)->getRawQuery();
                $sign = $uri->getPath().($rawQuery ? "?$rawQuery" : '').$key['sk'];

                return $handler($request->withUri($uri->withQueryValue('sig', md5($sign))), $options);
            };
        };
    }

    public function request(string $method, $uri, array $options = []): LazyResponse
    {
        return $this->getHttpClient()->requestLazy(strtoupper($method), $uri, $options);
    }

    /**
     * @see https://lbs.qq.com/service/webService/webServiceGuide/webServiceSuggestion
     *
     * @param array $query
     * @param array $options
     *
     * @return LazyResponse
     */
    public function suggestion(array $query, array $options = []): LazyResponse
    {
        $options[RequestOptions::QUERY] = $query;

        return $this->request('GET', '/ws/place/v1/suggestion', $options);
    }
}
