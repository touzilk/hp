<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/31
 * Time: 15:36
 */

namespace controllers;

use Core;
use Hprose\ResultMode;
use Hprose\Socket\Server as HpServer;
use Medoo\Medoo;
use services\put;
use services\putConfig;

class server
{
    /**
     * @throws \Exception
     */
    function start()
    {


        $server = new HpServer("tcp://0.0.0.0:2628");
        $server->debug = true;

        $server->onAccept = function (\stdClass $context) {

            if (HP_DEBUG) {
                Core::$app->logger->addDebug('接受客户端连接', $context);
            }
        };

        $server->onClose = function (\stdClass $context) {

            if (HP_DEBUG) {
                Core::$app->logger->addDebug('连接关闭', $context);
                Core::$app->logger->addDebug('所有查询PDO', Core::$app->db->last());
                Core::$app->logger->addDebug('PDO错误', Core::$app->db->error());
                Core::$app->logger->addDebug('数据库信息', Core::$app->db->info());
                Core::$app->logger->addDebug('--', '-------------------------------------------------');
            }
        };

        $server->onSendError = function ($error, \stdClass $context) {

            if (HP_DEBUG) {
                if (is_string($error)) {
                    $error = [$error];
                }
                Core::$app->logger->addError('发送阶段发生错误', $error);
            }
        };

        $server->onBeforeInvoke = function ($name, &$args, $byref, \stdClass $context) {
            if (HP_DEBUG) {
                Core::$app->logger->addDebug('开始调用方法', [$name, $args]);
            }
        };

        $server->onAfterInvoke = function ($name, &$args, $byref, &$result, \stdClass $context) {

            if (HP_DEBUG) {
                Core::$app->logger->addDebug('调用方法结束', [$name, $args]);
            }
        };


        $server->addFunction([new putConfig(), 'config_list'], '', [ResultMode::Normal]);
        $server->addFunction([new put(), 'put_record'], '', [ResultMode::Normal]);
        $server->addFunction([new put(), 'check_exam'], '', [ResultMode::Normal]);


        $server->start();
    }
}


