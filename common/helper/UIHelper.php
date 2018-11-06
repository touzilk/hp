<?php
/**
 * 定义业务处理公用方法，方法为静态方法
 * Created by PhpStorm.
 * author: wuhy
 * Date: 2018/11/02
 * Time: 15:57
 */


namespace common\helper;

use common\curl\Curl;
use RuntimeException;

class  UIHelper
{
    /**
     * 设置下载header解决文件名乱码问题
     * @author lvkui
     * @date 20160811
     * @param $filename
     */
    public static function setDownHeader($filename)
    {
        $ua = $_SERVER["HTTP_USER_AGENT"];
        $encoded_filename = urlencode($filename);
        $encoded_filename = str_replace("+", "%20", $encoded_filename);
        header('Content-Type: application/octet-stream');
        if (strpos($ua, 'MSIE') !== false || strpos($ua, 'rv:11.0')) {
            header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
        } else if (strpos($ua, 'Firefox') !== false) {
            header('Content-Disposition: attachment; filename*="utf8\'\'' . $filename . '"');
        } else {
            header('Content-Disposition: attachment; filename="' . $filename . '"');
        }

        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');
    }

    /**
     * 格式化路径
     * @param $path
     * @return mixed
     */
    public static function formate_path($path)
    {
        $file_path = str_replace('\\', DIRECTORY_SEPARATOR, $path);
        $file_path = str_replace('/', DIRECTORY_SEPARATOR, $file_path);
        return $file_path;
    }


    /**
     * 获取当前时间
     * @param string $formate
     * @return bool|string
     */
    public static function getCurrentDate($formate = 'Y-m-d H:i:s')
    {
        date_default_timezone_set('PRC');
        return date($formate);
    }

    /**
     * 格式化日期
     * @param string $date
     * @return bool|string
     */
    public static function getFormateDate($date, $formate = 'Y/m/d H:i:s')
    {
        date_default_timezone_set('PRC');
        return date($formate, strtotime($date));
    }

    /**
     * 格式化日期
     * @param string $date
     * @return bool|string
     */
    public static function getFormateTimeDate($date, $formate = 'Y/m/d H:i:s')
    {
        date_default_timezone_set('PRC');
        return date($formate, $date);
    }

    /**
     * 截取字符串
     * @param $strCut
     * @param $length
     * @param bool|true $suffix
     * @return string
     */
    public static function formateString($strCut, $length, $suffix = true)
    {

        if (!function_exists('mb_substr')) {
            return $strCut;
        }

        if (mb_strlen($strCut, 'utf8') > $length) {
            $strFormate = mb_substr($strCut, 0, $length, 'utf8');
            if ($suffix) {
                $strFormate .= '...';
            }
            return $strFormate;
        }
        return $strCut;
    }

    /**
     * 过滤html等内容
     * @param $strCut
     * @return mixed
     */

    public static function stripTags($strCut)
    {

        if (!$strCut) {
            return $strCut;
        }

        $strCut = strip_tags($strCut);

        return str_replace(["&nbsp;", "&amp;nbsp;", "\t", "\r\n", "\r", "\n"], ["", "", "", "", "", ""], $strCut);
    }

    /**
     * 截取字符串并清除html标签
     * @param $strCut
     * @param $length
     * @param bool $suffix
     * @return string
     */
    public static function subStringAndStripTags($strCut, $length, $suffix = true)
    {
        return UIHelper::formateString(UIHelper::stripTags($strCut), $length, $suffix);
    }


    /**
     * post提交数据
     * @param $url
     * @param array $post_data
     * @return string
     * @throws \ErrorException
     */
    public static function post($url, $post_data = [])
    {
        $ch = new Curl();
        $ch->setOpt(CURLOPT_TIMEOUT, 180);
        $result = $ch->post($url, $post_data);
        return $result;
    }

    /**
     * get请求
     * @param $url
     * @param array $post_data
     * @return string
     * @throws \ErrorException
     */
    public static function get($url, $post_data = [])
    {

        $ch = new Curl();
        $ch->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $ch->setOpt(CURLOPT_SSL_VERIFYHOST, false);
        $result = $ch->get($url, $post_data);
        return $result;
    }

