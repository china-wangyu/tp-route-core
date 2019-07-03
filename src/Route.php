<?php
/**
 * Created by User: wene<china_wangyu@aliyun.com> Date: 2019/7/1
 */

namespace WangYu;

use WangYu\exception\Exception;
use think\facade\Route as Router;
use WangYu\lib\Api;
use WangYu\lib\Reflex;
class Route extends Router
{
    public static $module;

    public static $middleware;

    public static function reflex(string $module = 'api',$middleware = [])
    {
        try{
            static::$module = $module;
            static::$middleware = $middleware;
            $apis = Reflex::toReflex((new Api(static::$module))->get());
            foreach ($apis as $api){
                static::setClassRoute($api);
            }
        }catch (\Exception $exception){
            throw new Exception(['message'=>$exception->getMessage()]);
        }
    }

    /**
     * 设置类路由
     * @param array $classRoute
     * @throws \Exception
     */
    public static function setClassRoute(array $classRoute):void
    {
        try{
            if(empty($classRoute)) return;
            foreach ($classRoute['actions'] as $key => $action){
                static::setMiddleware($classRoute['middleware'],$action['middleware']);
                static::setActionRoute(
                    static::getActionRule($classRoute['route'],$action['route']['rule']),
                    static::getActionRoute($classRoute['class'],$key),
                    $action['route']['method']);
            }
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * 设置中间件
     * @param array $clsMiddleware
     * @param array $funcMiddleware
     */
    public static function setMiddleware(array $clsMiddleware,array $funcMiddleware):void
    {
        if(!empty($clsMiddleware)){
            empty(static::$middleware) ?
                static::$middleware = $clsMiddleware :
                array_merge($clsMiddleware,static::$middleware);
        }

        if(!empty($funcMiddleware)){
            empty(static::$middleware) ?
                static::$middleware = $funcMiddleware :
                array_merge($funcMiddleware,static::$middleware);
        }
    }

    /**
     * 获取方法路由规则
     * @param string $classRule 类路由规则
     * @param string $actionRule 方法路由规则
     * @return string|null
     */
    public static function getActionRule(string $classRule, string $actionRule):string
    {

        if (empty($classRule) and empty($actionRule)) return '';
        if (empty($classRule) and !empty($actionRule)) return $actionRule;
        if (substr($actionRule,0,1) == '/') return $actionRule;
        if (strstr($actionRule,$classRule)) return $actionRule;
        return $classRule.'/'.$actionRule;
    }

    /**
     * 获取方法路由
     * @param string $class 类命名空间
     * @param string $action 方法名称
     * @return string
     */
    public static function getActionRoute(string $class,string $action):string
    {
        $route = str_replace(env('APP_NAMESPACE').'\\','',$class);
        $route = str_replace('controller\\','',$route);
        $route = explode('\\',$route);
        $route = $route[0].'/'.$route[1].(isset($route[2])?'.'.$route[2]:'');
        $route = $route.'/'.$action;
        return $route;
    }

    /**
     * 设置方法路由
     * @param string $rule
     * @param string $route
     * @param string $method
     */
    public static function setActionRoute(string $rule,string $route,string $method):void
    {
        Route::rule($rule,$route,$method)->middleware(static::$middleware)->allowCrossDomain();
    }
}