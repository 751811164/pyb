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
 * 岗位逻辑
 */
class AuthGroup extends AdminBase
{


    /**
     * 获取树形岗位组
     * @param array     $where
     * @param string    $field
     * @param string    $order
     * @param int|mixed $limit
     * @return array|mixed
     */
    public function getGroupTree($where = [], $field = "*", $order = '', $paginate = false)
    {
        $groupTree = cache('groupTree');
        $groupTree = false;
        if (!$groupTree)
        {
            //            $where['a.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];
            $where_type[DATA_STATUS_NAME]  = ['neq', DATA_DELETE];
            $where_shop[DATA_STATUS_NAME]  = ['neq', DATA_DELETE];
            $where_group['id']             = ['>', 0];
            $where_group[DATA_STATUS_NAME] = ['neq', DATA_DELETE];

            $typeList  = collection($this->logicShopType->getTypeList($where_type, 'id,name', "", false, [], ''))->toArray();
            $shopList  = collection($this->logicShop->getList($where_shop, 'id,name,type_id', "", false, [], ''))->toArray();
            $groupList = collection($this->getAuthGroupList($where_group, 'id,CONCAT(number,name) name,shop_id', '', false, [], ''))->toArray();
            //单独查询普通系统管理组(应业务需求)
            //        $adminGroup = $this->getGroupInfo(['id' => 0],'id, name,shop_id','',false,[],'',1)->data;

            foreach ($typeList as $key => $type)
            {
                $typeList[$key]["children"] = [];
                foreach ($shopList as $k => $shop)
                {
                    $shopList[$k]["children"] = [];

                    foreach ($groupList as $i => $group)
                    {
                        if ($group['shop_id'] == $shop['id'])
                        {
                            $shopList[$k]["children"][] = $group;
                        }
                    }


                    if ($shop['type_id'] == $type['id'])
                    {
                        $typeList[$key]["children"][] = $shopList[$k];
                    }
                }
            }
            //            array_unshift($typeList,$adminGroup);
            $groupTree = ['typeList' => $typeList, 'shopList' => $shopList, 'groupList' => $groupList];
            cache('groupTree', $groupTree, ['expire' => 600]);
        }

        return $groupTree;
    }


