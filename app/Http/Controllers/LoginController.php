<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Firebase\JWT\JWT;
use App\Users;

class LoginController extends Controller
{
    public function login(Request $req)
    {
        if (!isset($_POST['email']) or !isset($_POST['password'])) 
        {
            return $this->error(400, 'No puede haber campos vacíos');
        }

        $email = $_POST['email'];
        $password = $_POST['password'];

        if ($this->checkIsRegister($email,$password))
        {
            $userSave = Users::where('email', $email)->first();

            $userData = array(

                'id' => $userSave->id,
                'name' => $userSave->name,
                'email' => $userSave->email,
                'password' => $userSave->password
            );

            $token = JWT::encode($userData, $this->key);

            return $this->success('Usuario Logeado', $token);
        }
        else
        {
            return $this->error(400, 'Los datos no son correctos');
        }
    }

    public function loginAdmin(Request $req)
    {
        if (!isset($_POST['email']) or !isset($_POST['password'])) 
        {
            return $this->error(400, 'No puede haber campos vacíos');
        }

        $email = $_POST['email'];
        $password = $_POST['password'];

        if ($this->checkIsRegister($email,$password))
        {

            $userSave = Users::where('email', $email)->first();

            if($userSave->id_rol != 2)
            {
                return $this->error(403, 'No tienes permisos');
            }

            $userData = array(

                'id' => $userSave->id,
                'name' => $userSave->name,
                'email' => $userSave->email,
                'password' => $userSave->password
            );

            $token = JWT::encode($userData, $this->key);

            return $this->success('Usuario Logeado', $token);
        }
        else
        {
            return $this->error(400, 'Los datos no son correctos');
        }
    }

    public function checkIsRegister($email,$password)
    {   
        $userSave = Users::where('email', $email)->first();

        if(!is_null($userSave))
        {
            $passwordSave = $this->decodificar($userSave->password);
        }
        
        
        if(!is_null($userSave) && $passwordSave == $password)
        {
            return true;
        }

        return false;
    }


}
