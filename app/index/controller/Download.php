<?php
namespace app\index\controller;

use app\common\model\Attach;
use app\common\model\MemberLog;
use app\common\model\WebSite;
use OSS\OssClient;
use think\Controller;

class Download extends Controller
{

    public function index($attach_id = 0)
    {
        global $_G;
        $attach = Attach::where('attach_id', '=', $attach_id)->find();
        if (!$attach || $attach['status'] < 2) {
            return $this->error('文件不存在');
        }
        if (empty($_G['uid'])) {
            return $this->redirect('index/account/login');
        }
        $site = WebSite::where('site_id', '=', $attach['site_id'])->find();

        if (empty($site)) {
            return $this->error('暂不支持该附件下载！');
        }

        $log = MemberLog::where('uid', '=', $_G['uid'])->where('site_id', '=', $site['site_id'])->where('parse_url', '=', $attach['parse_url'])->find();
        if (empty($log) || (!empty($log) && (($this->request->time() - $log['create_time']) >= 3600))) {
            if ($_G['member']['out_time'] > 0 && $_G['member']['out_time'] <= request()->time()) {
                return $this->error('您的账户已过期，请联系管理员！');
            }
            if ($_G['member']['parse_max_times'] < 0) {
                return $this->error('您的账户没有解析权限');
            }
            if ($_G['member']['parse_max_times'] > 0 && $_G['member']['parse_times'] >= $_G['member']['parse_max_times']) {
                return $this->error('您的账户解析次数已达上限，请充值');
            }

            if (empty($_G['member']['site_access'][$site['site_id']])) {
                return $this->error('您没有该网站的解析权限，请联系管理员或充值');
            }
            $access = $_G['member']['site_access'][$site['site_id']];
            if ($access['day'] < 0 || $access['all'] < 0) {
                return $this->error('您没有该网站的解析权限，请联系管理员或充值');
            }
            if ($access['day'] > 0 && $access['day_used'] >= $access['day']) {
                return $this->error('目标网站今日的解析次数已用完，试试其他网站吧');
            }
            if ($access['all'] > 0 && $access['max_used'] >= $access['all']) {
                return $this->error('目标站解析次数已达上限，请联系客服充值');
            }

            MemberLog::create([
                'uid'       => $_G['member']['uid'],
                'site_id'   => $site['site_id'],
                'times'     => 1,
                'status'    => 1,
                'parse_url' => $attach['request_url'],
            ]);
            $access['day_used'] = $access['day_used'] + 1;
            $access['max_used'] = $access['max_used'] + 1;
            $site_access        = $_G['member']->site_access;

            $site_access[$site['site_id']] = $access;
            $_G['member']->site_access     = $site_access;
            $_G['member']->parse_times     = $_G['member']->parse_times + 1;
            $_G['member']->save();
        }

        if ($_G['setting']['AccessKeyId'] && $_G['setting']['AccessKeySecret'] && $_G['setting']['Endpoint']) {
            $ossClient = new OssClient($_G['setting']['AccessKeyId'], $_G['setting']['AccessKeySecret'], $_G['setting']['Endpoint']);
            if ($_G['setting']['Bucket']) {
                $doesExist = $ossClient->doesObjectExist($_G['setting']['Bucket'], $attach['savename']) ? 'setting' : '';
            }
            if (empty($doesExist) && !empty($attach['bucket'])) {
                $doesExist = $ossClient->doesObjectExist($attach['bucket'], $attach['savename']) ? 'attach' : '';
            }
        }

        ob_end_clean();
        header("Content-type: application/octet-stream");
        header("Content-Transfer-Encoding: binary");
        header("Accept-Ranges: bytes");
        header("Content-Length: " . $attach['filesize']);
        header("Content-Disposition: attachment; filename=\"" . ($attach['filename'] ?: $attach['savename']) . "\"");
        if (is_file($attach['local_file'])) {
            $file = fopen($attach['local_file'], 'rb');
            while (!feof($file)) {
                echo fread($file, 102400);
            }
            fclose($file);
            exit;
        } else if ($doesExist) {
            $start_range = 0;
            while ($start_range < $attach['filesize']) {
                $end_range = $start_range + 102400;
                $end_range = $end_range >= $attach['filesize'] ? $attach['filesize'] - 1 : $end_range;
                echo $ossClient->getObject($doesExist == 'setting' ? $_G['setting']['Bucket'] : 'attach', $attach['savename'], [
                    'range' => $start_range . '-' . $end_range,
                ]);
                $start_range = $end_range + 1;
            }
            exit;
        }
    }
}
