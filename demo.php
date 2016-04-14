<?php
	echo 'cdsacds赵伟康';
的市场的市场成都市成都

/**
 * Services are globally registered in this file
 *
 * @var \Phalcon\Config $config
 */
use Phalcon\Di\FactoryDefaul2t;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Util\Redis;
/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->setShared('url', function () use ($config) {
    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
});

/**
 * Setting up the view component
 */
$di->setShared('view', function () use ($config) {

    $view = new View();

    $view->setViewsDir($config->application->viewsDir);

    $view->registerEngines(array(
        '.volt' => function ($view, $di) use ($config) {

            $volt = new VoltEngine($view, $di);

            $volt->setOptions(array(
                'compiledPath' => $config->application->cacheDir,
                'compiledSeparator' => '_'
            ));

            return $volt;
        },
        '.phtml' => 'Phalcon\Mvc\View\Engine\Php'
    ));

    return $view;
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->set('dbMaster',function() use($config){
    $dbConfig = $config->db->master->toArray();

    $adapter = $dbConfig['adapter'];
    unset($dbConfig['adapter']);

    $class = 'Phalcon\Db\Adapter\Pdo\\' . $adapter;

    return new $class($dbConfig);
});

/**
 * 读取config的从数据库配置，随机在从服务器中选择
 */
$di->set('dbSlave',function() use($config){
    $slaveName ='slave'. (string)rand(1,$config->db->slavecount);
    $dbConfig = $config->db->$slaveName->toArray();

    $adapter = $dbConfig['adapter'];
    unset($dbConfig['adapter']);

    $class = 'Phalcon\Db\Adapter\Pdo\\' . $adapter;

    return new $class($dbConfig);
});


/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->setShared('modelsMetadata', function () {
    return new MetaDataAdapter();
});

/**
 * Start the session the first time some component request the session service
 */
$di->setShared('session', function () {
    $session = new SessionAdapter();
    $session->start();

    return $session;
});

/**
 * 注册配置文件
 */
$di->setShared('projectConfig',function(){
    $config =  include_once APP_PATH . '/app/config/projectConfig.php';

    return $config;
});
//sms配置
$di->setShared('smsConfig',function(){
    $config =  include_once APP_PATH . '/app/config/smsConfig.php';

    return $config;
});
//国政通配置
$di->setShared('gztConfig',function(){
    $config =  include_once APP_PATH . '/app/config/gztConfig.php';

    return $config;
});
//二维码配置
$di->setShared('qrcodeConfig',function(){
    $config =  include_once APP_PATH . '/app/config/qrcodeConfig.php';

    return $config;
});
$di->setShared('redisMap',function(){
    $config =  include_once APP_PATH . '/app/config/redisMapConfig.php';

    return $config;
});

/**
 * 注册redis服务
 */
$di->setShared('redis',function() use($config){
    if($config->redis->switch == 'on'){
        $redis = new Redis(array (
            'host'          => $config->redis->host,
            'port'          => $config->redis->port,
            'timeout'       => false,
            'persistent'    => false,
            'expire'        => 36000,
            'prefix'        => '',
            'length'        => '',
        ));
        return $redis;
    }
});


// 自定义路由
$di->set('router', function () {

    $router = new \Phalcon\Mvc\Router();

    //$router->setDefaultModule("webapp");

    $router->add('/:module/:controller/:action/:params', array(
        'module' => 1,
        'controller' => 2,
        'action' => 3,
        'params' => 4
    ));


    return $router;
});
>>>>>>> Stashed changes
