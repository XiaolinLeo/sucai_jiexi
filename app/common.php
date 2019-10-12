<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

\think\Console::addDefaultCommands([
    "\\Jobs\\queue\\command\\Work",
    "\\Jobs\\queue\\command\\Restart",
    "\\Jobs\\queue\\command\\Listen",
    "\\Jobs\\queue\\command\\Subscribe",
]);

if (!function_exists('authcode')) {
    function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0)
    {
        $ckey_length = 4;
        $key = md5($key != '' ? $key : config('app.authkey'));
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';

        $cryptkey = $keya . md5($keya . $keyc);
        $key_length = strlen($cryptkey);

        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
        $string_length = strlen($string);

        $result = '';
        $box = range(0, 255);

        $rndkey = array();
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }

        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }

        if ($operation == 'DECODE') {
            if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $keyc . str_replace('=', '', base64_encode($result));
        }

    }
}

if (!function_exists('create_dir')) {
    function create_dir($dir = '', $mode = 0777, $makeindex = true)
    {
        if (!empty($dir) && !is_dir($dir)) {
            create_dir(dirname($dir), $mode, $makeindex);
            @mkdir($dir, $mode);
            if (!empty($makeindex)) {
                @touch($dir . '/index.html');
                @chmod($dir . '/index.html', 0777);
            }
        }
        return true;
    }
}

if (!function_exists('delete_dir')) {
    function delete_dir($dir = '')
    {
        if (empty($dir)) {
            return false;
        }
        foreach (scandir($dir) as $entry) {
            if (in_array($entry, ['.', '..'])) {
                continue;
            }
            $filename = $dir . '/' . $entry;
            if (is_dir($filename)) {
                delete_dir($filename);
            } else {
                @unlink($filename);
            }
        }
        @rmdir($dir);
        return true;
    }
}

if (!function_exists('format_bytes')) {
    function format_bytes($size = 0)
    {
        $units = [' B', ' KB', ' MB', ' GB', ' TB'];
        for ($i = 0; $size >= 1024 && $i < 4; $i++) {
            $size /= 1024;
        }

        return round($size, 2) . $units[$i];
    }
}

if (!function_exists('replace_random_str')) {
    function replace_random_str($str = '')
    {

        $j_all = substr_count($str, '@');
        $k_all = substr_count($str, '#');
        $h_all = substr_count($str, '*');
        for ($j = 0; $j < $j_all; $j++) {
            $str = preg_replace_callback('/\@/', function () {
                $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $l = mt_rand(0, 25);
                return $str[$l];
            }, $str, 1);
        }
        for ($k = 0; $k < $k_all; $k++) {
            $str = preg_replace_callback('/\#/', function () {
                $str = '0123456789';
                $l = mt_rand(0, 9);
                return $str[$l];
            }, $str, 1);
        }
        for ($h = 0; $h < $h_all; $h++) {
            $str = preg_replace_callback('/\*/', function () {
                $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                $l = mt_rand(0, 35);
                return $str[$l];
            }, $str, 1);
        }
        return $str;
    }
}

if (!function_exists('random')) {
    function random($length = 10, $numeric = 0)
    {
        $seed = base_convert(md5(microtime() . $_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
        $seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
        if ($numeric) {
            $hash = '';
        } else {
            $hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
            $length--;
        }
        $max = strlen($seed) - 1;
        for ($i = 0; $i < $length; $i++) {
            $hash .= $seed{mt_rand(0, $max)};
        }
        return $hash;
    }
}

if (!function_exists('cutstr')) {
    function cutstr($string, $length = 80, $dot = ' ...')
    {
        if (strlen($string) <= $length) {
            return $string;
        }

        $pre = chr(1);
        $end = chr(1);
        $string = str_replace(['&amp;', '&quot;', '&lt;', '&gt;'], [$pre . '&' . $end, $pre . '"' . $end, $pre . '<' . $end, $pre . '>' . $end], $string);

        $strcut = '';
        $n = $tn = $noc = 0;
        while ($n < strlen($string)) {

            $t = ord($string[$n]);
            if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                $tn = 1;
                $n++;
                $noc++;
            } elseif (194 <= $t && $t <= 223) {
                $tn = 2;
                $n += 2;
                $noc += 2;
            } elseif (224 <= $t && $t <= 239) {
                $tn = 3;
                $n += 3;
                $noc += 2;
            } elseif (240 <= $t && $t <= 247) {
                $tn = 4;
                $n += 4;
                $noc += 2;
            } elseif (248 <= $t && $t <= 251) {
                $tn = 5;
                $n += 5;
                $noc += 2;
            } elseif ($t == 252 || $t == 253) {
                $tn = 6;
                $n += 6;
                $noc += 2;
            } else {
                $n++;
            }

            if ($noc >= $length) {
                break;
            }

        }
        if ($noc > $length) {
            $n -= $tn;
        }

        $strcut = substr($string, 0, $n);
        $strcut = str_replace([$pre . '&' . $end, $pre . '"' . $end, $pre . '<' . $end, $pre . '>' . $end], ['&amp;', '&quot;', '&lt;', '&gt;'], $strcut);

        $pos = strrpos($strcut, chr(1));
        if ($pos !== false) {
            $strcut = substr($strcut, 0, $pos);
        }
        return $strcut . $dot;
    }
}
function doCurlPost($url, $data, $headerSet = [], $timeoutConnect = 5, $timeoutRun = 180, $cookie = '')
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeoutConnect); //连接超时
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeoutRun); //执行任务超时
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge([
            'application/x-www-form-urlencoded',
            'Content-Length: ' . strlen($data),
        ], $headerSet)
    );
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.103 Safari/537.36");
    if (file_exists($cookie)) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
    }
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function doCurlGet($url, $timeout = 180, $timeOutConnect = 5)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeOutConnect); //连接超时
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout); //这个时间设长点
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 获取数据返回
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1); // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}
