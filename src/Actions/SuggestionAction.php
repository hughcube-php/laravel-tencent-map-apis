<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/4/15
 * Time: 8:42 下午.
 */

namespace HughCube\Laravel\Tencent\Map\Api\Actions;

use HughCube\Laravel\Tencent\Map\Api\Exceptions\BusinessException;
use HughCube\Laravel\Tencent\Map\Api\TencentMapApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class SuggestionAction
{
    /**
     * @return JsonResponse
     *
     * @throws Throwable
     */
    public function action(): JsonResponse
    {
        $response = TencentMapApi::suggestion(array_filter($this->getRequest()->input()));

        $results = $response->toArray();
        if (!isset($results['status'])) {
            throw new BusinessException($response);
        }

        if (0 != $results['status']) {
            throw new BusinessException($response, $results['status'], $results['message']);
        }

        $list = [];
        foreach (($results['data'] ?: []) as $item) {
            $list[] = [
                'title' => $item['title'],
                'address' => $item['address'],
                'province' => $item['province'],
                'city' => $item['city'],
                'adcode' => $item['adcode'],
                'type' => $item['type'],
                'latitude' => $item['location']['lat'],
                'longitude' => $item['location']['lng'],
            ];
        }

        return new JsonResponse([
            'code' => 200,
            'message' => 'ok',
            'data' => array_values($list),
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
     * @return JsonResponse
     *
     * @throws Throwable
     */
    public function __invoke(): JsonResponse
    {
        return $this->action();
    }
}
