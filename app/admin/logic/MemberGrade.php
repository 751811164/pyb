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
class MemberGrade extends AdminBase
{

    /**
     * 获取列表
     */
    public function getMemberGradeList($where = [], $field = true, $order = '',$pagenate=0,$join=[])
    {
        $this->modelMemberGrade->alias('mg');
        if (empty( $where['mg.' . DATA_STATUS_NAME])&&empty( $where[ DATA_STATUS_NAME]))
        {
            $where['mg.' . DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        }
        return $this->modelMemberGrade->getList($where, $field, $order, $pagenate, $join);
    }

    /**
     * 获取列表
     */
    public function getSimpleMemberGradeList($where = [], $field = true, $order = '',$pagenate=0,$join=[])
    {
        $this->modelMemberGrade->alias('mg');
        if (empty( $where['mg.' . DATA_STATUS_NAME])&&empty( $where[ DATA_STATUS_NAME]))
        {
            $where['mg.' . DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        }
        return $this->modelMemberGrade->getList($where, $field, $order, $pagenate, $join);
    }

    /**
     * 获取搜索条件
     * @param array $data
     * @return array
     */
    public function getWhere($data=[]){
        $where = [];

        !empty($data['search_data']) && $where['p.name'] = ['like', '%'.$data['search_data'].'%'];

        return $where;
    }


    /**
     * 信息编辑
     */
    public function memberGradeEdit($data = [])
    {

        $validate_result = $this->validateMemberGrade->scene('edit')->check($data);

        if (!$validate_result) : return [RESULT_ERROR, $this->validateMemberGrade->getError()]; endif;

        $url = url('memberGradeList');

        $result = $this->modelMemberGrade->setInfo($data);

        $handle_text = empty($data['id']) ? '新增' : '编辑';

        $result && action_log($handle_text, '' . $handle_text . '，name：' . $data['name']);

        return $result ? [RESULT_SUCCESS, '操作成功', $url] : [RESULT_ERROR, $this->modelMemberGrade->getError()];
    }

    /**
     * 获取信息
     */
    public function getMemberGradeInfo($where = [], $field = true)
    {

        return $this->modelMemberGrade->getInfo($where, $field);
    }

    /**
     * 删除
     */
    public function memberGradeDel($where = [])
    {

        $result = $this->modelMemberGrade->deleteInfo($where);

        $result && action_log('删除', '删除' . '，where：' . http_build_query($where));

        return $result ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, $this->modelMemberGrade->getError()];
    }

}
