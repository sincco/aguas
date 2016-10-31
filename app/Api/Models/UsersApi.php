<?php

class UsersapiModel extends Sincco\Sfphp\Abstracts\Model {

    public function validateAccess($data) {
        $query = 'SELECT * FROM __usersAPI WHERE email=:email AND password=:password and estatus=:estatus;';
        $users = $this->connector->query($query,['email'=>$data['email'], 'password'=>$data['password'], 'estatus'=>'A']);
        return array_shift($users);
    }
}