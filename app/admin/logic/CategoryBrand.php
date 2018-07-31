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
 * 商品分类逻辑
 */
class CategoryBrand extends AdminBase
{
    /**
     * 获取列表
     */
    public function getItemList($where = [], $field = true, $order = '', $paginate = 0)
    {
        $where[DATA_STATUS_NAME] = DATA_NORMAL;
        $list = $this->modelCategoryBrand->getList($where, $field, $order, $paginate);

        return $list;
    }

    public function getItemColumn($where = [], $field = '', $key = ''){

        return $this->modelCategoryBrand->getColumn($where, $field,$key);
    }


}
