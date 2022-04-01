<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/4/18
 * Time: 10:31 下午.
 */

namespace HughCube\Laravel\Tencent\Map\Api;

use Illuminate\Support\Facades\Facade as IlluminateFacade;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Package.
 *
 * @method static ResponseInterface request(string $method, $uri, array $options = [])
 * @method static ResponseInterface suggestion(array $query, array $options = [])
 *
 * @see \HughCube\Laravel\Tencent\Map\Api\Client
 * @see \HughCube\Laravel\Tencent\Map\Api\ServiceProvider
 */
class TencentMapApi extends IlluminateFacade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    public static function getFacadeAccessor(): string
    {
        return 'tencentmapapi';
    }
}
