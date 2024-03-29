<?php
/**
 * Created by User: wene<china_wangyu@aliyun.com> Date: 2019/7/1
 */

namespace WangYu\lib;

use WangYu\exception\RouteException;

/**
 * Class Api 获取API模块下的接口文件
 * @package LinCmsTp\lib
 */
class RouteApi
{
    protected $module;

    /**
     * 初始化API模型，需要设置API模块
     * Api constructor.
     * @param string $module
     */
    public function __construct(string $module = 'api')
    {
        $this->module = $module;
    }

    /**
     * 获取模块下的API集合
     * @return array|null
     * @throws RouteException
     * @throws \LinCmsTp5\exception\BaseException
     */
    public function get():?array
    {
        try{
            return $this->getApi(env('APP_PATH').$this->module.'/controller');
        }catch (\Exception $exception){
            throw new RouteException(['message'=>$exception->getMessage()]);
        }
    }

    /**
     * 获取API列表
     * @param string $path
     * @return array|null
     * @throws RouteException
     * @throws \LinCmsTp5\exception\BaseException
     */
    protected function getApi(string $path):?array
    {
        $files = RouteTool::getDirFile($path);
        return $this->getApiAction($files);
    }

    /**
     * 获取API接口方法
     * @param array $files
     * @return array|null
     * @throws \Exception
     */
    protected function getApiAction(array $files):?array
    {
        try{
            $actions = $filename = [];
            if(empty($files) or !is_array($files))return $actions;
            foreach ($files as $key => $file) {
                $object = RouteTool::getClass($file);
                $action = RouteTool::getPhpAction($object);
                if(!empty($action)) $actions[RouteTool::getClassNamespace($object)] = $action;
            }
            return $actions;
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }




}