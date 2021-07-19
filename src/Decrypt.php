<?php
namespace DecryptAJS;

class Decrypt
{
    private static function getCookieStr($str)
    {
        $arr = self::getOriginalArr($str);
        $str = self::deUnicodeForArr($arr);
        $arr = self::get2ArrayString($str);
        $res = [];
        foreach ($arr as $value) {
            $res[] = self::getArrayFormString($value);
        }
        return self::deUnicodeForCookieForm2array($res);
    }

    /**
     * 从url获得的js加密代码中提取原始的数组
     * @param $str
     * @return mixed
     */
    private static function getOriginalArr($str)
    {
        $arr = [];
        preg_match('/var [a-z]{1,6}=\[[0-9,]{200,2000}\];/',$str,$arr);
        return self::getArrayFormString($arr[0]);
    }

    /**
     * 从原始数组中解码出带有两个数组和执行代码的字符串
     * @param array $arr
     * @return string
     */
    private static function deUnicodeForArr(array $arr)
    {
        $t = '';
        foreach ($arr as $value) {
            $t .= chr($value-1);
        }
        return $t;
    }

    /**
     * 从deUnicodeForArr获得的字符串中提取两个数组
     * @param $str
     * @return mixed
     */
    private static function get2ArrayString($str)
    {
        $arr = [];
        preg_match_all('/var [a-z]{4,6}=\[[0-9,]{10,200}\];/',$str,$arr);
        return $arr[0];
    }

    /**
     * 将字符串形式的js数组转成php数组
     * @param $str
     * @return mixed
     */
    private static function getArrayFormString($str) {
        $arr = [];
        preg_match_all('/[0-9]{1,4}/',$str,$arr);
        return $arr[0];
    }

    /**
     * 从两个数组中解码出cookie的密串
     * @param array $arr
     * @return string
     */
    private static function deUnicodeForCookieForm2array(array $arr)
    {
        $str = '';
        foreach ($arr[1] as $value) {
            $str .= chr($arr[0][$value]);
        }
        return $str;
    }
}