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
 * 类型逻辑
 */
class GoodsType extends AdminBase
{
    
    /**
     * 获取类型列表
     */
    public function getTypeList($where = [], $field = true, $order = '', $paginate = 0,$join = [], $group = '', $limit = null)
    {
        if (empty($where[DATA_STATUS_NAME]))
        {
            $where[DATA_STATUS_NAME]= ['neq', DATA_DELETE];
        }
        return $this->modelGoodsType->getList($where, $field, $order, $paginate,$join ,$group, $limit);
    }


    
    /**
     * 类型信息编辑
     */
    public function typeEdit($data = [])
    {
        
        $validate_result = $this->validateGoodsType->scene('edit')->check($data);
        
        if (!$validate_result) {
            
            return [RESULT_ERROR, $this->validateGoodsType->getError()];
        }
        
        $url = url('typeList');
        
        $result = $this->modelGoodsType->setInfo($data);
        
        $handle_text = empty($data['id']) ? '新增' : '编辑';
        
        $result && action_log($handle_text, '类型' . $handle_text . '，name：' . $data['name']);
        
        return $result ? [RESULT_SUCCESS, '操作成功', $url] : [RESULT_ERROR, $this->modelGoodsType->getError()];
    }

    /**
     * 获取类型信息
     */
    public function getTypeInfo($where = [], $field = true)
    {
        
        return $this->modelGoodsType->getInfo($where, $field);
    }
    
    /**
     * 类型删除
     */
    public function typeDel($where = [])
    {
        
        $result = $this->modelGoodsType->deleteInfo($where);
        
        $result && action_log('删除', '类型删除' . '，where：' . http_build_query($where));
        
        return $result ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, $this->modelGoodsType->getError()];
    }
}
