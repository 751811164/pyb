<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Bigotry <3162875@qq.com>                               |
// +---------------------------------------------------------------------+
// | Repository | https://gitee.com/Bigotry/OneBase                      |
// +---------------------------------------------------------------------+

namespace app\admin\controller;

/**
 * 控制器
 */
class SettingCoupon  extends AdminBase
{



    /**
     * 添加与编辑通用方法
     */
    public function common()
    {
        $where=[];
        if (!array_key_exists('id',$this->param))
        {
            $where["start_time"]=["<",time()];
            $where["end_time"]=[">",time()];
        }
        
        $list = $this->logicCoupon->getCouponList($where,'c.*,t.name type_name','',false);

        $this->assign('list', $list);
    }

}
