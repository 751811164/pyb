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
 * 店铺类型逻辑
 */
class ShopType extends AdminBase
{
    
    /**
     * 获取店铺类型列表
     */
    public function getTypeList($where = [], $field = true, $order = '', $paginate = 0,$join = [], $group = '', $limit = null)
    {
        if (empty($where[DATA_STATUS_NAME]))
        {
            $where[DATA_STATUS_NAME] = DATA_NORMAL;
        }
        return $this->modelShopType->getList($where, $field, $order, $paginate,$join ,$group, $limit);
    }


    
    /**
     * 店铺类型信息编辑
     */
    public function typeEdit($data = [])
    {
        
        $validate_result = $this->validateShopType->scene('edit')->check($data);
        
        if (!$validate_result) {
            
            return [RESULT_ERROR, $this->validateShopType->getError()];
        }
        
        $url = url('typeList');
        
        $result = $this->modelShopType->setInfo($data);
        
        $handle_text = empty($data['id']) ? '新增' : '编辑';
        
        $result && action_log($handle_text, '店铺类型' . $handle_text . '，name：' . $data['name']);
        
        return $result ? [RESULT_SUCCESS, '操作成功', $url] : [RESULT_ERROR, $this->modelShopType->getError()];
    }

    /**
     * 获取店铺类型信息
     */
    public function getTypeInfo($where = [], $field = true)
    {
        
        return $this->modelShopType->getInfo($where, $field);
    }
    
    /**
     * 店铺类型删除
     */
    public function typeDel($where = [])
    {
        
        $result = $this->modelShopType->deleteInfo($where);
        
        $result && action_log('删除', '店铺类型删除' . '，where：' . http_build_query($where));
        
        return $result ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, $this->modelShopType->getError()];
    }
}
