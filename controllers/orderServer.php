<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/31
 * Time: 15:36
 */

namespace controllers;

use Hprose\ResultMode;
use Hprose\Socket\Server;
use services\say;

class orderServer
{



    function start()
    {
        $server = new Server("tcp://0.0.0.0:2628");
        $server->debug = true;
        $server->heartbeat = 3000;


        /*$server->onClose = function (\stdClass $context) {
            var_dump($context);
        };*/

        $server->onSendError = function ($error, \stdClass $context) {
            var_dump($error);
        };


        $server->addFunction([new say(),'hello'], '', [ResultMode::Normal]);

        $server->publish('time');

        $server->tick(1000, function () use ($server) {
            $server->push('time', microtime(true));
        });


        $server->start();
    }


}


