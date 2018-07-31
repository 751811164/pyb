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
class PerformanceRecord extends AdminBase
{

    /**
     * 获取信息
     */
    public function getRecordInfo($where = [], $field = true)
    {

        return $this->modelRecord->getInfo($where, $field);
    }


    /**
     * 获取列表
     */
    public function getRecordList($where = [], $field = "r.*,a.username", $order = '', $paginate = 0)
    {
        $this->modelPerformanceRecord->alias('r');

        $join = [
            [SYS_DB_PREFIX.'admin a', 'r.admin_id = a.id'],
            [SYS_DB_PREFIX.'auth_group_access ga', 'ga.admin_id = a.id'],
        ];

        $where['a.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        $where['r.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];


        return $this->modelPerformanceRecord->getList($where, $field, $order, $paginate, $join);
    }

    /**
     * 获取查询条件
     */
    public function getWhere($data)
    {
        $where = [];
        !empty($data["search_data"]) && $where["name"] = ["like", "%".$data["search_data"]."%"];
        return $where;
    }

    /**
     * 信息编辑
     */
    public function recordEdit($data = [])
    {

        $validate_result = $this->validatePerformanceRecord->scene('edit')->check($data);

        if (!$validate_result)
        {

            return [RESULT_ERROR, $this->validatePerformanceRecord->getError()];
        }

        $info  = $this->logicSalaryType->getTypeInfo(["id" => $data["type_id"]]);
        $table = $info ? $info->getAttr('table'): null;

        if (!$table)
        {
            return [RESULT_ERROR, "类型有误"];
        }
        $fn  = "calc".ucfirst(lineToHump($table));
        $res = $this->$fn($data);
        if ($res[0] === RESULT_ERROR)
        {
            return $res;
        }

        $data = array_merge($data, $res[1]);
        //        $url = url('recordList');

        $result = $this->modelPerformanceRecord->setInfo($data);

        $handle_text = empty($data['id']) ? '新增': '编辑';

        $result && action_log($handle_text, '绩效计算'.$handle_text.'，admin_id：'.$data['admin_id'].'，金额：'.$data['amount']);

        return $result ? [RESULT_SUCCESS, '操作成功']: [RESULT_ERROR, $this->modelPerformanceRecord->getError()];
    }

    /**
     * 删除
     */
    public function recordDel($where = [])
    {

        $result = $this->modelRecord->deleteInfo($where);

        $result && action_log('删除', '绩效计算删除'.'，where：'.http_build_query($where));

        return $result ? [RESULT_SUCCESS, '删除成功']: [RESULT_ERROR, $this->modelRecord->getError()];
    }


    /**
     * 基本工资
     */
    public function calcSettingSalary($data = [])
    {

        //        $structure = $this->logicSalaryStructure->getStructureInfo(["id"=>$admin['group_id']]);
        //员工信息
        $admin = $this->logicAdmin->getAdminInfo(["id" => $data['admin_id']]);

        $where_setting = [
            "group_id" => $admin['group_id'],
            DATA_STATUS_NAME => DATA_NORMAL,
        ];
        //岗位配置信息
        $setting = $this->logicSettingSalary->getSettingInfo($where_setting);
        if (empty($setting))
        {
            return [RESULT_ERROR, "该员工所在岗位未配置基本薪资"];
        }


        $diff = date_diff(date_create($admin["entry_time"]), date_create());
        if ($diff->d >= $setting['trial_period'])
        {
            //转正
            $times = (int)(($diff->d - $setting['trial_period']) / $setting['increase_period']);//次数
            $times = ($times < $setting['increase_times']) ? $times: $setting['increase_times'];

            return [RESULT_SUCCESS, ['amount' => $setting['regular_salary'] + $setting['increase_amount'] * $times]];
        }
        else
        {
            //试用
            return [RESULT_SUCCESS, ['amount' => $setting["trial_salary"]]];;
        }

        //入职时间 判断是否是试用期
        //转正后是否是加薪状态

    }

    /**
     * 基本业绩提成
     * @Todo 计算规则
     */
    public function calcSettingPerformance($data = [])
    {


    }

    /**
     * 基本类别提成
     * @Todo 计算规则
     */
    public function calcSettingCategory($data = [])
    {


    }

    /**
     * 基本品牌提成
     * @Todo 计算规则
     */
    public function calcSettingBrand($data = [])
    {


    }

