<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Bigotry <3162875@qq.com>                               |
// +---------------------------------------------------------------------+
// | Recarditory | https://gitee.com/Bigotry/OneBase                      |
// +---------------------------------------------------------------------+

namespace app\admin\logic;

/**
 * 逻辑
 */
class OtherAward extends AdminBase
{

    /**
     * 获取列表
     */
    public function getAwardList($where = [], $field = 'a.*', $order = '',$pagenate=0,$join=[])
    {

        $this->modelOtherAward->alias('a');
        //$join = [
        //    [SYS_DB_PREFIX . 'admin a', 'p.admin_id = a.id',"LEFT"],
        //    [SYS_DB_PREFIX . 'shop s', 'p.shop_id = s.id','LEFT'],
        //];
        $where['a.' . DATA_STATUS_NAME] = ['neq', DATA_DELETE];

        return $this->modelOtherAward->getList($where, $field, $order, $pagenate, $join);
    }

    /**
     * 获取搜索条件
     * @param array $data
     * @return array
     */
    public function getWhere($data=[]){
        $where = [];

        !empty($data['search_data']) && $where['a.name'] = ['like', '%'.$data['search_data'].'%'];

        return $where;
    }


    /**
     * POS机信息编辑
     */
    public function awardEdit($data = [])
    {

//        $validate_result = $this->validateAward->scene('edit')->check($data);
//
//        if (!$validate_result) : return [RESULT_ERROR, $this->validateAward->getError()]; endif;

        $url = url('awardList');

        $result = $this->modelOtherAward->setInfo($data);
        $lastInsID =$this->getLastInsID();

        $handle_text = empty($data['id']) ? '新增' : '编辑';

        $result && action_log($handle_text, '奖项名' . $handle_text . '，name：' . $data['name']);

//       $list= collection($this->getAwardList([],true,'id,name',false))->toArray();

        return $result ? [RESULT_SUCCESS, '操作成功', $url,['id'=>$lastInsID,'name'=>$data['name']]] : [RESULT_ERROR, $this->modelOtherAward->getError()];
    }

    /**
     * 获取POS机信息
     */
    public function getAwardInfo($where = [], $field = true)
    {

        return $this->modelOtherAward->getInfo($where, $field);
    }

    /**
     * POS机删除
     */
    public function awardDel($where = [])
    {

        $result = $this->modelOtherAward->deleteInfo($where);

        $result && action_log('删除', 'POS机删除' . '，where：' . http_build_query($where));

        return $result ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, $this->modelOtherAward->getError()];
    }

}
