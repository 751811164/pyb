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

namespace addon\categoryselect\controller;

use app\common\controller\AddonBase;

/**
 * 区域选择控制器
 */
class Index extends AddonBase
{
    
    /**
     * 获取选项信息
     */
    public function getOptions()
    {
        
        $where['pid']      = input('pid', DATA_DISABLE);
        $where['level']     = input('level', DATA_NORMAL);
        
        $select_id = input('select_id', DATA_DISABLE);
        
        $list = $this->logicIndex->getCategoryList($where);
        
        switch ($where['level'])
        {
            case 0: $default_option_text = "---请选择一级分类---"; break;
            case 1: $default_option_text = "---请选择二级分类---"; break;
            case 2: $default_option_text = "---请选择三级分类---"; break;
            default: $this->error('分类不存在');
        }
        
        $data = $this->logicIndex->combineOptions($select_id, $list, $default_option_text);
        
        return $this->result($data);
    }
    
}
