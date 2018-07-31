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
 * 登录逻辑
 */
class Login extends AdminBase
{
    
    /**
     * 登录处理
     */
    public function loginHandle($username = '', $password = '', $verify = '')
    {
        
        $validate_result = $this->validateLogin->scene('admin')->check(compact('username','password','verify'));
        
        if (!$validate_result) {
            
            return [RESULT_ERROR, $this->validateLogin->getError()];
        }
        
        $admin = $this->logicAdmin->getAdminInfo(['username' => $username]);
        
        if (!empty($admin['password']) && data_md5_key($password) == $admin['password']) {
            
            $this->logicAdmin->setAdminValue(['id' => $admin['id']], TIME_UT_NAME, TIME_NOW);

            $auth = ['admin_id' => $admin['id'], TIME_UT_NAME => TIME_NOW];

            session('admin_info', $admin);
            session('admin_auth', $auth);
            session('admin_auth_sign', data_auth_sign($auth));
            session('auth_shop_permission',$this->authShopPermission( $admin['id']));

            action_log('登录', '登录操作，username：'. $username);

            return [RESULT_SUCCESS, '登录成功', url('index/index')];
            
        } else {
            
            $error = empty($admin['id']) ? '用户账号不存在' : '密码输入错误';
            
            return [RESULT_ERROR, $error];
        }
    }
    
    /**
     * 注销当前用户
     */
    public function logout()
    {
        
        clear_login_session();
        
        return [RESULT_SUCCESS, '注销成功', url('login/login')];
    }
    
    /**
     * 清理缓存
     */
    public function clearCache()
    {
        
        \think\Cache::clear();
        
        return [RESULT_SUCCESS, '清理成功', url('index/index')];
    }
}
