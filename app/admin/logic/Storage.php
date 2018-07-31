<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Bigotry <3162875@qq.com>                               |
// +---------------------------------------------------------------------+
// | ReStorageitory | https://gitee.com/Bigotry/OneBase                      |
// +---------------------------------------------------------------------+

namespace app\admin\logic;

/**
 * 逻辑
 */
class Storage extends AdminBase
{

    /**
     * 获取列表
     */
    public function getStorageList($where = [], $field = 'st.*, gp.name goods_name,sh.name shop_name,m.name member_name,m.number member_number', $order = '',$pagenate=0,$join=[])
    {

        $this->modelStorage->alias('st');
        if (empty($join))
        {
            $join=[
                [SYS_DB_PREFIX . 'member m','m.id=st.member_id','LEFT'],
                [SYS_DB_PREFIX . 'shop sh','st.shop_id=sh.id','LEFT'],
                [SYS_DB_PREFIX . 'goods_barcode gb','gb.barcode=st.barcode','LEFT'],
                [SYS_DB_PREFIX . 'goods_profile gp','gp.id=gb.profile_id','LEFT'],
            ];
        }

        $where['st.' . DATA_STATUS_NAME] = ['neq', DATA_DELETE];

        return $this->modelStorage->getList($where, $field, $order, $pagenate, $join);
    }

    /**
     * 获取搜索条件
     * @param array $data
     * @return array
     */
    public function getWhere($data=[]){
        $where = [];

        !empty($data['search_data']) && $where['st.sn|m.number|m.name'] = ['like', '%'.$data['search_data'].'%'];
        !empty($data['search_data']) && $where['st.shop_id']=$data['search_data'];
        if (!empty($data['start_time'])||!empty($data['end_time']))
        {
            $date = getdate();
            $end_time=  empty($data['end_time'])? mktime(0,0, 0,$date['mon'],$date['mday']+1,$date['year']):$data['end_time']." 23:59:59";
            $start_time= empty($data['start_time'])?0:$data['start_time'];
            //            $query->whereTime('do.create_time', 'between', [$start_time, $end_time]);
            $where['st.create_time']=[ 'between time', [$start_time, $end_time]];
        }
        return $where;
    }

//
//    /**
//     * 编辑
//     */
//    public function StorageEdit($data = [])
//    {
//
//        $validate_result = $this->validateStorage->scene('edit')->check($data);
//
//        if (!$validate_result) : return [RESULT_ERROR, $this->validateStorage->getError()]; endif;
//
//        $url = url('StorageList');
//
//        $result = $this->modelStorage->setInfo($data);
//
//        $handle_text = empty($data['id']) ? '新增' : '编辑';
//
//        $result && action_log($handle_text, '礼券' . $handle_text . '，name：' . $data['name']);
//
//        return $result ? [RESULT_SUCCESS, '操作成功', $url] : [RESULT_ERROR, $this->modelStorage->getError()];
//    }
//
//    /**
//     * 获取信息
//     */
//    public function getStorageInfo($where = [], $field = true)
//    {
//
//        return $this->modelStorage->getInfo($where, $field);
//    }
//
//    /**
//     * 删除
//     */
//    public function StorageDel($where = [])
//    {
//
//        $result = $this->modelStorage->deleteInfo($where);
//
//        $result && action_log('删除', '礼券删除' . '，where：' . http_build_query($where));
//
//        return $result ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, $this->modelStorage->getError()];
//    }

}
