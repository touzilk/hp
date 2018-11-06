<?php

namespace core;

use Core;
use core\Request;

/**
 * Class Application
 * @property \Medoo\Medoo $db The database connection. This property is read-only.
 * @property \Monolog\Logger $logger The database connection. This property is read-only.
 * @package core
 */
class Application
{


    private $_request;

    private $_defaultRoute = 'index';

    private $_controllerNamespace = 'controllers';

    public static $classMap = [];

    private $_components = [];


    /**
     * 构造函数
     * Application constructor.
     * @param array $config
     * @throws \Exception
     */
    public function __construct($config = [])
    {
        Core::$app = $this;
        $this->_request = new Request();
        $config = $this->loadConfig($config);
        $this->preInit($config);

    }

    /**
     * 加载配置项
     * @param $config
     * @return mixed
     */
    protected function loadConfig($config)
    {
        return $config;
    }

    /**
     * 验证配置项
     * @param $config
     * @throws \Exception
     */
    public function preInit(&$config)
    {
        if (!isset($config['id'])) {
            throw new \Exception('The "id" configuration for the Application is required.');
        } else {
            Core::$app->id = trim($config['id']);
        }

        if (isset($config['controllerNamespace'])) {
            $this->_controllerNamespace = trim($config['controllerNamespace']);
        }

        if (isset($config['basePath'])) {
            Core::$app->basePath = trim($config['basePath']);
        }

        if (isset($config['db'])) {

            if (is_array($config['db'])) {
                $db = $config['db'];
                if (!isset($db['class'])) {
                    throw new  \ErrorException('db 必须包含 class属性');
                }
                Core::$app->db = new  $db['class']($db);
                $this->_components['db'] = $config['db'];
            }

        }

        if (isset($config['logger'])) {

            if (is_array($config['logger'])) {
                $logger = $config['logger'];
                if (!isset($logger['class'])) {
                    throw new  \ErrorException('logger 必须包含 class属性');
                }
                Core::$app->logger = new  $logger['class']('app');
                Core::$app->logger->pushHandler(new $logger['handler'](__DIR__ . '/logs/app.log'));
            }

        }

        if (isset($config['params'])) {

            if (is_array($config['params'])) {
                Core::$app->params = $config['params'];
            } else {
                Core::$app->params = [];
            }

        }

    }

    /**
     * db
     * @return null
     */
    public function getDb()
    {
        if (isset($this->_components['db'])) {
            $db = $this->_components['db'];
            return Core::$app->db = new  $db['class']($db);
        }

        return null;
    }


    /**
     * run
     * @return bool|int|mixed
     */
    public function run()
    {
        try {

            list ($route, $params) = $this->_request->resolve();

            $result = $this->runAction($route, $params);

            /*
             * web 方式解析
             ob_start();
             ob_implicit_flush(false);
             extract($result, EXTR_OVERWRITE);
             $_file_ = '';
             require($_file_);
            */

        } catch (\Exception $e) {
            return $e->getCode();
        }
    }

    /**
     * runAction
     * @param $route
     * @param $params
     * @return bool|mixed
     * @throws \ReflectionException
     */
    public function runAction($route, $params)
    {
        if ($route === '') {
            $route = $this->_defaultRoute;
        }

        $route = trim($route, '/');
        if (strpos($route, '//') !== false) {
            return false;
        }

        if (strpos($route, '/') !== false) {

            list ($id, $route) = explode('/', $route, 2);
        } else {
            $id = $route;
            $route = '';
        }

        $className = "{$this->_controllerNamespace}\\{$id}";
        $obj = new $className();
        return call_user_func_array([$obj, $route], $params);

    }

    /**
     * 自动加载
     * @param $className
     * @throws \Exception
     */
    public static function autoload($className)
    {
        if (isset(self::$classMap[$className])) {
            return;
        }

        $classFile = PATH . DIRECTORY_SEPARATOR . $className . '.php';
        $classFile = str_replace('\\', DIRECTORY_SEPARATOR, $classFile);
        $classFile = str_replace('/', DIRECTORY_SEPARATOR, $classFile);

        if ($classFile === false || !is_file($classFile)) {
            return;
        }

        if (include($classFile)) {

            $class_map[$className] = $classFile;
        }

        if (!class_exists($className, false) && !interface_exists($className, false) && !trait_exists($className, false)) {
            throw new \Exception("Unable to find '$className' in file: $classFile. Namespace missing?");
        }
    }
}
