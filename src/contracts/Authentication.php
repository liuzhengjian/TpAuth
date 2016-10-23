<?php
namespace TpAuth\Auth\Contracts;
/**
 * Created by PhpStorm.
 * User: liuzhengjian
 * Date: 16/9/28
 * Time: 下午9:47
 */
interface Authentication
{
    public function check();

    public function user();

    public function attempt(array $credentials = [], $remember = false, $login = true);

    public function validate(array $credentials = []);

    public function login(array $user, $remember = false);

    public function loginUsingId($id, $remember = false);

    public function viaRemember();

    public function logout();

}