    /**
     * 获取权限分组列表
     */
    public function getAuthGroupList($where = [], $field = true, $order = '', $paginate = false, $join = [], $group = '', $limit = null)
    {
        if (empty($where[DATA_STATUS_NAME]))
        {
            $where[DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        }
        return $this->modelAuthGroup->getList($where, $field, $order, $paginate, $join, $group, $limit);
    }

    /**
     * 获取查询条件
     */
    public function getWhere($data)
    {
        $where = [];
        !empty($data['search_data']) && $where['name'] = ['like', "%".$data['search_data']."%"];
        return $where;
    }

    /**
     * 岗位添加
     */
    public function groupAdd($data = [])
    {

        $validate_result = $this->validateAuthGroup->scene('add')->check($data);

        if (!$validate_result)
        {
            return [RESULT_ERROR, $this->validateAuthGroup->getError()];
        }

        if (empty($data['id']))
        {
            $data['number'] = $this->createCode($data);
        }
        else
        {
            unset($data['number']);
        }

        $url = url('groupList');

        $data['admin_id'] = ADMIN_ID;

        $result = $this->modelAuthGroup->setInfo($data);

        $result && action_log('新增', '新增岗位，name：'.$data['name']);
        $result && cache('groupTree', null);
        return $result ? [RESULT_SUCCESS, '岗位添加成功']: [RESULT_ERROR, $this->modelAuthGroup->getError()];
    }


    /**
     * 权限复制--快速设置
     */
    public function fastSet($data)
    {

        $validate_result = $this->validateAuthGroup->scene('fastSet')->check($data);
        if (!$validate_result) : return [RESULT_ERROR, $this->validateAuthGroup->getError()]; endif;
        $url = url('grouplist');
        //$where=[];
        //参考的岗位权限
        $authGroup = $this->modelAuthGroup->getInfo(['id' => $data['refer_id']]);

        $where['id'] = $data["id"];
        $result      = $this->modelAuthGroup->setInfo(['rules' => $authGroup->rules], $where);
        return $result ? [RESULT_SUCCESS, '操作成功']: [RESULT_ERROR, $this->modelAuthGroup->getError()];


    }


    public function createCode($data = [], $len = 2)
    {
        $where_shop['id']=$data['shop_id'];
        $where_shop[DATA_STATUS_NAME]=['neq',DATA_DELETE];
        $shop = $this->modelShop->getInfo($where_shop, true);
        if (empty($shop))
        {
            return null;
        }
        $where_group['shop_id']=$data['shop_id'];
        $where_group[DATA_STATUS_NAME]=DATA_NORMAL;
        $number = $this->modelAuthGroup->stat($where_group, 'max', 'number');
        $number= substr($number,-$len);
        $number = sprintf("%0{$len}s",  intval($number) + 1);

        return $shop['number'].$number;
    }


    //    /**
    //     * 岗位编辑
    //     */
    //    public function groupEdit($data = [])
    //    {
    //
    //        $validate_result = $this->validateAuthGroup->scene('edit')->check($data);
    //
    //        if (!$validate_result) {
    //
    //            return [RESULT_ERROR, $this->validateAuthGroup->getError()];
    //        }
    //
    //        $url = url('groupList');
    //
    //        $result = $this->modelAuthGroup->setInfo($data);
    //
    //        $result && action_log('编辑', '编辑岗位，name：' . $data['name']);
    //
    //        return $result ? [RESULT_SUCCESS, '岗位编辑成功', $url] : [RESULT_ERROR, $this->modelAuthGroup->getError()];
    //    }

    //    /**
    //     * 岗位删除
    //     */
    //    public function groupDel($where = [])
    //    {
    //
    //        $result = $this->modelAuthGroup->deleteInfo($where);
    //
    //        $result && action_log('删除', '删除岗位，where：' . http_build_query($where));
    //
    //        return $result ? [RESULT_SUCCESS, '岗位删除成功'] : [RESULT_ERROR, $this->modelAuthGroup->getError()];
    //    }

    /**
     * 获取岗位信息
     */
    public function getGroupInfo($where = [], $field = true, $join = [])
    {
        $this->modelAuthGroup->alias("g");
        return $this->modelAuthGroup->getInfo($where, $field, $join);
    }

    /**
     * 设置用户组权限节点
     */
    public function setGroupRules($data = [])
    {

        $data['rules'] = !empty($data['rules']) ? implode(',', array_unique($data['rules'])): '';

        $url = url('groupList');

        $result = $this->modelAuthGroup->setInfo($data);

        if ($result)
        {

            action_log('授权', '设置岗位权限，id：'.$data['id']);

            $this->updateSubAuthByGroup($data['id']);

            return [RESULT_SUCCESS, '权限设置成功'];
        }
        else
        {

            return [RESULT_ERROR, $this->modelAuthGroup->getError()];
        }
    }

    /**
     * 选择岗位 admin/adminAuth调用
     */
    public function selectAuthGroupList($group_list = [], $admin_group_list = [])
    {

        $admin_group_ids = array_extract($admin_group_list, 'group_id');

        foreach ($group_list as &$info)
        {

            in_array($info['id'], $admin_group_ids) ? $info['tag'] = 'active': $info['tag'] = '';
        }

        return $group_list;
    }

    /**
     * 递归更新下级权限节点，确保下级权限不能超越上级
     * 若上级某权限被收回，则下级对应的权限同样被收回
     * 按会员更新
     */
    public function updateSubAuthByAdmin($admin_id = 0)
    {

        $group_list = $this->logicAuthGroupAccess->getAdminGroupInfo($admin_id);

        $rules_str_list = array_extract($group_list, 'rules');

        $rules_array_list = array_map("str2arr", $rules_str_list);

        $rules_array = [];

        foreach ($rules_array_list as $v)
        {

            $rules_array = array_merge($rules_array, $v);
        }

        // 当前授权会员的所有权限节点数组
        $rules_unique_array = array_unique($rules_array);

        $sub_admin_ids = $this->logicAdmin->getSubAdminIds($admin_id);

        $sub_group_list = $this->logicAuthGroupAccess->getAdminGroupInfo($sub_admin_ids);

        // 所有下级的岗位id集合
        $sub_group_ids = array_unique(array_extract($sub_group_list, 'group_id'));

        $this->updateGroupRulesByStandard($rules_unique_array, $sub_group_ids);
    }

    /**
     * 递归更新下级权限节点，确保下级权限不能超越上级
     * 若上级某权限被收回，则下级对应的权限同样被收回
     * 按岗位更新
     */
    public function updateSubAuthByGroup($group_id = 0)
    {

        $group_list = $this->logicAuthGroupAccess->getAuthGroupAccessList(['group_id' => $group_id]);

        $admin_arr_ids = array_unique(array_extract($group_list, 'admin_id'));

        foreach ($admin_arr_ids as $id)
        {

            $this->updateSubAuthByAdmin($id);
        }
    }

    /**
     * 按参数$standard_rules_array权限节点数组标准，将参数$group_ids岗位ID数组下的权限节点全部更新
     */
    public function updateGroupRulesByStandard($standard_rules_array = [], $group_ids = [])
    {

        $group_list = $this->getAuthGroupList(['id' => ['in', $group_ids]]);

        foreach ($group_list as $v)
        {

            $rules_arr = str2arr($v['rules']);

            foreach ($rules_arr as $kk => $vv)
            {
                if (!in_array($vv, $standard_rules_array))
                {

                    unset($rules_arr[$kk]);
                }
            }

            $v['rules'] = arr2str(array_values($rules_arr));

            $this->modelAuthGroup->setFieldValue(['id' => $v['id']], 'rules', $v['rules']);
        }
    }
}
