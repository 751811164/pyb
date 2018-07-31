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
class ArrangeDate extends AdminBase
{

    public function dateInfo(){
        $info = $this->logicArrangeDate->getDateInfo($this->param);
        return $info;

//        return $this->assign('info', $this->logicArrangeDate->getDateInfo($this->param));
//        IS_AJAX && $this->jump($this->logicAuthGroup->fastSet($this->param));
//
//        return $this->fetch('arrange_list');
    }


}
