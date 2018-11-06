<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/11/2
 * Time: 13:53
 */

namespace services;

use common\helper\UIHelper;
use Core;
use FangStarNet\PHPValidator\Validator;

class put
{
    /**
     * 检测体检号是否被使用
     * @param $exam
     * @return array
     */
    function check_exam($exam){

        $exist = Core::$app->getDb()->has('medical_record',[
                'exam_num' => (string)$exam,
                'is_delete' => 0,
                'type' => 1
        ]);

        if (HP_DEBUG) {
            Core::$app->logger->addDebug('检测体检号是否被使用', [$exist]);
        }

        if($exist) {
            return ['code' => 0, 'message' => '已存在该会诊记录'];
        }

        return ['code' => 500, 'message' => '新会诊记录'];
    }


    /**
     * 推送会诊信息到Holter平台
     * @param $data
     * @return array
     * @throws \ErrorException
     */
    function put_record($data)
    {
        $data = UIHelper::object_array($data);
        Validator::make($data, [
            "name" => "present|length_max:10",
            "exam" => "present|num|length_between:5,20",
            "age" => "present|num|length_max:3",
            "gender" => "present|num|in:1,2",
            "miscode" => "present|num",
        ], [
            "name.present" => "请输入姓名",
            "name.length_max" => "姓名最多为10位",
            "exam.present" => "请输入体检号",
            "exam.num" => "体检号必须为数字",
            "exam.length_between" => "体检号必须为5位至20位",
            "age.present" => "请输入年龄",
            "age.num" => "年龄必须为数字",
            "age.length_max" => "年龄最多为3位",
            "gender.present" => "请输入性别",
            "gender.num" => "性别必须为数字",
            "gender.in" => "性别值为1或2",
            "miscode.present" => "请输入机构标识",
            "miscode.num" => "机构标识必须为数字",
        ]);

        if (Validator::has_fails()) { //Validator::has_fails()
            return ['code' => 500, 'message' => Validator::error_msg()]; // 校验不通过，打印提示信息(默认使用语言包中的文案)
        }

        $org = Core::$app->getDb()->get("put_config", [
            "msicode",
            "piscode",
            "user_name",
            "org_name"
        ], [
            "msicode" => $data['miscode'],
            "user_name[!]" => "",
        ]);

        $postData = [[
            'org_code' => $data['miscode'],
            'org_name' => $org['org_name'],
            'exam' => $data['exam'],
            'name' => $data['name'],
            'gender' => $data['gender'] == 1 ? "男" : '女',
            'age' => $data['age'],
            'project' => "",
            'tel' => "",
            'dept' => "",
        ]];

        //推送会诊信息
        $putUrl = Core::$app->params['put_url'];
        $result = UIHelper::post(rtrim($putUrl, '/') . '/ikang/put/exam-info', [
            'client_id' => 'ikang',
            'content' => json_encode($postData)
        ]);

        if (HP_DEBUG) {
            Core::$app->logger->addDebug('推送会诊信息到Holter平台', [$result]);
        }

        return ['code' => $result->code, 'message' => $result->message];
    }
}