    /**
     * 获取headers
     * @author beytagh
     * @param $url
     * @param int $timeout 超时
     * @return array|bool
     */
    public static function get_curl_headers($url, $timeout = 2)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        if (empty($data)) return false;
        $headers = explode("\n", $data);
        foreach ($headers as $key => $headerLine) {
            if (strlen($headerLine) > 1) {
                if (strpos($headerLine, ':') !== false) $headers[stristr($headerLine, ':', true)] = trim(stristr($headerLine, ':'), ': ');
            } else {
                unset($headers[$key]);
            }
        }
        return $headers;
    }

    /**
     * 文件下载
     * @param $fileName
     * @param $filePath
     */
    public static function downFile($fileName, $filePath)
    {
        try {

            $file_exists = true;
            if (strpos($filePath, 'http://') !== false) {
                if (!preg_match("/200/", @self::get_curl_headers($filePath)[0])) {
                    $file_exists = false;
                }
            } else {
                $file_exists = file_exists($filePath);
            }

            if (!$file_exists) {
                throw new \Exception("文件不存在");
            }

            $file = fopen($filePath, "r");
            self::setDownHeader($fileName);
            $buffer = 1024;

            while (!feof($file)) {
                $file_data = fread($file, $buffer);
                echo $file_data;
            }
            fclose($file);
            exit;

        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * 获取随机码
     * @author lvkui
     * @date 20161201
     * @param int $length 随机码长度
     * @param bool|true $lower 小写
     * @return string
     */
    public static function getRandom($length = 6, $lower = true)
    {
        $bytes = openssl_random_pseudo_bytes($length * 2);
        if ($bytes === false) {
            throw new RuntimeException('Unable to generate a random string');
        }

        $str_base64 = base64_encode($bytes);
        $string = substr(str_replace(['/', '+', '='], '', $str_base64), 0, $length);
        if ($lower === true) {
            return strtolower($string);
        }
        return $string;
    }

    /**
     * 随机获取密码
     * @author wuhy
     * @param $length
     * @return string
     */
    public static function getRandPwd($length = 6)
    {
        $chars = '0123456789';
        mt_srand((double)microtime() * 1000000 * getmypid());
        $password = "";
        while (strlen($password) < $length)
            $password .= substr($chars, (mt_rand() % strlen($chars)), 1);
        return $password;
    }

    /**
     * 计算两个时间戳相差的天时分秒
     * @author beytagh
     * @param $timestamp1
     * @param $timestamp2
     * @return array
     */
    public static function time_diff($timestamp1, $timestamp2)
    {
        if ($timestamp2 <= $timestamp1) {
            return ['days' => 0, 'hours' => 0, 'minutes' => 0, 'seconds' => 0];
        }

        $timediff = $timestamp2 - $timestamp1;
        $days = intval($timediff / 86400);

        // 时
        $remain = $timediff % 86400;
        $hours = intval($remain / 3600);

        //
        $remain = $timediff % 3600;
        $mins = intval($remain / 60);
        // 秒
        $secs = $remain % 60;

        $time = ['days' => $days, 'hours' => $hours, 'minutes' => $mins, 'seconds' => $secs];

        return $time;
    }


    /**
     * Ftp curl下载文件
     * @author beytagh
     * @date 20180706
     * @param $target_file
     * @param $ftp_user
     * @param $ftp_pwd
     */
    public static function down_ftp_file($target_file, $ftp_user, $ftp_pwd)
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $target_file);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_FTP_USE_EPSV, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT, 0);
        curl_setopt($curl, CURLOPT_USERPWD, "{$ftp_user}:{$ftp_pwd}");

        $url_path = explode('/', $target_file);
        $out_path = end($url_path);
        $outfile = fopen($out_path, 'w+');
        curl_setopt($curl, CURLOPT_FILE, $outfile);
        curl_exec($curl);
        fclose($outfile);
        $error_no = curl_errno($curl);
        curl_close($curl);
    }

    /**
     * @author beytagh
     * 生成数字唯一编号
     * @return string
     */
    public static function get_num_uuid()
    {
        $date = date('ymd');
        list($usec, $sec) = explode(" ", microtime());
        $time = substr($sec, -6) . substr($usec, 2);
        $salt = sprintf('%03d', mt_rand(1, 999));
        $uuid = $date . $time . $salt;
        return $uuid;
    }

    /**
     * @author beytagh
     * 生成15随机数[全库唯一]
     * @return string
     */
    public static function get_uuid()
    {
        $uuid = self::get_num_uuid();
        return base_convert($uuid, 10, 36);
    }

    /**
     * @param $array
     * @return array
     */
    public static function object_array($array)
    {
        if (is_object($array)) {
            $array = (array)$array;
        }
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $array[$key] = self::object_array($value);
            }
        }
        return $array;
    }
}

