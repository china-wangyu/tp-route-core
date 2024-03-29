<?php
/**
 * Created by User: wene<china_wangyu@aliyun.com> Date: 2019/7/1
 */

namespace WangYu;

use WangYu\exception\RouteException;
use think\facade\Route as Router;
use WangYu\lib\RouteApi;
use WangYu\lib\RouteReflex;
class Route extends Router
{
    public static $module;

    public static $middleware;

    /**
     * 注释路由模块注册
     * @param string $module
     * @param array $middleware
     * @throws RouteException
     */
    public static function reflex(string $module = 'api',$middleware = [])
    {
        try{
            static::$module = $module;
            static::$middleware = $middleware;
            $apis = RouteReflex::toReflex((new RouteApi(static::$module))->get());
            foreach ($apis as $api){
                static::setClassRoute($api);
            }
        }catch (\Exception $exception){
            throw new RouteException(['message'=>$exception->getMessage()]);
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
        // 设置类中间件
        if(!empty($clsMiddleware)){
            empty(static::$middleware) ?
                static::$middleware = $clsMiddleware :
                array_merge($clsMiddleware,static::$middleware);
        }

        // 设置方法中间件
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
        // 类路由和方法路由规则为空
        if (empty($classRule) and empty($actionRule)) return '';
        // 类路由不为空，方法路由为空
        if (!empty($classRule) and empty($actionRule)) return $classRule;
        // 类路由为空，方法路由不为空
        if (empty($classRule) and !empty($actionRule)) return $actionRule;
        // 都不为空的情况下，1.方法路由包含类路由规则
        if (strstr($actionRule,$classRule)) return $actionRule;
        // 都不为空的情况下，2.方法路由规则为根规则
        if (substr($actionRule,0,1) == '/') return $actionRule;
        // 都不为空的情况下，3. 拼接形成最后的规则
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