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
class SettingCategory  extends AdminBase
{



    /**
     * 添加与编辑通用方法
     */
    public function common()
    {
//        $list = $this->logicCategory->getCategoryList([],true,'sort',false);
//        $list= $this->logicCategory->makeTreeForHtml($list);
//        $this->assign('list', $list);
        //分类
//        $pids        = $this->logicCategory->getCategoryColumn([DATA_STATUS_NAME => DATA_NORMAL], "pid");
//        $where["id"] = ["not in", array_unique($pids)];
        $where_category["level"] =config('logic_config.category_level') ;
        $this->assign('categoryList', $categoryList = $this->logicCategory->getCategoryList($where_category, true, '', false));

    }




}
