<?php
namespace app\admin\controller;

use app\admin\controller\Base;
use app\common\model\Card as RechargeCard;
use app\common\model\WebSite;
use think\Db;

class Card extends Base
{
    public function index()
    {
        $where                 = [];
        $this->view->card_list = RechargeCard::with('use_user,create_user')->where($where)->order('status desc,create_time desc')->paginate(50);
        $this->view->page      = $this->view->card_list->render();
        return $this->fetch();
    }

    public function create()
    {
        global $_G;
        if ($this->request->isPost()) {
            $post = $this->request->post();
            if (empty($post['rule'])) {
                return $this->error('请输入正确的卡密生成规则');
            }
            if (intval($post['numbers']) <= 0) {
                return $this->error('请输入正确的卡密张数');
            }

            $card_id = [];
            for ($i = 0; $i < $post['numbers']; $i++) {
                $card_id[$i] = replace_random_str($post['rule']);
            }
            foreach (RechargeCard::where('card_id', 'in', $card_id)->field('card_id')->select() as $old) {
                $k = array_search($old['card_id'], $card_id);
                unset($card_id[$k]);
            }
            foreach ($card_id as $k => $name) {
                RechargeCard::create([
                    'card_id'       => $name,
                    'create_uid'    => $_G['uid'],
                    'account_times' => $post['account_times'],
                    'valid_time'    => $post['valid_time'] == -1 ? -1 : $post['valid_time'] * 3600,
                    'access_times'  => $post['access_times'] ?: [],
                    'from'          => 'admin_create',
                    'status'        => 1,
                ]);
            }

            return $this->success('本次成功生成' . count($card_id) . '张卡密！', 'admin/card/index');
        }

        $this->view->site_list = WebSite::select();
        return $this->fetch();
    }

    public function export()
    {
        ob_start();
        $expCellName = [
            ['card_id', '卡号'],
            ['valid_time', '增加账户有效期'],
            ['access_times', '站点解析次数'],
            ['account_times', '账户总解析次数'],
            ['status', '是否可用'],
        ];
        $expTableData = RechargeCard::select()->toArray();
        if (empty($expTableData)) {
            return $this->error('暂无数据可导出');
        }
        $cellNum     = count($expCellName);
        $dataNum     = count($expTableData);
        $objPHPExcel = new \PHPExcel();
        $cellName    = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ'];
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $objPHPExcel->getActiveSheet(0)->mergeCells('A1:' . $cellName[$cellNum - 1] . '1');
        $objPHPExcel->getActiveSheet()->setCellValue('A1', '充值卡')->getStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        for ($i = 0; $i < $cellNum; $i++) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i] . '2', $expCellName[$i][1]);
        }
        for ($i = 0; $i < $dataNum; $i++) {
            for ($j = 0; $j < $cellNum; $j++) {
                switch ($expCellName[$j][0]) {
                    case 'access_times':
                        $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j] . ($i + 3), " " . json_encode($expTableData[$i][$expCellName[$j][0]]));
                        break;
                    case 'status':
                        $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j] . ($i + 3), " " . ($expTableData[$i][$expCellName[$j][0]] == 1 ? '可用' : '已失效'));
                        break;

                    default:
                        $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j] . ($i + 3), " " . $expTableData[$i][$expCellName[$j][0]]);
                        break;
                }
            }
        }
        ob_end_clean();
        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="充值卡.xls"');
        $fileName = '充值卡' . date('_YmdHis');
        header("Content-Disposition:attachment;filename=$fileName.xls");
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    public function delete($card_id = 0)
    {
        if (!$this->request->isAjax()) {
            return $this->error('请求类型错误');
        }
        $card = RechargeCard::where('card_id', '=', $card_id)->find();
        if (empty($card)) {
            return $this->error('指定数据不存在');
        }
        $card->delete();
        return $this->success('充值卡删除成功');
    }

    public function delete_all()
    {
        if (!$this->request->isAjax()) {
            return $this->error('请求类型错误');
        }
        Db::query("DELETE FROM card;");
        return $this->success('已清除所有充值卡');
    }

    public function delete_used()
    {
        if (!$this->request->isAjax()) {
            return $this->error('请求类型错误');
        }
        RechargeCard::where('status', '=', 0)->delete();
        return $this->success('已清除所有已使用充值卡');
    }
}
