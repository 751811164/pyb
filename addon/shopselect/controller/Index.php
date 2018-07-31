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

namespace addon\shopselect\controller;

use app\common\controller\AddonBase;

/**
 * 机构选择控制器
 */
class Index extends AddonBase
{
    
    /**
     * 获取选项信息
     */
    public function getOptions()
    {
        $list = $this->logicIndex->getShopList();
        $data = $this->logicIndex->combineOptions( $list, $default_option_text='请选择');

        return $this->result($data);
    }
    
}
