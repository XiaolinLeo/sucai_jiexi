<?php
namespace app\common\job;

use app\common\model\Attach;
use Http\RequestCore;
use Jobs\queue\Job;
use think\facade\Debug;

class Download
{

    public function fire(Job $job, $data)
    {
        $attach = Attach::where('status', '=', 0)->find();
        if (empty($attach)) {
            return true;
        }
        $local_file     = 'public/' . $attach->local_file;
        $attach->status = 1;
        $attach->save();
        Debug::remark('begin');
        $request = new RequestCore($attach->response_url);
        $request->set_useragent('Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36');
        $request->set_write_file($local_file);
        try {
            $request->send_request();
        } catch (\Exception $e) {
            $attach->queue_error = $e->getMessage();
            $attach->save();
            return true;
        }
        Debug::remark('end');
        $attach->queue_error   = '';
        $attach->download_time = Debug::getRangeTime('begin', 'end');
        if (is_file($local_file)) {
            $file = fopen($local_file, 'rb');
            $bin  = fread($file, 2);
            fclose($file);
            $strInfo  = @unpack('C2chars', $bin);
            $typeCode = intval($strInfo['chars1'] . $strInfo['chars2']);
            switch ($typeCode) {
                case 7790:
                    $fileType = 'exe';
                    break;
                case 7784:
                    $fileType = 'midi';
                    break;
                case 8297:
                    $fileType = 'rar';
                    break;
                case 255216:
                    $fileType = 'jpg';
                    break;
                case 7173:
                    $fileType = 'gif';
                    break;
                case 6677:
                    $fileType = 'bmp';
                    break;
                case 13780:
                    $fileType = 'png';
                    break;
                case 8075:
                    $fileType = 'zip';
                    break;
                case 4949:
                    $fileType = 'tar';
                    break;
                case 55122:
                    $fileType = '7z';
                    break;
                case 5666:
                    $fileType = 'psd';
                    break;
                default:
                    $response_url = $attach->response_url;
                    if (strpos($response_url, '?') !== false) {
                        $response_url = explode('?', $response_url);
                        $fileType     = pathinfo($response_url['0'], PATHINFO_EXTENSION);
                    } else {
                        $fileType = pathinfo($response_url, PATHINFO_EXTENSION);
                    }
                    if (empty($fileType)) {
                        $fileType = 'unknow';
                    }
            }
            $attach->savename = md5(request()->time() . random(10)) . '.' . $fileType;
            $attach->filename = md5(request()->time() . random(10)) . '.' . $fileType;
            $attach->filesize = filesize($local_file);
            $attach->status   = 2;
        }
        $attach->save();
    }

    public function failed($data)
    {
    }

}
