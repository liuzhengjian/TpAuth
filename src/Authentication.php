<?php
namespace TpAuth\Auth;

use think\Db;
use TpAuth\Auth\Contracts\Authentication as UserContract;

class Authentication implements UserContract
{
    protected $attributes;

    //默认配置
    protected $_config = array(
        'auth_remember_token_cookie' => 'remember_token', //记住登录存储的标示名称
        'auth_remember_token' => 'remember_token', //记住登录字段
        'auth_user_sign_name' => 'auth_user_sign_name', //用户登陆后的唯一标示名
        'auth_storage_name' => 'auth_user_info', //缓存用户登录信息的索引名称
        'auth_data_key' => '9.xF%CQ{yz7mq-DXJ=?!lI1^T}+[c]eP:kBDUYHNM"`5', //默认数据加密KEY
        'auth_user' => 'user',            // 用户信息表
    );

    public function __construct()
    {
        if (config('auth')) {
            //可设置配置项 auth_config, 此配置项为数组。
            $this->_config = array_merge($this->_config, config('auth'));
        }

    }

    /**
     * 生成用户登录唯一标示
     * @param $data
     * @return string
     */
    public function data_sign_auth($data)
    {
        //数据类型检测
        if (!is_array($data)) {
            $data = (array)$data;
        }
        ksort($data); //排序
        $code = http_build_query($data); //url编码并生成query字符串
        $sign = md5($code . $this->_config['auth_data_key']); //生成签名
        return $sign;
    }

    /**
     * 检测用户是否登录
     * @return bool
     */
    public function check()
    {
        $user = $this->user();
        if (!$user) {
            return false;
        } else {
            $this->setAttributes($user);
            return session($this->_config['auth_user_sign_name']) == $this->data_sign_auth($user) ? true : false;
        }
    }

    /**
     * 获取登录后的用户信息
     * @return Session
     */
    public function user()
    {
        return session($this->_config['auth_storage_name']);
    }

    /**
     * 认证用户信息
     * @param array $credentials
     * @param bool $remember
     * @param bool $login
     * @return bool
     */
    public function attempt(array $credentials = [], $remember = false, $login = true)
    {
        if (!$this->validate($credentials)) {
            return false;
        }

        if ($login) {
            $this->login($this->getAttributes(), $remember);
        }

        return true;
    }

    /**
     * 验证登录信息
     * @param array $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        $pwd = $credentials['password'];
        unset($credentials['password']);
        if (empty($credentials)) {
            return false;
        }
        $user = Db::name($this->_config['auth_user'])->where($credentials)->find();
        if ($user && password_verify($pwd, $user['password'])) {
            $this->setAttributes($user);
            return true;
        }
        return false;
    }

    /**
     * 认证用户信息
     * @param array $user
     * @param bool $remember
     */
    public function login(array $user, $remember = false)
    {
        if ($remember) {
            //更新用户的 remember_token
            $sign = $this->data_sign_auth($user);
            $this->updateRememberToken($user, $sign);
            cookie($this->_config['auth_remember_token_cookie'], $sign, 3600*24*30);
            $user[$this->_config['auth_remember_token']] = $sign;
        }
        $this->setAttributes($user);
        session($this->_config['auth_storage_name'], $user);
        session($this->_config['auth_user_sign_name'], $this->data_sign_auth($user));
    }

    /**
     * 通过ID认证用户
     * @param $id
     * @param bool $remember
     * @return bool
     */
    public function loginUsingId($id, $remember = false)
    {
        $user = Db::name($this->_config['auth_user'])->where('id', $id)->find();
        if ($user) {
            $this->login($user, $remember);
            return true;
        }
        return false;
    }

    /**
     * 通过记住功能登录
     * @return bool
     */
    public function rememberLogin()
    {
        $rememberToken = $this->getRememberToken();
        if (!$rememberToken) {
            return false;
        }
        $user = Db::name($this->_config['auth_user'])->where($this->getRememberTokenName(), $rememberToken)->find();
        if ($user && $user['status'] == 1) {
            $this->login($user);
            return true;
        }
        return false;
    }

    /**
     * 判断是否使用记住功能
     * @return bool
     */
    public function viaRemember()
    {
        $remember_token = cookie($this->_config['auth_remember_token_cookie']);
        if ($remember_token) {
            $this->setRememberToken($remember_token);
            return true;
        }
        return false;
    }

    public function logout()
    {
        session($this->_config['auth_storage_name'], null);
        session($this->_config['auth_user_sign_name'], null);
        cookie($this->_config['auth_remember_token_cookie'], null);
    }

    /**
     * 更新用户RememberToken
     * @param array $user
     */
    public function updateRememberToken(array $user, $token)
    {
        Db::name($this->_config['auth_user'])->where('id', $user['id'])->update(
            [$this->getRememberTokenName() => $token]
        );
    }

    /**
     * @return mixed
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param mixed $attributes
     */
    public function setAttributes($attributes = null)
    {
        $this->attributes = $attributes;
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->attributes['id'];
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->attributes['password'];
    }

    /**
     * Get the "remember me" token value.
     *
     * @return string
     */
    public function getRememberToken()
    {
        return $this->attributes[$this->getRememberTokenName()];
    }

    /**
     * Set the "remember me" token value.
     *
     * @param  string $value
     * @return void
     */
    public function setRememberToken($value)
    {
        $this->attributes[$this->getRememberTokenName()] = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return $this->_config['auth_remember_token'] ? $this->_config['auth_remember_token'] : 'remember_token';
    }

    /**
     * Dynamically access the user's attributes.
     *
     * @param  string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->attributes[$key];
    }

    /**
     * Dynamically set an attribute on the user.
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Dynamically check if a value is set on the user.
     *
     * @param  string $key
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->attributes[$key]);
    }

    /**
     * Dynamically unset a value on the user.
     *
     * @param  string $key
     * @return void
     */
    public function __unset($key)
    {
        unset($this->attributes[$key]);
    }
}