<?php
namespace app\common\job;

use app\common\model\Attach;
use app\common\model\Setting;
use app\common\model\WebSite;
use Jobs\queue\Job;
use OSS\Core\OssException;
use OSS\OssClient;
use think\facade\Debug;

class Upload
{

    public function fire(Job $job, $data)
    {
        $setting = [];
        foreach (Setting::select() as $value) {
            $setting[$value['key']] = $value['value'];
        }
        if (!$setting['AccessKeyId'] || !$setting['AccessKeySecret'] || !$setting['Endpoint']) {
            return true;
        }
        $attach = Attach::where('status', '=', 2)->find();
        if (empty($attach)) {
            return true;
        }
        $site = WebSite::where('site_id', '=', $attach['site_id'])->find();
        if (!$setting['Bucket'] && !$site['bucket']) {
            return true;
        }
        $local_file = 'public/' . $attach['local_file'];
        if (!is_file($local_file)) {
            return true;
        }
        $attach->status = 3;
        $attach->save();
        Debug::remark('begin');

        try {
            $ossClient = new OssClient($setting['AccessKeyId'], $setting['AccessKeySecret'], $setting['Endpoint']);
            $ossClient->uploadFile($site['bucket'] ?: $setting['Bucket'], $attach['savename'], $local_file);
        } catch (OssException $e) {
            $error = $e->getMessage();
        }
        Debug::remark('end');
        if (!empty($error)) {
            $attach->queue_error = $error;
            $attach->save();
            return true;
        }
        @unlink($local_file);
        if (!is_file($local_file)) {
            $attach->local_file = '';
        }
        $attach->queue_error = '';
        $attach->bucket      = $site['bucket'] ?: $setting['Bucket'];
        $attach->upload_time = Debug::getRangeTime('begin', 'end');
        $attach->status      = 4;
        $attach->save();
    }

    public function failed($data)
    {
    }

}
