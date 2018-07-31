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

namespace app\admin\controller;

/**
 * 控制器
 */
class AdminExpense extends AdminBase
{

    /**
     *  列表
     */
    public function expenseList()
    {
        $where = $this->logicAdminExpense->getWhere($this->param);
        $this->assign('list', $this->logicAdminExpense->getExpenseList($where));
        return $this->fetch('expense_list');
    }



    /**
     * 添加与编辑通用方法
     */
    public function ExpenseCommon()
    {
        IS_POST && $this->jump($this->logicAdminExpense->expenseEdit($this->param));

        $where[DATA_STATUS_NAME]=DATA_NORMAL;
        $this->assign('shop_list', $this->logicShop->getShopList($where, 'id,name', '', false));
    }


    /**
     * 添加
     */
    public function expenseAdd()
    {
        $this->ExpenseCommon();
        return $this->fetch('expense_edit');
    }

    /**
     * 编辑
     */
    public function expenseEdit()
    {
        $this->ExpenseCommon();
        $info = $this->logicAdminExpense->getExpenseInfo(['id' => $this->param['id']]);

        $this->assign('info', $info);

        return $this->fetch('expense_edit');
    }

    /**
     * 删除
     */
    public function expenseDel($id = 0)
    {

        $this->jump($this->logicAdminExpense->expenseDel(['id' => $id]));
    }




}
