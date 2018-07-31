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

namespace addon\groupselect\controller;

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

        $upid = input('upid', DATA_DISABLE);
        $type = input('type', 'type');

        $select_id = input('select_id', DATA_DISABLE);
        $where     = [];

        switch ($type)
        {
            case 'type':
                $default_option_text = "---请选择机构类型---";
                $list                = $this->logicIndex->getTypeList($where);
                break;
            case 'shop':
                $default_option_text = "---请选择机构---";
                $where['type_id']    = $upid;
                $list                = $this->logicIndex->getShopList($where);
                break;
            case 'group':
                $default_option_text = "---请选择岗位---";
                $where['shop_id']    = $upid;
                $list                = $this->logicIndex->getAuthGroupList($where);
                break;
            default:
                $this->error('信息有误');
        }


        $data = $this->logicIndex->combineOptions($select_id, $list, $default_option_text);
        return $this->result($data);
    }

}
