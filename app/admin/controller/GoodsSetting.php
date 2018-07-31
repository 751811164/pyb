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
 * 商品设置控制器
 */
class GoodsSetting extends AdminBase
{

    public function setting(){
        IS_POST&&$this->jump($this->logicGoodsSetting->setSeting($this->param));
        $info = $this->logicGoodsSetting->getSettingInfo();
        $this->assign('info',$info);
        return $this->fetch("setting");
    }

}
