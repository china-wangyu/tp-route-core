# china-wangyu/tp-route-core
ThinkPhp5.1 的反射路由类核心模块封装，含多种路由注册模式，路由及请求类型验证,基于`wangyu/reflex-core`扩展。本扩展为注释全家桶的核心路由模块。

* 反射路由模式
* 优化路由注册
* 反射参数验证
* 简洁
* 优秀

# `composer`安装说明

```bash
composer require wangyu/tp-route
```

# 使用说明

> `WangYu\Route` 继承于 `think\facade\Route`, 所以你可以使用 `\WangYu\Route` 调用 `think\facade\Route` 的方法

## 反射路由模式

### (1)使用`@route('路由','请求类型')`注册路由

- 函数参数说明

| 类型 | 模式 | 参数 | 说明 |
| --- | --- | --- | --- |
|类|route|rule| 路由前缀设置 |
|Class|route|'cms/user'|  |
|action|route|{'','get'}| 实际等于：{'cms/user/','get'} |
|action|route|{'/user/login','post'}| 实际等于：{'/user/login','post'} |

- 类`@route('规则')`路由注册

例如：

```php
/**
 * Class User
 * @route('cms/user')
 ......
 */
class User extends Controller{.....}
```

- 方法`@route('规则','请求类型')`路由注册

```php
/**
 * 账户登陆
 * @route('cms/user/login','post')
......
 */
public function login(Request $request){......}
```



## (2) 使用路由中间件

> 如果类中使用`middleware`和方法中使用`middleware`，定义的中间件类相同的数组参数，会通过`array_merge`去掉

| 类型 | 模式 | 参数 | 说明 |
| --- | --- | --- | --- |
|类|middleware|array| 路由中间件设置，请先在middleware.php设置好 |
|方法|middleware|array| 路由中间件设置，请先在middleware.php设置好 |
|Class|middleware|{'Auth','WYRouteParam'}| 相当于设置了'Auth','WYRouteParam'这两个中间件 |
|Action|middleware|{'Auth','WYRouteParam'}| 相当于设置了'Auth','WYRouteParam'这两个中间件 |

- 需要在系统`config`配置`middleware.php`

```php
<?php
return [
    // 默认中间件命名空间
    'default_namespace' => 'app\\http\\middleware\\',
    'Param' => WangYu\Param::Class
];
```

- 需要在接口类`注释`设置`@middleware`

```php
/**
 * Class User
 * @route('cms/user')
 * @middleware('Param','Auth')
 ......
 */
class User extends Controller{.....}
```

- 需要在接口方法`注释`设置`@middleware`

```php
/**
 * 账户登陆
 * @route('cms/user/login','post')
 * @middleware('Param','Auth')
......
 */
public function login(Request $request){......}
```

### （3）更改 `route.php` 文件注册路由方式

- 注册模块路由`\WangYu\Route::reflex('api');`

| 函数名 | 函数说明 | 函数参数 | 函数默认值 |
| :----: | :----: | :----: | :----: |
| \WangYu\Route | 路由模块，继承TP的\think\Route | [tp5.1官网文档]('https://www.kancloud.cn/manual/thinkphp5_1/353962') |
| \WangYu\Route::reflex() | 注释路由函数 | \WangYu\Route::reflex('API模块', 中间件数组) | \WangYu\Route::reflex('api', []) |


```php
// 引用路由类
use WangYu\Route;
// 注册模块路由
Route::reflex(); // 等于使用 Route::reflex('api',[]);
```

# 联系我们

- QQ: `354007048` 
- Email: `china_wangyu@aliyun.com`