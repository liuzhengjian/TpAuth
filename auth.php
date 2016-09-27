<?php
/**
 * Created by PhpStorm.
 * User: liuzhengjian
 * Date: 16/9/26
 * Time: 下午1:21
 */
return [
    'auth_on' => true,                      // 认证开关
    'auth_type' => 1,                         // 认证方式，1为实时认证；2为登录认证。
    'auth_group' => 'auth_group',        // 用户组数据表名,不带前缀
    'auth_group_access' => 'auth_group_access', // 用户-用户组关系表,不带前缀
    'auth_rule' => 'auth_rule',         // 权限规则表,不带前缀
    'auth_user' => 'user'              // 用户信息表,不带前缀
];