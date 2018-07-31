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

namespace app\api\logic;

use app\api\error\CodeBase;
use app\api\error\Common as CommonError;
use \Firebase\JWT\JWT;

/**
 * 接口基础逻辑
 */
class Common extends ApiBase
{

    /**
     * 登录接口逻辑
     */
    public function login($data = [])
    {
      
        $validate_result = $this->validateAdmin->scene('login')->check($data);
        
        if (!$validate_result) {
            
            return CommonError::$usernameOrPasswordEmpty;
        }
        
        begin:
        
        $admin = $this->logicAdmin->getAdminInfo(['username' => $data['username']]);

        // 若不存在用户则注册
        if (empty($admin))
        {
            $register_result = $this->register($data);
            
            if (!$register_result) {
                
                return CommonError::$registerFail;
            }
            
            goto begin;
        }
        
        if (data_md5_key($data['password']) !== $admin['password']) {
            
            return CommonError::$passwordError;
        }
        
        return $this->tokenSign($admin);
    }
    
    /**
     * 注册方法
     */
    public function register($data)
    {

        $data['password']  = data_md5_key($data['password']);

        return $this->logicAdmin->setInfo($data);
    }
    
    /**
     * JWT验签方法
     */
    public static function tokenSign($admin)
    {
        
        $key = API_KEY . JWT_KEY;
        
        $jwt_data = ['admin_id' => $admin['id'],  'username' => $admin['username'], 'create_time' => $admin['create_time']];
        
        $token = [
            "iss"   => "OneBase JWT",         // 签发者
            "iat"   => TIME_NOW,              // 签发时间
            "exp"   => TIME_NOW + TIME_NOW,   // 过期时间
            "aud"   => 'OneBase',             // 接收方
            "sub"   => 'OneBase',             // 面向的用户
            "data"  => $jwt_data
        ];
        
        $jwt = JWT::encode($token, $key);
        
        $jwt_data['user_token'] = $jwt;
        
        return $jwt_data;
    }
    
    /**
     * 修改密码
     */
    public function changePassword($data)
    {
        
        $admin = get_admin_by_token($data['user_token']);
        
        $admin_info = $this->logicAdmin->getAdminInfo(['id' => $admin->admin_id]);
        
        if (empty($data['old_password']) || empty($data['new_password'])) {
            
            return CommonError::$oldOrNewPassword;
        }
        
        if (data_md5_key($data['old_password']) !== $admin_info['password']) {
            
            return CommonError::$passwordError;
        }

        $admin_info['password'] = $data['new_password'];
        
        $result = $this->logicAdmin->setInfo($admin_info);
        
        return $result ? CodeBase::$success : CommonError::$changePasswordFail;
    }
    
    /**
     * 友情链接
     */
    public function getBlogrollList()
    {
        
        return $this->modelBlogroll->getList([DATA_STATUS_NAME => DATA_NORMAL], true, 'sort desc,id asc', false);
    }
}
