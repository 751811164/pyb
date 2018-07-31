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
 * 员工逻辑
 */
class Admin extends AdminBase
{

    /**
     * 获取员工信息
     */
    public function getAdminInfo($where = [], $field = '*')
    {
        $this->modelAdmin->alias('a');
        $join = [
            [SYS_DB_PREFIX.'auth_group_access ga', 'ga.admin_id = a.id', 'LEFT'],
            [SYS_DB_PREFIX.'admin_permission p', 'p.admin_id = a.id', 'LEFT'],
        ];
        $where['a.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        $info = $this->modelAdmin->getInfo($where, $field, $join);
        return $info;
    }

    public function getAdminJoinInfo($where = [], $field = '*',$join=[])
    {
        $this->modelAdmin->alias('a');

        $info = $this->modelAdmin->getInfo($where, $field, $join);
        return $info;
    }


    /**
     * 获取员工列表
     */
    public function getAdminList($where = [], $field = 'a.*,g.name group_name,g.number group_number,pc_cashier,mob_cashier,take_stock', $order = '', $paginate = DB_LIST_ROWS)
    {

        $this->modelAdmin->alias('a');

        $join = [
//            [SYS_DB_PREFIX.'admin b', 'a.leader_id = b.id', 'LEFT'],
            [SYS_DB_PREFIX.'auth_group_access ga', 'ga.admin_id = a.id', 'LEFT'],
            [SYS_DB_PREFIX.'auth_group g', 'g.id = ga.group_id', 'LEFT'],
            [SYS_DB_PREFIX.'admin_permission ap', 'ap.admin_id = a.id', 'LEFT'],
            [SYS_DB_PREFIX.'shop s', 's.id = g.shop_id', 'LEFT'],
        ];

        $where['a.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        $where['a.id']                = ['neq', SYS_ADMINISTRATOR_ID];//列表不显示超级管理员

        return $this->modelAdmin->getList($where, $field, $order, $paginate, $join);
    }

    public function getSimpleAdminList($where = [], $field = true, $order = '', $paginate = DB_LIST_ROWS, $join = [])
    {
        $this->modelAdmin->alias('a');
        return $this->modelAdmin->getList($where, $field, $order, $paginate, $join);
    }


    /**
     * 导出员工列表
     */
    public function exportAdminList($where = [], $field = 'a.*,b.username as leader_username', $order = '')
    {

        $list = $this->getAdminList($where, $field, $order, false);

        $titles = "编号,名字,性别,生日,身份证,手机,住址,注册时间";
        $keys   = "number,username,gender,birthday,identity,mobile,address,create_time";

        action_log('导出', '导出员工列表');

        export_excel($titles, $keys, $list, '员工列表');
    }

    /**
     * 获取员工列表搜索条件
     */
    public function getWhere($data = [])
    {

        $where = [];

        !empty($data['search_data']) && $where['a.username|a.number|a.mobile'] = ['like', '%'.$data['search_data'].'%'];

        //        if (!is_administrator()) {
        //            //非超管只查看共享员工
        //            $admin = session('admin_info');
        //
        //            if ($admin['is_share_admin']) {
        //
        //                $ids = $this->getInheritAdminIds(ADMIN_ID);
        //
        //                $ids[] = ADMIN_ID;
        //
        //                $where['m.leader_id'] = ['in', $ids];
        //
        //            } else {
        //
        //                $where['m.leader_id'] = ADMIN_ID;
        //            }
        //        }

        return $where;
    }


    /**
     * 获取树形员工组
     * @return array
     */
    public function getAdminTree()
    {
        $adminTree = cache('adminTree');
               $adminTree = false;
        if (!$adminTree)
        {
            $where_shop[DATA_STATUS_NAME] = ['neq', DATA_DELETE];
            $where_group[DATA_STATUS_NAME] = ['neq', DATA_DELETE];
            $where_group['id'] = ['>',0];
            //            $typeList =collection( $this->logicShopType->getTypeList($where_type, 'id,name', "" ,false,[],''))->toArray();
            $shopList  = collection($this->logicShop->getList($where_shop, 'id,name,type_id', "", false, [], ''))->toArray();
            $groupList = collection($this->logicAuthGroup->getAuthGroupList($where_group, 'id,CONCAT(number,name) name,shop_id', '', false, [], ''))->toArray();
            $adminList = collection($this->getAdminList([], 'a.id,a.username as name,group_id', '', false, [], ''))->toArray();
            //单独查询普通系统管理组(应业务需求)
            $adminGroup =$this->logicAuthGroup->getGroupInfo(['id' => 0],'id, name,shop_id','',false,[],'',1)->data;

            foreach ($shopList as $k => $shop)
            {
                $shopList[$k]["children"] = [];

                foreach ($groupList as $i => $group)
                {
                    $groupList[$i]["children"]=[];
                    foreach ($adminList as $a=>$admin){

                        if ( $admin['group_id'] == $group['id'])
                        {
                            $groupList[$i]["children"][] = $admin;
                        }
                    }

                    if ($group['shop_id'] == $shop['id'])
                    {
                        $shopList[$k]["children"][] =$groupList[$i];
                    }
                }

            }

            foreach ($adminList as $a=>$admin){

                if ($admin['group_id'] == $adminGroup['id'])
                {
                    $adminGroup['children'][]=$admin;
                }
            }


            array_unshift($shopList,$adminGroup);

            $adminTree = ['shopList' => $shopList, 'groupList' => $groupList,'adminList'=>$adminList];
            cache('adminTree', $adminTree, ['expire' => 60]);
        }

        return $adminTree;
    }



    /**
     * 获取存在继承关系的员工ids
     * @deprecated
     */
    public function getInheritAdminIds($id = 0, $data = [])
    {

        $admin_id = $this->modelAdmin->getValue(['id' => $id, 'is_share_admin' => DATA_NORMAL], 'leader_id');

        if (empty($admin_id))
        {

            return $data;
        }
        else
        {

            $data[] = $admin_id;

            return $this->getInheritAdminIds($admin_id, $data);
        }
    }

    /**
     * 获取员工的所有下级员工
     * @deprecated
     */
    public function getSubAdminIds($id = 0, $data = [])
    {

        $admin_list = $this->modelAdmin->getList(['leader_id' => $id], 'id', 'id asc', false);

        foreach ($admin_list as $v)
        {

            if (!empty($v['id']))
            {

                $data[] = $v['id'];

                $data = array_unique(array_merge($data, $this->getSubAdminIds($v['id'], $data)));
            }

            continue;
        }

        return $data;
    }

    /**
     * 员工添加到用户组
     * @deprecated
     */
    public function addToGroup($data = [])
    {

        $url = url('adminList');

        if (SYS_ADMINISTRATOR_ID == $data['id'])
        {

            return [RESULT_ERROR, '天神不能授权哦~', $url];
        }

        $where = ['admin_id' => ['in', $data['id']]];

        $this->modelAuthGroupAccess->deleteInfo($where, true);

        if (empty($data['group_id']))
        {

            return [RESULT_SUCCESS, '员工授权成功', $url];
        }

        $add_data = [];

        foreach ($data['group_id'] as $group_id)
        {

            $add_data[] = ['admin_id' => $data['id'], 'group_id' => $group_id];
        }

        if ($this->modelAuthGroupAccess->setList($add_data))
        {

            action_log('授权', '员工授权，id：'.$data['id']);

            $this->logicAuthGroup->updateSubAuthByAdmin($data['id']);

            return [RESULT_SUCCESS, '员工授权成功', $url];
        }
        else
        {

            return [RESULT_ERROR, $this->modelAuthGroupAccess->getError()];
        }
    }

    /**
     * 员工添加
     */
    public function adminAdd($data = [])
    {


        $validate_result = $this->validateAdmin->scene('add')->check($data);

        if (!$validate_result)
        {
            return [RESULT_ERROR, $this->validateAdmin->getError()];
        }

        $url = url('adminList');

        //        $data['leader_id'] = ADMIN_ID;
        //        $data['is_inside'] = DATA_NORMAL;

        $data['number']= $this->createCode($data);
        //保存员工信息
        $adminFn = function() use ($data) {
            $data['password'] = data_md5_key($data['password']);
            $result            = $this->modelAdmin->setInfo($data);
            $admin_id          = $this->modelAdmin->getLastInsID();
            $_POST['admin_id'] = $admin_id;
        };

        //保存员工所在组
        $groupFn = function() use ($data) {
            $data['admin_id'] = $_POST['admin_id'];

            //$this->modelAuthGroupAccess->deleteInfo(['admin_id' => $data['admin_id']], true);
            $result = $this->modelAuthGroupAccess->setInfo($data);
        };
        //保存拥有的权限
        $permissionFn = function() use ($data) {
            $data['admin_id'] = $_POST['admin_id'];

            // $this->modelAdminPermission->deleteInfo(['admin_id' => $data['admin_id']], true);
            $result = $this->modelAdminPermission->setInfo($data);
        };
        $result       = closure_list_exe([$adminFn, $groupFn, $permissionFn]);


        $result && action_log('新增', '新增员工，username：'.$data['username']);

        return $result ? [RESULT_SUCCESS, '员工添加成功', $url]: [RESULT_ERROR, $this->modelAdmin->getError()];
    }

    /**
     * 员工编辑
     */
    public function adminEdit($data = [])
    {

        $validate_result = $this->validateAdmin->scene('edit')->check($data);

        if (!$validate_result)
        {
            return [RESULT_ERROR, $this->validateAdmin->getError()];
        }

//        $url = url('adminList');

        //保存员工信息
        $adminFn = function() use ($data) {
            unset($data['number']);
            $data['password'] = data_md5_key($data['password']);
            $result = $this->modelAdmin->setInfo($data);
        };

        if (SYS_ADMINISTRATOR_ID != $data['id'])
        {
            //保存员工所在组
            $groupFn = function() use ($data) {
                $result = $this->modelAuthGroupAccess->deleteInfo(['admin_id' => $data['admin_id']], true);
                $this->modelAuthGroupAccess->setInfo($data);
            };
            //保存拥有的权限
            $permissionFn = function() use ($data) {
                unset($data['id']);
                $info = $this->modelAdminPermission->getInfo(['admin_id' => $data['admin_id']]);

                if (empty($info) || $info->getAttr('admin_id') <= 0)
                {
                    $result = $this->modelAdminPermission->setInfo($data);
                }
                else
                {
                    $this->modelAdminPermission->updateInfo(['admin_id' => $data['admin_id']], $data);
                }
            };
            $result       = closure_list_exe([$adminFn, $groupFn, $permissionFn]);
        }
        else
        {
            $result = closure_list_exe([$adminFn]);
        }


        $result && action_log('编辑', '编辑员工，id：'.$data['id']);

        return $result ? [RESULT_SUCCESS, '员工编辑成功']: [RESULT_ERROR, $this->modelAdmin->getError()];
    }


    /**
     * 快速设置
     */
    public function fastSet($data = [])
    {
        $validate_result = $this->validateAdmin->scene('fastSet')->check($data);
        if (!$validate_result)
        {
            return [RESULT_ERROR, $this->validateAdmin->getError()];
        }


        $idArr    = explode(',', $data["ids"]);
        $groupArr = [];
        $list     = [];
        foreach ($idArr as $key => $id)
        {
            $list[$key]       = $data;
            $list[$key]['id'] = $id;

            $groupArr[$key] = ['admin_id' => $id, 'group_id' => $data['group_id']];
        }

        //保存员工信息
        $adminFn = function() use ($list) {
            $result = $this->modelAdmin->setList($list, true);
        };
        //保存员工所在组
        $groupFn = function() use ($groupArr, $idArr) {
            $result = $this->modelAuthGroupAccess->deleteInfo(['admin_id' => ['in', $idArr]], true);
            $this->modelAuthGroupAccess->setList($groupArr);
        };


        $result = closure_list_exe([$adminFn, $groupFn]);
        $url    = url('adminList');

        $result && action_log('批量设置', '编辑员工，ids：'.$data['ids']);

        return $result ? [RESULT_SUCCESS, '批量设置成功', $url]: [RESULT_ERROR, $this->modelAdmin->getError()];


    }


    /**
     * (登录时)设置员工信息
     */
    public function setAdminValue($where = [], $field = '', $value = '')
    {

        return $this->modelAdmin->setFieldValue($where, $field, $value);
    }

    /**
     * 员工删除
     */
    public function adminDel($where = [])
    {

        $url = url('adminList');

        if (SYS_ADMINISTRATOR_ID == $where['id'] || ADMIN_ID == $where['id'])
        {

            return [RESULT_ERROR, '天神和自己不能删除哦~', $url];
        }

        $result = $this->modelAdmin->deleteInfo($where);

        $result && action_log('删除', '删除员工，where：'.http_build_query($where));

        return $result ? [RESULT_SUCCESS, '员工删除成功', $url]: [RESULT_ERROR, $this->modelAdmin->getError(), $url];
    }

    public function setStatus($model = 'Admin', $data = [], $field = DATA_STATUS_NAME, $msg = "数据状态调整")
    {
        $keys = array_keys($data);
        if (count($keys) != 2)
        {
            return [RESULT_ERROR, "数据有误"];
        }

        return parent::setStatus($model, $data, $keys[1], $msg);
    }



    public function createCode($data = [], $len = 3)
    {
        $where_group['id']= $data['group_id'];
        $where_group[DATA_STATUS_NAME]= ['neq',DATA_DELETE];
        $group = $this->modelAuthGroup->getInfo($where_group, true);
        if (empty($group))
        {
            return null;
        }

        $join = [
            [SYS_DB_PREFIX.'auth_group_access ga', 'ga.admin_id = a.id', 'RIGHT'],
        ];
        $where['a.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        $where['ga.group_id'] = $data['group_id'];
        $admin= $this->getAdminJoinInfo($where,'max(a.number) number',$join);

        $number= substr($admin['number'],-$len);
        $number = sprintf("%0{$len}s",  intval($number) + 1);

        return $group['number'].$number;
    }


}

