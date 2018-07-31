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
 * 商品分类控制器
 */
class Category extends AdminBase
{
    /**
     * 商品分类列表
     */
    public function categoryList()
    {
        $list =collection($this->logicCategory->getCategoryList([],"id,pid,name as text, false as selectable",'',false))->toArray();

        $list= $this->logicCategory->makeTreeForTreeView($list);
        $this->assign('list', $list);
        return $this->fetch('category_list');
    }


    /**
     * 商品分类添加
     */
    public function categoryAdd()
    {
        IS_POST && $this->jump($this->logicCategory->categoryEdit($this->param));
//        $this->categoryCommon();
        return $this->fetch('edit_modal');
    }
    
    /**
     * 商品分类编辑
     */
    public function categoryEdit()
    {
        IS_POST && $this->jump($this->logicCategory->categoryEdit($this->param));
//        $this->categoryCommon();
        $info = $this->logicCategory->getCategoryInfo(['id' => $this->param['id'],DATA_STATUS_NAME=>DATA_NORMAL]);
        $this->assign('info', $info);
        return $this->fetch('edit_modal');
    }

    /**
     * 分类添加和编辑通用方法
     * 生成下拉分类
     */
    public function categoryCommon(){
        IS_POST && $this->jump($this->logicCategory->categoryEdit($this->param));

        $where["id"]=["neq", $this->param['id']];
        $list =collection($this->logicCategory->getCategoryList($where,true,'sort',false))->toArray();
        $list = $this->logicCategory->makeTree($list);
        $list = $this->logicCategory->categoryToSelect($list);
        $this->assign('category_select', $list);
    }
    

    /**
     * 商品分类删除
     */
    public function categoryDel($id = 0)
    {
        
        $this->jump($this->logicCategory->categoryDel(['id' => $id]));
    }


    /**
     * 数据状态设置
     */
    public function setStatus()
    {

        $this->jump($this->logicAdminBase->setStatus('Category', $this->param));
    }

    /**
     * 创建编号
     *
     */
    public function createCode(){

        return $this->logicCategory->createCode();
    }
    /**
     * 排序
     */
    public function setSort()
    {

        $this->jump($this->logicAdminBase->setSort('Category', $this->param));
    }

}
