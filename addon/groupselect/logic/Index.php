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

namespace addon\groupselect\logic;

use app\common\model\Addon;

/**
 * 类型机构岗位三级联动插件逻辑
 */
class Index extends Addon
{

    /**
     * 组合下拉框选项信息
     */
    public function combineOptions($id = 0, $list = [], $default_option_text = '')
    {
        
        $data = "<option value =''>$default_option_text</option>";
        
        foreach ($list as $vo)
        {
            $data .= "<option ";
            
            !($id == $vo['id']) ?: $data .= " selected ";
            
            $data .= " value ='" . $vo['id'] . "'>" . $vo['name'] . "</option>";
        }
        
        return $data;
    }
    

    /**
     * 获取列表
     */
    public function getShopList($where = [])
    {
        $where[DATA_STATUS_NAME] = DATA_NORMAL;
        $list = $this->modelShop->getList($where, true, 'id', false);
        return $list;
    }
    /**
     * 获取列表
     */
    public function getAuthGroupList($where = [])
    {
        $where[DATA_STATUS_NAME] = DATA_NORMAL;
        $list = $this->modelAuthGroup->getList($where, true, 'id', false);
        return $list;
    }

    /**
     * 获取列表
     */
    public function getTypeList($where = [])
    {
        $where[DATA_STATUS_NAME] = DATA_NORMAL;

        $list = $this->modelShopType->getList($where, true, 'id', false);
        return $list;
    }
}
