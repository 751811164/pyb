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
 * 商品品牌逻辑
 */
class Brand extends AdminBase
{
    
    /**
     * 获取商品品牌列表
     */
    public function getBrandList($where = [], $field = true, $order = '', $paginate = 0,$join=[])
    {
        $this->modelBrand->alias("b");
        if (empty($where["b.".DATA_STATUS_NAME]))
        {
            $where["b.".DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        }
        return $this->modelBrand->getList($where, $field, $order, $paginate,$join);
    }

    /**
     * 获取查询条件
     */
    public function getWhere($data){
        $where=[];
        !empty($data["search_data"])&&$where["name"] =["like","%".$data["search_data"]."%"];
//        !empty($data["category_id"])&&$where["category_id"] =$data["category_id"];
        return $where;
    }
    
    /**
     * 商品品牌信息编辑
     */
    public function brandEdit($data = [])
    {
        
        $validate_result = $this->validateBrand->scene('edit')->check($data);
        
        if (!$validate_result) {
            
            return [RESULT_ERROR, $this->validateBrand->getError()];
        }
        $url = url('brandList');
        
        $result = $this->modelBrand->setInfo($data);

        //region 不做关联处理了
        //        //保存分类--品牌关联
//        if (empty($data["id"]))
//        {
//            $brand_id=$this->modelBrand->getLastInsID();
//        }
//        else
//        {
//            $brand_id= $data["id"];
//        }
//
//        $items=[];
//        foreach ($data["category_id"] as $key=> $category_id)
//        {
//            $items[$key]["category_id"] =$category_id;
//            $items[$key]["brand_id"] =$brand_id;
//        }
//
//        $this->modelCategoryBrand->deleteInfo(["brand_id"=>$brand_id],true);
//        $this->modelCategoryBrand->setList($items,true);
        //endregion

        $handle_text = empty($data['id']) ? '新增' : '编辑';
        
        $result && action_log($handle_text, '商品品牌' . $handle_text . '，name：' . $data['name']);
        
        return $result ? [RESULT_SUCCESS, '操作成功', $url] : [RESULT_ERROR, $this->modelBrand->getError()];
    }

    /**
     * 获取商品品牌信息
     */
    public function getBrandInfo($where = [], $field = true)
    {
        
        return $this->modelBrand->getInfo($where, $field);
    }
    
    /**
     * 商品品牌删除
     */
    public function brandDel($where = [])
    {
        
        $result = $this->modelBrand->deleteInfo($where);
        
        $result && action_log('删除', '商品品牌删除' . '，where：' . http_build_query($where));
        
        return $result ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, $this->modelBrand->getError()];
    }
}
