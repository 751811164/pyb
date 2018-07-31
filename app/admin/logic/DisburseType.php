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
class DisburseType extends AdminBase
{
    
    /**
     * 获取商品分类列表
     */
    public function getDisburseTypeList($where = [], $field = true, $order = '', $paginate = 0)
    {
        
        return $this->modelDisburseType->getList($where, $field, $order, $paginate);
    }
    
    /**
     * 商品分类信息编辑
     */
    public function disburseTypeEdit($data = [])
    {
        
        $validate_result = $this->validateDisburseType->scene('edit')->check($data);
        
        if (!$validate_result) {
            
            return [RESULT_ERROR, $this->validateDisburseType->getError()];
        }
        
        $url = url('disburseTypeList');
        
        $result = $this->modelDisburseType->setInfo($data);
        
        $handle_text = empty($data['id']) ? '新增' : '编辑';
        
        $result && action_log($handle_text, '商品分类' . $handle_text . '，name：' . $data['name']);
        
        return $result ? [RESULT_SUCCESS, '操作成功', $url] : [RESULT_ERROR, $this->modelDisburseType->getError()];
    }

    /**
     * 获取商品分类信息
     */
    public function getDisburseTypeInfo($where = [], $field = true)
    {
        
        return $this->modelDisburseType->getInfo($where, $field);
    }
    
    /**
     * 商品分类删除
     */
    public function disburseTypeDel($where = [])
    {
        
        $result = $this->modelDisburseType->deleteInfo($where);
        
        $result && action_log('删除', '商品分类删除' . '，where：' . http_build_query($where));
        
        return $result ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, $this->modelDisburseType->getError()];
    }
}
