<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Users;

class UserController extends Controller
{
    public function index()
    {
        if ($this->checkLoginAdmin()) 
        { 
        	$users = Users::all();

        	if(count($users) <= 0)
        	{
        		return $this->error(400, "No hay usuarios");
        	}

            return $this->success('Todos los usuarios', $users);
        }
        else
        {
            return $this->error(400, "No tienes permisos");
        }    
    }
    
    public function store(Request $request)
    {
        if ($this->checkLoginAdmin()) 
        { 
            
        	if(!$request->filled("user"))
            {
                return $this->error(400, "No puede estar vacio el nombre del usuario");
            }

            if(!$request->filled("email"))
            {
                return $this->error(400, "No puede estar vacio el email del usuario");
            }

            if(!$request->filled("password"))
            {
                return $this->error(400, "No puede estar vacio la contraseña");
            }

            if($this->checkPassword($request->password))
            {
                return $this->error(400,'La contraseña tiene que ser superior a 8 carecteres');
            }
            if($this->checkEmail($request->email))
            {
                return $this->error(400,'El email no es valido');
            }
            if($this->checkUserExist($request->email))
            {
                return $this->error(400,'El usuario ya existe');
            }

            $user = new Users();
            $user->name = $this->deleteAllSpace($request->user);
            $user->password = $this->codificar($request->password);
            $user->email = $request->email;
            $user->id_rol = 2;
            $user->save();
            return $this->success('El usuario ha sido creado', $user);
        }
        else
        {
            return $this->error(400, "No tienes permisos");
        }    
    }

    public function show($id)
    {
        if ($this->checkLoginAdmin()) 
        {
            $userSave = Users::where("id",$id)->first();

            if(is_null($userSave))
            {
            	return $this->error(400, "No existe el usuario");
            }
            return $this->success('El usuario', $userSave);
        }
        else
        {
            return $this->error(400, "No tienes permisos");
        } 
    }

    public function update(Request $request, $id)
    {
        if ($this->checkLoginAdmin()) 
        { 
            $userData = $this->getUserData();

            $newName = $this->deleteAllSpace($request->newName);
            $newEmail = $request->newEmail;
            $newPassword = $request->newPassword;

            $userSave = Users::where('id',$id)->first();

            if(!is_null($newName))
            {
                $userSave->name = $newName;
            }
            if(!is_null($newEmail))
            {
                if($this->checkEmail($newEmail))
                {
                    return $this->error(400,'El email no es valido');
                }
                if($this->checkUserExist($newEmail))
                {
                    return $this->error(400,'El mail ya esta siendo usado');
                }
                $userSave->email = $newEmail;
            }
            if(!is_null($newPassword))
            {
                if($this->checkPassword($newPassword))
                {
                    return $this->error(400,'La contraseña tiene que ser superior a 8 carecteres');
                }
                $userSave->password = $newPassword;
            }

            $userSave->save();

            return $this->success('Usuario modificado', $userSave);
        }
        else
        {
            return $this->error(400, "No tienes permisos");
        }
    }
   
    public function destroy($id)
    {
        if ($this->checkLoginAdmin()) 
        { 
            $userSave = Users::where("id",$id)->first();

            if(is_null($userSave))
            {
            	return $this->error(400, "No existe el usuario");
            }

            $userSave->delete();
            return $this->success('El usuario a fallado', "");
        }
        else
        {
            return $this->error(400, "No tienes permisos");
        }   
    }
}
