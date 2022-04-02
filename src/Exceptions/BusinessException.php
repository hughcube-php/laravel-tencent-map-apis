<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2022/4/2
 * Time: 14:43
 */

namespace HughCube\Laravel\Tencent\Map\Api\Exceptions;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class BusinessException extends Exception
{
    protected $response;

    public function __construct(ResponseInterface $response, $message = "", $code = 0, Throwable $previous = null)
    {
        $this->response = $response;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
