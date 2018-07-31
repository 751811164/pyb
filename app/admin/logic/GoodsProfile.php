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
class GoodsProfile extends AdminBase
{

    /**
     * 获取信息
     */
    public function getProfileInfo($where = [], $field = true)
    {

        return $this->modelGoodsProfile->getInfo($where, $field);
    }

    /**
     * 获取信息
     */
    public function getProfileJoinInfo($where = [], $field = "*")
    {

        $this->modelGoodsProfile->alias('gf');
        if (!array_key_exists('gf.'.DATA_STATUS_NAME,$where))
        {
            $where['gf.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        }


        if (!array_key_exists("gp.shop_id",$where))
        {
            $where["gp.shop_id"] = SHOP_ID;//默认价
        }

        if (!array_key_exists("gf.kind",$where))
        {
            $where["gf.kind"] = 0;//连接单品
        }















        $join = [
            [SYS_DB_PREFIX.'goods_price gp', 'gp.profile_id = gf.id ', 'LEFT'],
            [SYS_DB_PREFIX.'goods_group gg', 'gg.profile_id = gf.id ', 'LEFT'],
        ];
        return $this->modelGoodsProfile->getInfo($where, $field,$join);
    }

    /**
     * 获取列表
     */
    public function getProfileList($where = [], $field = true, $order = '', $paginate = 0)
    {
        $this->modelGoodsProfile->alias('gf');
        if (!array_key_exists('gf.'.DATA_STATUS_NAME,$where))
        {
            $where['gf.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        }
        if (!array_key_exists("gp.shop_id",$where))
        {
            $where["gp.shop_id"] = SHOP_ID;//默认价
        }

        if (!array_key_exists("gf.kind",$where))
        {
            $where["gf.kind"] = 0;//连接单品
        }

        $join = [          
            [SYS_DB_PREFIX.'goods_price gp', 'gp.profile_id = gf.id', 'LEFT'],
            [SYS_DB_PREFIX.'goods_group gg', 'gg.profile_id = gf.id ', 'LEFT'],          
        ];


        
        return $this->modelGoodsProfile->getList($where, $field, $order, $paginate,$join );
    }

    /**
     * 获取列表
     */
    public function getSimpleProfileList($where = [], $field = true, $order = '', $paginate = 0,$join = [], $group = '', $limit = null)
    {
        $this->modelGoodsProfile->alias('gf');
        if (!array_key_exists('gf.'.DATA_STATUS_NAME,$where))
        {
            $where['gf.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        }
        return $this->modelGoodsProfile->getList($where, $field, $order, $paginate,$join ,$group, $limit);
    }
    
    /**
     * 信息编辑
     */
    public function profileEdit($data = [])
    {
        
        $validate_result = $this->validateGoodsProfile->scene('edit')->check($data);
        
        if (!$validate_result) {
            
            return [RESULT_ERROR, $this->validateGoodsProfile->getError()];
        }
        
        $url = url('profileList');
        
        $result = $this->modelGoodsProfile->setInfo($data);
        
        $handle_text = empty($data['id']) ? '新增' : '编辑';
        
        $result && action_log($handle_text, '' . $handle_text . '，name：' . $data['name']);
        
        return $result ? [RESULT_SUCCESS, '操作成功', $url] : [RESULT_ERROR, $this->modelGoodsProfile->getError()];
    }


    
    /**
     * 删除
     */
    public function profileDel($where = [])
    {
        
        $result = $this->modelGoodsProfile->deleteInfo($where);
        
        $result && action_log('删除', '删除' . '，where：' . http_build_query($where));
        
        return $result ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, $this->modelGoodsProfile->getError()];
    }
}
