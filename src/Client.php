<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/4/20
 * Time: 4:19 下午.
 */

namespace HughCube\Laravel\Tencent\Map\Api;

use Closure;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\RequestOptions;
use HughCube\GuzzleHttp\Client as HttpClient;
use HughCube\GuzzleHttp\HttpClientTrait;
use HughCube\PUrl\HUrl;
use Illuminate\Support\Arr;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

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

                $auth = Arr::random($this->getKeys(), 1, false)[0];
                $key = $auth['key'] ?? null;
                $sk = $auth['sk'] ?? null;

                $uri = HUrl::instance($request->getUri())->withQueryValue('key', $key);

                /** 无需签名(白名单模式) */
                if (empty($sk)) {
                    return $handler($request->withUri($uri), $options);
                }

                /** 签名排序 */
                $queryArray = $uri->getQueryArray();
                ksort($queryArray, SORT_STRING);

                /** 签名query */
                $query = [];
                foreach ($queryArray as $name => $value) {
                    $query[] = "$name=$value";
                }
                $query = implode('&', $query);

                /** 组合签名 */
                $sign = $uri->getPath().($query ? sprintf('?%s', $query) : '').$sk;

                return $handler($request->withUri($uri->withQueryValue('sig', md5($sign))), $options);
            };
        };
    }

    public function request(string $method, $uri, array $options = []): ResponseInterface
    {
        return $this->getHttpClient()->requestLazy(strtoupper($method), $uri, $options);
    }

    /**
     * @see https://lbs.qq.com/service/webService/webServiceGuide/webServiceSuggestion
     *
     * @param  array  $query
     * @param  array  $options
     *
     * @return ResponseInterface
     */
    public function suggestion(array $query, array $options = []): ResponseInterface
    {
        $options[RequestOptions::QUERY] = $query;

        return $this->request('GET', '/ws/place/v1/suggestion', $options);
    }
}
