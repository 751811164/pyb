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
class Supplier extends AdminBase
{
    /**
     * 获取信息
     */
    public function getSupplierInfo($where = [], $field = "s.*,add.username admin_add_name,edit.username admin_edit_name", $join = [])
    {
        $this->modelSupplier->alias('s');
        if (empty($join)&&$join!==false)
        {
            $join = [
                [SYS_DB_PREFIX."admin add", "add.id = s.admin_add_id", "LEFT"],
                [SYS_DB_PREFIX."admin edit", "edit.id = s.admin_edit_id", "LEFT"],
            ];
        }
        return $this->modelSupplier->getInfo($where, $field, $join);
    }

    /**
     * 获取列表
*/
    public function getSupplierList($where = [], $field = true, $order = '', $pagenate = 0, $join = [])
    {
        $this->modelSupplier->alias('s');
        if (empty($where['s.'.DATA_STATUS_NAME]))
        {
            $where['s.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        }

        return $this->modelSupplier->getList($where, $field, $order, $pagenate, $join);
    }

    /**
     * 获取搜索条件
     * @param array $data
     * @return array
     */
    public function getWhere($data = [])
    {
        $where = [];
        !empty($data['search_data']) && $where['s.name'] = ['like', '%'.$data['search_data'].'%'];
        !empty($data["cid"]) && $where["city_id"] = $data["cid"];
        return $where;
    }

    /**
     * 获取某列值
     */
    public function getSupplierColumn($where = [], $field = '', $key = ''){
      return  $this->modelSupplier->getColumn($where , $field , $key  );
    }

    /**
     * 信息编辑
     */
    public function supplierEdit($data = [])
    {

        $validate_result = $this->validateSupplier->scene('edit')->check($data);

        if (!$validate_result) : return [RESULT_ERROR, $this->validateSupplier->getError()]; endif;

        $fn = function() use ($data) {
            $contacts = [];
            foreach ($data as $name => $value)
            {
                if (strpos($name, "@") === 0)
                {
                    $name = trim($name, "@");
                    foreach ($value as $key => $val)
                    {
                        $contacts[$key][$name] = $val;
                    }
                }
            }

            if (empty($data["id"]))
            {
                $data["admin_add_id"] = ADMIN_ID;
            }
            else
            {
                $data["admin_edit_id"] = ADMIN_ID;
            }

            $this->modelSupplier->setInfo($data);

            if (empty($data["id"]))
            {
                $data["id"] = $this->modelSupplier->getLastInsID();
                $number     = sprintf("1%'06s", $data["id"]);
                $this->modelSupplier->setInfo(["number" => $number]);
            }

            foreach ($contacts as $key => $item)
            {
                $contacts[$key]["id"]=$item["id"]?:null;
                $contacts[$key]["supplier_id"] = $data["id"];
            }
            $this->modelSupplierContacts->setList($contacts,true);

        };

       $result= closure_list_exe([$fn]);

        $handle_text = empty($data['id']) ? '新增': '编辑';

        $result && action_log($handle_text, ''.$handle_text.'，name：'.$data['name']);

        return $result ? [RESULT_SUCCESS, '操作成功']: [RESULT_ERROR, $this->modelSupplier->getError()];
    }


    /**
     * 删除
     */
    public function supplierDel($where = [])
    {

        $result = $this->modelSupplier->deleteInfo($where);

        $result && action_log('删除', '删除'.'，where：'.http_build_query($where));

        return $result ? [RESULT_SUCCESS, '删除成功']: [RESULT_ERROR, $this->modelSupplier->getError()];
    }

}
