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

namespace app\admin\logic;

/**
 * 逻辑
 */
class SalaryType extends AdminBase
{
    
    /**
     * 获取店铺类型列表
     */
    public function getTypeList($where = [], $field = true, $order = '', $paginate = 0,$join = [], $group = '', $limit = null)
    {
        
        return $this->modelSalaryType->getList($where, $field, $order, $paginate,$join ,$group, $limit);
    }


    public function getTypeInfo($where=[],$field=true){
        return $this->modelSalaryType->getInfo($where, $field);
    }





}
