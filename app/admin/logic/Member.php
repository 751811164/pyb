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
class Member extends AdminBase
{

    /**
     * 获取会员档案信息
     */
    public function getMemberInfo($where = [], $field = true)
    {
        return $this->modelMember->getInfo($where, $field);
    }

    /**
     * 获取会员档案信息
     */
    public function getMemberJoinInfo($where = [], $field = 'm.*,c.type_id,add.username admin_add_name,edit.username admin_edit_name,refer.username referrer_name')
    {
        $this->modelMember->alias('m');
        $join                         = [
            //            [SYS_DB_PREFIX.'member_type mt', 'mt.id = m.type_id', 'LEFT'],
            [SYS_DB_PREFIX.'card c', 'c.id = m.card_id', 'LEFT'],
            [SYS_DB_PREFIX.'admin refer', 'refer.id = m.referrer_id', 'LEFT'],
            [SYS_DB_PREFIX.'admin add', 'add.id = m.admin_add_id', 'LEFT'],
            [SYS_DB_PREFIX.'admin edit', 'edit.id = m.admin_edit_id', 'LEFT'],
        ];
        $where['m.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        return $this->modelMember->getInfo($where, $field, $join);
    }

    /**
     * 获取列表
     */
    public function getMemberList($where = [], $field = 'm.*,s.name shop_name,ct.name type_name, ct.number type_number,c.name card_name,c.number card_number,add.username admin_add_name,edit.username admin_edit_name', $order = '', $pagenate = 0)
    {

        $this->modelMember->alias('m');
        $join                         = [
            [SYS_DB_PREFIX.'shop s', 's.id = m.shop_id', 'LEFT'],
            [SYS_DB_PREFIX.'card c', 'c.id = m.card_id', 'LEFT'],
            [SYS_DB_PREFIX.'card_type ct', 'ct.id = c.type_id', 'LEFT'],
            [SYS_DB_PREFIX.'admin add', 'add.id = m.admin_add_id', 'LEFT'],
            [SYS_DB_PREFIX.'admin edit', 'edit.id = m.admin_edit_id', 'LEFT'],
        ];
        $where['m.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];

        return $this->modelMember->getList($where, $field, $order, $pagenate, $join);
    }

    /**
     * 获取搜索条件
     * @param array $data
     * @return array
     */
    public function getWhere($data = [])
    {
        $where = [];

        !empty($data['search_data']) && $where['m.name'] = ['like', '%'.$data['search_data'].'%'];
        !empty($data['cid']) && $where['m.card_id'] =$data['cid'] ;

        if (!empty($data['start_time'])||!empty($data['end_time']))
        {
            $date = getdate();
            $end_time=  empty($data['end_time'])? mktime(0,0, 0,$date['mon'],$date['mday']+1,$date['year']):$data['end_time']." 23:59:59";
            $start_time= empty($data['start_time'])?0:$data['start_time'];
//            $query->whereTime('do.create_time', 'between', [$start_time, $end_time]);
            $where['m.create_time']=[ 'between time', [$start_time, $end_time]];
        }


        return $where;
    }


    /**
     * 会员档案信息编辑
     */
    public function memberEdit($data = [])
    {
        $validate_result = $this->validateMember->scene('edit')->check($data);
        if (!$validate_result) : return [RESULT_ERROR, $this->validateMember->getError()]; endif;

        $children = [];
        foreach ($data as $name => $value)
        {
            if (strpos($name, "@") === 0)
            {
                $name = trim($name, "@");
                foreach ($value as $key => $val)
                {
                    $children[$key][$name] = $val;
                }
            }
        }



        $data['password'] = data_md5_key($data['password']);
        if (empty($data['id']))
        {
            $data['number']       = $this->createCode($data);
            $data['admin_add_id'] = ADMIN_ID;
            $handle_text          = '新增';
        }
        else
        {
            $data['admin_edit_id'] = ADMIN_ID;
            $handle_text           = '编辑';
        }
        $fn = function()use($data,$children) {
            $result = $this->modelMember->setInfo($data);

            if (empty($data["id"]))
            {
                $data["id"] = $this->modelMember->getLastInsID();
            }

            foreach ($children as $key => $item)
            {
                $children[$key]["id"]        = $item["id"] ?: null;
                $children[$key]["member_id"] = $data["id"];
            }
           $res= $this->modelMemberChildren->setList($children, true);
            return $result;
        };
        $result=  closure_list_exe([$fn]);
        $result && action_log($handle_text, '会员档案'.$handle_text.'，name：'.$data['name']);

        return $result ? [RESULT_SUCCESS, '操作成功']: [RESULT_ERROR, $this->modelMember->getError()];
    }


    /**
     * 会员档案删除
     */
    public function memberDel($where = [])
    {

        $result = $this->modelMember->deleteInfo($where);

        $result && action_log('删除', '会员档案删除'.'，where：'.http_build_query($where));

        return $result ? [RESULT_SUCCESS, '删除成功']: [RESULT_ERROR, $this->modelMember->getError()];
    }

    /**
     *生成编号
     */
    public function createCode($data = [], $len = 5)
    {
        $where_card['id']= $data['type_id'];
        $where_card[DATA_STATUS_NAME]= ['neq',DATA_DELETE];
        $card = $this->modelCard->getInfo($where_card, true);
        if (empty($card))
        {
            return null;
        }

        $where['card_id']=$data['card_id'];
        $where[DATA_STATUS_NAME]=['neq',DATA_DELETE];
        $number = $this->modelMember->stat($where, 'max', 'number');

        $number= substr($number,-$len);
        $number = sprintf("%0{$len}s",  intval($number) + 1);

        return $card['number'].$number;
    }
}
