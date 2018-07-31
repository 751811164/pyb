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
 * 逻辑层
 */
class SalaryStructure extends AdminBase
{

    /**
 * 信息
 */
    public function getStructureInfo($where = [], $field = true)
    {
        $info = $this->modelSalaryStructure->getInfo($where, $field);
        return $info;
    }

    /**
     * 信息
     */
    public function getStructureJoinInfo($where = [], $field = true,$join=[])
    {
        $this->modelSalaryStructure->alias('ss');
        $info = $this->modelSalaryStructure->getInfo($where, $field,$join);
        return $info;
    }

    /**
     * 获取结构列表
     */
    public function getStructureList($where = [], $field = 's.name shop_name,g.name group_name,ss.*', $order = 'ss.status desc,ss.id desc', $paginate = DB_LIST_ROWS)
    {

        $this->modelSalaryStructure->alias('ss');

        $join = [
            [SYS_DB_PREFIX.'auth_group g', 'g.id = ss.group_id', 'LEFT'],
            [SYS_DB_PREFIX.'shop s', 's.id = g.shop_id', 'LEFT'],
        ];

        $where['g.'.DATA_STATUS_NAME]  = ['neq', DATA_DELETE];
        $where['ss.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];

        $info = $this->modelSalaryStructure->getList($where, $field, $order, $paginate, $join);
        return $info;
    }

    public function getSimpleStructureList($where = [], $field = true, $order, $paginate = DB_LIST_ROWS)
    {
        return $this->modelSalaryStructure->getList($where, $field, $order, $paginate);
    }


    /**
     * 获取列表搜索条件
     */
    public function getWhere($data = [])
    {

    }


    /**
     * 编辑
     */
    public function structureEdit($data = [])
    {

        $validate_result = $this->validateSalaryStructure->scene('edit')->check($data);

        if (!$validate_result) : return [RESULT_ERROR, $this->validateSalaryStructure->getError()]; endif;

//        $url = url('structureList');
        $data["salary_types"]=implode(',',$data["salary_types"]);
//        $data[DATA_STATUS_NAME]=DATA_NORMAL;
        if (empty($data['id']))
        {
            $info =$this->modelSalaryStructure->getInfo(['group_id'=>$data['group_id'],DATA_STATUS_NAME=>['neq',DATA_DELETE]]);
            if ($info)
            {
                return [RESULT_ERROR, "该岗位已添加过薪资结构"];
            }
        }

//        $this->modelSalaryStructure->setInfo([DATA_STATUS_NAME=>DATA_DISABLE],['group_id'=>$data['group_id']]);
        $result = $this->modelSalaryStructure->setInfo($data);


        $handle_text = empty($data['id']) ? '新增': '编辑';

        $result && action_log($handle_text, '薪资结构'.$handle_text.'，岗位id：'.$data['group_id']);

        return $result ? [RESULT_SUCCESS, '操作成功']: [RESULT_ERROR, $this->modelSalaryStructure->getError()];

    }


    public function setStatusDisable($where = [])
    {
        $this->modelSalaryStructure->setFieldValue($where, DATA_STATUS_NAME, 0);
    }


    public function getFieldValue($where = [], $field = '', $default = null, $force = false)
    {
        $this->modelSalaryStructure->getValue($where, $field, $default, $force);
    }


}

