<?php
/**
 * Created by User: wene<china_wangyu@aliyun.com> Date: 2019/5/17 Time: 10:00
 */

namespace WangYu\exception;


class RouteException extends \WangYu\BaseException
{
    public $code = 400;
    public $message = '反射路由设置错误';
    public $error_code = 66668;
}