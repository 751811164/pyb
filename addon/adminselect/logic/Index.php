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

namespace addon\adminselect\logic;

use app\common\model\Addon;

/**
 * 机构岗位员工三级联动插件逻辑
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
            
            $data .= " value ='" . $vo['id'] . "'>" ."[{$vo['number']}]" . $vo['name'] . "</option>";
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
    public function getAdminList($where = [])
    {
        $where['a.'.DATA_STATUS_NAME] = DATA_NORMAL;
        $this->modelAdmin->alias('a');
        $join=[
            [SYS_DB_PREFIX.'auth_group_access ga','ga.admin_id=a.id']
        ];
        $list = $this->modelAdmin->getList($where, 'id,number,username name', 'id', false,$join);
        return $list;
    }
}
