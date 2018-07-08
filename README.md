# yii2 Swoole 扩展

扩展内容主要包括:
一.异步任务队列
这里根据不同的需求,我设计了三种不同的异步处理方法
```
方法一(可用http请求投递):
Yii::$app->swoole->webTask($data);
    将异步任务,以触发浏览器链接的方式执行,适用于能通过web请求来处理的小耗时任务
    优点:操作简单
    缺点:安全性低,任务链接要做权限处理;
        稳定性较差,如果链接中有脚本错误,或连接超时,会导致任务丢失.

方法二(用cli请求投递):
Yii::$app->swoole->cliAsync($data);
    以cli的形式来投递任务,适用场景较多的为计划任务,来通过yii2的console来执行,同样由mongo来传递和记录任务队列
```

二.websocket通信
本扩展以websocket为基础服务,所以可以处理websocket的请求,多客户端连接通信,通过自定义命令来实时处理业务

三.基于websocket的实时推送
服务端有消息变更时,通过向客户端推送消息,来达到消息的同步和实时反馈``
```
Yii::$app->swoole->pushMsg($fd,$data);
```

四.简单的启动/关闭/重启/状态获取命令


swoole版本要求：>=1.8.1

实现原理
------------

适用场景
------------
需要客户端触发的耗时请求，客户端无需等待返回结果
websocket的这种场景

安装
------------
```
composer require nolanchic/yii2-swoole "v1.0.3"
```

如何使用

安装前准备:
1.需要安装curl扩展,
```
composer require linslin/yii2-curl "1.1.3"
```
2.需要安装mongodb,因为有部分异步任务是需要存储到mongodb中


-----
1、修改common/config/params.php
```php
return [
    'swooleAsync' => [
        'host'             => 'ip', 		//服务启动IP
        'port'             => '9512',      		//服务启动端口
        'swoole_http'      => 'http://ip:9512',//推送触发连接地址
        'process_name'     => 'swooleWebSocket',		//服务进程名
        'open_tcp_nodelay' => '1',         		//启用open_tcp_nodelay
        'daemonize'        => false,				//守护进程化
        'heartbeat_idle_time' => 180,               //客户端向服务端请求的间隔时间,单位秒(s)
        'heartbeat_check_interval' => 120,          //服务端向客户端发送心跳包的间隔时间，两参数要配合使用,单位秒(s)
        'worker_num'       => '2',				//work进程数目
        'task_worker_num'  => '2',				//task进程的数量
        'task_max_request' => '10000',			//work进程最大处理的请求数
        'pidfile'           => Yii::getAlias('@swoole').'/yii2-swoole/yii2-swoole.pid',
        'log_dir'           => Yii::getAlias('@swoole').'/yii2-swoole',
        'task_tmpdir'       => Yii::getAlias('@swoole').'/yii2-swoole',
        'log_file'          => Yii::getAlias('@swoole').'/yii2-swoole/swoole.log',
        'log_size'          => 204800000,       //运行时日志 单个文件大小
    ]
];
```
2.上一步中,我把pidfile和log目录单独定义到了swoolelog目录下,如果你也采用相同的方法,你需要设置swoolelog别名，并创建swoolelog目录
修改common/bootstrap.php,添加如下内容:
```
Yii::setAlias('@swoole',dirname(dirname(__DIR__)) . '/swoolelog');
```

3、在console/config/main.php配置文件中增加controllerMap
```php
 'controllerMap' => [
        'swoole' => [
            'class' => 'nolanchic\swoole\console\SwooleController',
        ],
        //test主要用来测试
        'test' => [
            'class' => 'nolanchic\swoole\console\TestController',
        ],
    ],
```

4、在主配置文件中增加components
```php
'components' => [
     'swoole' => [
                 'class' => 'nolanchic\swoole\component\SwooleAsyncComponent',
             ]
]
```
5.在前端应用下（frontend/config/main.php）,添加modules，通过访问http://ip/swoole来测试
```
 'modules' => [
        'swoole' => [
            'class' => 'nolanchic\swoole\Module',
        ]
    ],
```

6、服务管理
```
//启动
php /path/to/yii/application/yii swoole start
 
//重启
php /path/to/yii/application/yii swoole restart

//停止
php /path/to/yii/application/yii swoole stop

//查看状态
php /path/to/yii/application/yii swoole status

//查看进程列表
php /path/to/yii/application/yii swoole list

```

7、测试
a.通过分别访问front/swoole/default/index|mongo|del|来测试异步任务
b.通过访问front/swoole/default/push来测试websocket推送,fd为1,客户端需要自己建立连接,
网页在线测试客户端链接地址:`http://www.blue-zero.com/WebSocket/`
c.通过执行
```
php /path/to/yii/application/yii test cli
```
来测试命令行的异步任务



