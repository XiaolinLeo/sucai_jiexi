<?php
namespace app\common\job;

use app\common\model\Member;
use Jobs\queue\Job;

class ResetTimes
{

    public function fire(Job $job, $data)
    {
        $day   = date('Ymd');
        $query = Member::where('reset_times', '<', $day)->limit(50)->select();
        if (!$query->isEmpty()) {
            foreach ($query as $member) {
                $member->reset_times();
            }
        }
    }

    public function failed($data)
    {
    }

}
