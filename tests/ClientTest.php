<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/4/20
 * Time: 11:45 下午.
 */

namespace HughCube\Laravel\Tencent\Map\Api\Tests;

use HughCube\Laravel\Tencent\Map\Api\Client;
use HughCube\Laravel\Tencent\Map\Api\TencentMapApi;
use Psr\Http\Message\ResponseInterface;

class ClientTest extends TestCase
{
    public function testFacade()
    {
        $this->assertInstanceOf(Client::class, TencentMapApi::getFacadeRoot());
    }

    public function testRequest()
    {
        $response = TencentMapApi::request('GET', '/ws/place/v1/suggestion');

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    public function testSuggestion()
    {
        $response = TencentMapApi::suggestion([]);

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }
}
