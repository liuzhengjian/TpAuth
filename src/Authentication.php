<?php
namespace TpAuth\Auth;

use think\session;

class Authentication {

    public function info(){
        session::set('id',1);
        dump(session::get('id'));
    }


}