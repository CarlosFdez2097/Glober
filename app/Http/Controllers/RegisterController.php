<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Firebase\JWT\JWT;
use App\Users;

class RegisterController extends Controller
{
    public function register (Request $request)
    {
        if (!isset($_POST['user']) or !isset($_POST['email']) or !isset($_POST['password'])) 
        {
            return $this->error(400, 'No puede haber campos vacios');
        }

        $user = $this->deleteAllSpace($_POST['user']);
        $email = $_POST['email'];
        $password = $_POST['password'];

        if($this->checkPassword($password))
        {
            return $this->error(400,'La contraseña tiene que ser superior a 8 carecteres');
        }
        if($this->checkEmail($email))
        {
            return $this->error(400,'El email no es valido');
        }
        if($this->checkUserExist($email))
        {
            return $this->error(400,'El usuario ya existe');
        }
     
        if (!empty($user) && !empty($email) && !empty($password))
        {
            $users = new Users();
            $users->name = $user;
            $users->password = $this->codificar($password);
            $users->email = $email;
            $users->save();

            $userSave = Users::where('email', $email)->first();

            $userData = array(

                'id' => $userSave->id,
                'name' => $userSave->name,
                'email' => $userSave->email,
                'password' => $userSave->password
            );

            $token = JWT::encode($userData, $this->key);

            return $this->success('Usuario registrado',$token);                 
        }
        else
        {
            return $this->error(400,'No puede haber campos vacios');
        }    
    }
}
