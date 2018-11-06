<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/11/2
 * Time: 13:53
 */

namespace services;


use Core;

class putConfig
{
    /**
     * 获取体检机构配置项列表
     * @param string $orgName
     * @return mixed
     */
    function config_list($orgName = "")
    {
        $result = Core::$app->getDb()->select("put_config", [
            "msicode",
            "piscode",
            "user_name",
            "org_name"
        ], [
            "user_name[!]" => "",
            "org_name[~]" => $orgName,
        ]);

        if (HP_DEBUG) {
            Core::$app->logger->addDebug('获取体检机构配置项列表', $result);
        }

        return $result;
    }

}