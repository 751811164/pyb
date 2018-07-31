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
class SupplierContacts extends AdminBase
{
    /**
     * 获取信息
     */
    public function getContactsInfo($where = [], $field = true, $join = [])
    {
        $this->modelSupplierContacts->alias('c');
//        if (empty($join))
//        {
//            $join = [
//                [SYS_DB_PREFIX."admin add", "add.id = s.admin_add_id", "LEFT"],
//                [SYS_DB_PREFIX."admin edit", "edit.id = s.admin_edit_id", "LEFT"],
//            ];
//        }
        return $this->modelSupplierContacts->getInfo($where, $field, $join);
    }

    /**
     * 获取列表
     */
    public function getContactsList($where = [], $field = true, $order = '', $pagenate = 0, $join = [])
    {
        $this->modelSupplierContacts->alias('c');
        if (empty($where['c.'.DATA_STATUS_NAME]))
        {
            $where['c.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        }

        return $this->modelSupplierContacts->getList($where, $field, $order, $pagenate, $join);
    }

    /**
     * 获取搜索条件
     * @param array $data
     * @return array
     */
    public function getWhere($data = [])
    {
        $where = [];
        !empty($data['search_data']) && $where['c.name'] = ['like', '%'.$data['search_data'].'%'];
       
        return $where;
    }


    /**
     * 信息编辑
     */
    public function contactsEdit($data = [])
    {

        $validate_result = $this->validateContacts->scene('edit')->check($data);

        if (!$validate_result) : return [RESULT_ERROR, $this->validateContacts->getError()]; endif;



        $result=  $this->modelSupplierContacts->setInfo($data);



        $handle_text = empty($data['id']) ? '新增': '编辑';

        $result && action_log($handle_text, ''.$handle_text.'，name：'.$data['name']);

        return $result ? [RESULT_SUCCESS, '操作成功']: [RESULT_ERROR, $this->modelSupplierContacts->getError()];
    }


    /**
     * 删除
     */
    public function contactsDel($where = [])
    {

        $result = $this->modelSupplierContacts->deleteInfo($where);

        $result && action_log('删除', '删除'.'，where：'.http_build_query($where));

        return $result ? [RESULT_SUCCESS, '删除成功','']: [RESULT_ERROR, $this->modelSupplierContacts->getError()];
    }

}
