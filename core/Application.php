<?php

namespace core;

use core\Request;
use ReflectionClass;

class Application
{


    private $_request;

    private $_defaultRoute = 'index';

    private $_controllerNamespace = 'controllers';

    public static $classMap = [];

    /**
     * 构造函数
     * Application constructor.
     * @param array $config
     * @throws \Exception
     */
    public function __construct($config = [])
    {
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
        }

        if (isset($config['controllerNamespace'])) {
            $this->_controllerNamespace = trim($config['controllerNamespace']);
        }
    }

    /**
     * run
     * @return bool|int|mixed
     */
    public function run()
    {
        try {

            list ($route, $params) = $this->_request->resolve();

            $result =  $this->runAction($route, $params);
            var_dump($result);die;

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

        $obj = $this->getRelation($id);
        return call_user_func_array([$obj, $route], $params);

    }

    /**
     * 获取关联对象
     * @param $name
     * @return object
     * @throws \ReflectionException
     */
    public function getRelation($name)
    {
        $class = new  \ReflectionClass("controllers\\{$name}");
        return $class->newInstance();
    }

    /**
     * 自动加载
     * @param $className
     * @throws \Exception
     */
    public static function autoload($className){
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
