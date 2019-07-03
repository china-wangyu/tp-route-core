<?php
/**
 * Created by User: wene<china_wangyu@aliyun.com> Date: 2019/5/17 Time: 10:00
 */

namespace WangYu\lib;


use WangYu\exception\Exception;

class Reflex extends \WangYu\Reflex
{
    /**
     * 获取模块API反射文档
     * @param array $api
     * @return array|null
     * @throws Exception
     * @throws \LinCmsTp5\exception\BaseException
     */
    public static function toReflex(array $api):?array
    {
        try{
            $result = $action = $doc = $cls = [];
            if (empty($api) or !is_array($api)) return [];
            foreach ($api as $key => $actions){
                $doc = static::getApiClass(new $key());
                $action = static::getApiActions(new $key(),$actions);
                if(empty($action)) continue;
                $result[$key] = array_merge(['class'=>$key,'actions'=>$action],$doc);
            }
            return $result;
        }catch (\Exception $exception){
            throw new Exception(['message'=>$exception->getMessage()]);
        }
    }

    /**
     * 获取类文档注释
     * @param $object
     * @return array|string
     * @throws Exception
     * @throws \LinCmsTp5\exception\BaseException
     */
    public static function getApiClass($object)
    {
        try{
            $result = '';
            if (is_object($object)){
                $reflex = new static($object);
                ($reflex)->reflex->getDocComment();
                $doc = $reflex->get('doc',['doc']);
                $route = $reflex->get('route',['rule']);
                $middleware = $reflex->get('middleware',[]);
                $result = [
                    'doc' => $doc[0]['doc'] ?? basename(get_class($object)),
                    'route' => $route[0]['rule'] ?? '',
                    'middleware' => $middleware[0] ?? []
                ];
            }
            return $result;
        }catch (\Exception $exception){
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * 获取API方法内容
     * @param $object
     * @param array $actions
     * @return array
     * @throws Exception
     * @throws \LinCmsTp5\exception\BaseException
     */
    public static function getApiActions($object,array $actions = []):array
    {
        try{
            $result = [];
            if (is_object($object)){
                foreach ($actions as $key => $item){
                    $Reflex = new static($object,$item);
                    $route = $Reflex->get('route', ['rule', 'method']);
                    $middleware = $Reflex->get('middleware', []);
                    if(empty($route) and empty($middleware)) continue;
                    $result[$item] = [
                        'route'=> $route[0] ??
                            [
                                'rule'=> basename(dirname(dirname(get_class($object)))).'/'.
                                    basename(dirname(get_class($object))).'/'.
                                    basename(get_class($object)),
                                'method'=>strtolower($_SERVER['REQUEST_METHOD'])
                            ],
                        'middleware' => $middleware[0] ?? []
                    ];
                }
            }
            return $result;
        }catch (\Exception $exception){
            throw new Exception($exception->getMessage());
        }
    }
}