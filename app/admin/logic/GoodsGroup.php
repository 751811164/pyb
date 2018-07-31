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
class GoodsGroup extends AdminBase
{

    /**
     * 获取信息
     */
    public function getGroupInfo($where = [], $field = true)
    {

        return $this->modelGoodsGroup->getInfo($where, $field);
    }

    /**
     * 获取信息
     */
    public function getGroupJoinInfo($where = [], $field = "*")
    {

        $this->modelGoodsGroup->alias('gg');
        if (!array_key_exists('gg.'.DATA_STATUS_NAME,$where))
        {
            $where['gg.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        }

        $join = [
            [SYS_DB_PREFIX.'goods g', 'g.id = gg.goods_id '],
            [SYS_DB_PREFIX.'goods_profile gf', 'g.id = gf.goods_id and gf.kind=0'],
        ];
        return $this->modelGoodsGroup->getInfo($where, $field,$join);
    }

    /**
     * 获取列表
     */
    public function getGroupList($where = [], $field = true, $order = '', $paginate = 0)
    {
        $this->modelGoodsGroup->alias('gg');
        if (!array_key_exists('gg.'.DATA_STATUS_NAME,$where))
        {
            $where['gg.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        }

        $join = [
            [SYS_DB_PREFIX.'goods g', 'g.id = gg.goods_id '],
            [SYS_DB_PREFIX.'goods_profile gf', 'g.id = gf.goods_id and gf.kind=0'],
            [SYS_DB_PREFIX.'goods_price gp', 'gf.id = gp.profile_id and gp.shop_id='.SHOP_ID],
            [SYS_DB_PREFIX.'goods_type gt', 'g.goods_type = gt.id', 'LEFT'],
            [SYS_DB_PREFIX.'category c', 'g.category_id = c.id', 'LEFT'],
            [SYS_DB_PREFIX.'admin a', 'g.admin_add_id = a.id', 'LEFT'],
        ];


        
        return $this->modelGoodsGroup->getList($where, $field, $order, $paginate,$join );
    }

    /**
     * 获取列表
     */
    public function getSimpleGroupList($where = [], $field = true, $order = '', $paginate = 0,$join = [], $group = '', $limit = null)
    {

        return $this->modelGoodsGroup->getList($where, $field, $order, $paginate,$join ,$group, $limit);
    }
    
    /**
     * 信息编辑
     */
    public function groupEdit($data = [])
    {
        
        $validate_result = $this->validateGoodsGroup->scene('edit')->check($data);
        
        if (!$validate_result) {
            
            return [RESULT_ERROR, $this->validateGoodsGroup->getError()];
        }
        
        $url = url('groupList');
        
        $result = $this->modelGoodsGroup->setInfo($data);
        
        $handle_text = empty($data['id']) ? '新增' : '编辑';
        
        $result && action_log($handle_text, '' . $handle_text . '，name：' . $data['name']);
        
        return $result ? [RESULT_SUCCESS, '操作成功', $url] : [RESULT_ERROR, $this->modelGoodsGroup->getError()];
    }


    
    /**
     * 删除
     */
    public function groupDel($where = [])
    {
        
        $result = $this->modelGoodsGroup->deleteInfo($where);
        
        $result && action_log('删除', '删除' . '，where：' . http_build_query($where));
        
        return $result ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, $this->modelGoodsGroup->getError()];
    }
}
