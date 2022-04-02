<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/4/15
 * Time: 8:42 下午.
 */

namespace HughCube\Laravel\Tencent\Map\Api\Actions;

use HughCube\Laravel\Tencent\Map\Api\TencentMapApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class SuggestionAction
{
    /**
     * @throws Throwable
     *
     * @return JsonResponse
     */
    public function action(): JsonResponse
    {
        $response = TencentMapApi::suggestion(array_filter($this->getRequest()->input()));

        $results = $response->toArray();
        if (!isset($results['status'])) {
            return new JsonResponse(['code' => 500, 'message' => '系统繁忙, 请稍后再试!']);
        }

        if (0 != $results['status']) {
            return new JsonResponse(['code' => $results['status'], 'message' => $results['message']]);
        }

        $list = [];
        foreach (($results['data'] ?: []) as $item) {
            $list[] = [
                'title'     => $item['title'],
                'address'   => $item['address'],
                'province'  => $item['province'],
                'city'      => $item['city'],
                'adcode'    => $item['adcode'],
                'type'      => $item['type'],
                'latitude'  => $item['location']['lat'],
                'longitude' => $item['location']['lng'],
            ];
        }

        return new JsonResponse([
            'code'    => 200,
            'message' => 'ok',
            'data'    => ['list' => array_values($list)],
        ]);
    }

    /**
     * @return Request
     */
    protected function getRequest(): Request
    {
        return request();
    }

    /**
     * @throws Throwable
     *
     * @return JsonResponse
     */
    public function __invoke(): JsonResponse
    {
        return $this->action();
    }
}
