<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/4/20
 * Time: 11:36 下午.
 */

namespace HughCube\Laravel\Tencent\Map\Api\Tests;

use HughCube\Laravel\Tencent\Map\Api\ServiceProvider as PackageServiceProvider;
use HughCube\Laravel\Tencent\Map\Api\TencentMapApi;
use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    /**
     * @param  Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        return [
            PackageServiceProvider::class,
        ];
    }

    /**
     * @param  Application  $app
     */
    protected function getEnvironmentSetUp($app)
    {
        /** @var Repository $appConfig */
        $appConfig = $app['config'];
        $appConfig->set(TencentMapApi::getFacadeAccessor(),
            (require dirname(__DIR__).'/config/config.php')
        );
    }
}