    /**
     * 基本商品提成
     * @Todo 计算规则
     */
    public function calcSettingGoods($data = [])
    {


    }

    /**
     * 基本办卡提成
     * @Todo 计算规则
     */
    public function calcSettingCard($data = [])
    {


    }

    /**
     * 礼券提成
     * @Todo 计算规则
     */
    public function calcSettingCoupon($data = [])
    {


    }

    /**
     * 推广提成
     */
    public function calcSettingGeneralize($data = [])
    {

        $admin = $this->logicAdmin->getAdminInfo(["id" => $data['admin_id']]);

        $where["referrer_id"] = $admin["admin_id"];
        $where["create_time"] = ["between time", [$data["start_time"], $data["end_time"]]];//精确到秒
        //统计获得的推广次数
        $generalizes = $this->modelGeneralize->getList($where, true, '', false);
        if (empty($generalizes))
        {
            return [RESULT_ERROR, "该员在此期间暂无推广奖励"];;
        }

        //查找有效期内的所有推广配置
       // $sql      = "select * from ob_setting_generalize where (start_time < '".$data["end_time"]."' or end_time > '".$data["start_time"]."' ) and group_id = ".$admin["group_id"]." and status=1";

        $sql = $this->modelSettingGeneralize->where(function($query) use ($data) {
            $query->where("start_time", "< time", $data["end_time"])->whereOr("end_time", "> time", $data["start_time"]);
        })->where(["group_id" => $admin["group_id"], "status" => 1])->buildSql(false);

        $settings = $this->logicSettingOther->query($sql);
        if (empty($settings))
        {
            return [RESULT_ERROR, "该员工所在岗位未配置推广奖励"];
        }
        $amount = 0;
        foreach ($settings as $key => $setting)
        {

            foreach ($generalizes as $k => $generalize)
            {
                $typename = $generalize["type_name"];
                if (array_key_exists($typename,$setting))
                {
                    $amount   += (int)$setting[$typename];
                }

            }
        }

        return [RESULT_SUCCESS, ['amount' => $amount]];;


    }

    /**
     * 其他奖励
     */
    public function calcSettingOther($data = [])
    {

        //$sql = "select * from ob_setting_other where (start_time < '".$data["end_time"]."' or end_time > '".$data["start_time"]."' ) and admin_id = ".$data["admin_id"]." and status=1";
        //'id > :id AND name LIKE :name ',['id'=>0, 'name'=>'thinkphp%']

        $sql = $this->modelSettingOther->where(function($query) use ($data) {
            $query->where("start_time", "<  time", $data["end_time"])->whereOr("end_time", ">  time", $data["start_time"]);
        })->where(["admin_id" => $data["admin_id"], "status" => 1])->buildSql(false);


        //查找有效期内的所有奖励配置
        $settings = $this->logicSettingOther->query($sql);
        if (empty($settings))
        {
            return [RESULT_ERROR, "该员工未配置其他奖励"];
        }
        $amount = 0;
        foreach ($settings as $key => $setting)
        {
            $rules = json_decode($setting['rules'], true);
            foreach ($rules as $k => $rule)
            {
                if (array_key_exists("award", $rule))
                {
                    $amount += $rule["award"];
                }
            }
        }
        return [RESULT_SUCCESS, ['amount' => $amount]];;

    }

    /**
     * 抵扣费用
     */
    public function calcSettingExpense($data = [])
    {
        //        $sql="select * from ob_setting_expense where (start_time < '".$data["end_time"]."' or end_time > '".$data["start_time"]."' ) and admin_id = ".$data["admin_id"]." and status=1";

        $sql = $this->modelSettingExpense->where(function($query) use ($data) {
            $query->where("start_time", "<  time", $data["end_time"])->whereOr("end_time", ">  time", $data["start_time"]);
        })->where(["admin_id" => $data["admin_id"], "status" => 1])->buildSql(false);

        //查找有效期内的所有费用配置
        $settings = $this->modelSettingExpense->query($sql);
        if (empty($settings))
        {
            return [RESULT_ERROR, "该员工未配置抵扣费用"];
        }
        $amount = 0;
        foreach ($settings as $key => $setting)
        {
            $rules = json_decode($setting['rules'], true);
            foreach ($rules as $k => $rule)
            {
                if (array_key_exists("expense", $rule))
                {
                    $amount += $rule["expense"];
                }

            }
        }

        return [RESULT_SUCCESS, ['amount' => $amount]];
    }
}
