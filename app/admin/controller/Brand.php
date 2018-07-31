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
 * 商品品牌控制器
 */
class Brand extends AdminBase
{
    /**
     * 商品品牌列表
     */
    public function brandList()
    {
        $where=$this->logicBrand->getWhere($this->param);
        $this->assign('list', $this->logicBrand->getBrandList($where));
//echo model("brand")->getLastSql();
        return $this->fetch('brand_list');
    }



    /**
     * 商品品牌列表
     */
    public function brandListByCategory()
    {
        !empty($this->param["category_id"])&&$where["category_id"]=$this->param["category_id"];
        $join=[
            [SYS_DB_PREFIX.'category_brand cb', 'cb.brand_id = b.id ', 'LEFT'],
        ];
         $list = $this->logicBrand->getBrandList($where,"b.id,b.name","",false,$join);

        return $list;
    }

    /**
     * 商品品牌添加
     */
    public function brandAdd()
    {

        $this->brandCommon();
        return $this->fetch('edit_modal');
    }
    
    /**
     * 商品品牌编辑
     */
    public function brandEdit()
    {
        $this->brandCommon();
        
        $info = $this->logicBrand->getBrandInfo(['id' => $this->param['id']]);
        $this->assign('info', $info);
        $checkList = $this->logicCategoryBrand->getItemColumn(['brand_id'=>$this->param['id']], 'category_id');
        $this->assign('checkList', join(",",$checkList) );
        return $this->fetch('edit_modal');
    }

    /**
     * @throws \Exception
     */
    public function brandCommon()
    {
        IS_POST && $this->jump($this->logicBrand->brandEdit($this->param));
//        $pids        = $this->logicCategory->getCategoryColumn([DATA_STATUS_NAME => DATA_NORMAL], "pid");
//        $where["id"] = ["not in", array_unique($pids)];
        $where_category["level"] =config('logic_config.category_level') ;
        $this->assign('categoryList', $categoryList = $this->logicCategory->getCategoryList($where_category, true, '', false));

    }
    

    /**
     * 商品品牌删除
     */
    public function brandDel($id = 0)
    {
        
        $this->jump($this->logicBrand->brandDel(['id' => $id]));
    }


    /**
     * 数据状态设置
     */
    public function setStatus()
    {

        $this->jump($this->logicAdminBase->setStatus('Brand', $this->param));
    }


    /**
     * 排序
     */
    public function setSort()
    {

        $this->jump($this->logicAdminBase->setSort('Brand', $this->param));
    }



}
