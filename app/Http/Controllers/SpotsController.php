<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Spots;

class SpotsController extends Controller
{
    public function index()
    {
        if ($this->checkLogin()) 
        { 
            $userData = $this->getUserData();
            $spotsSave = $this->allSpotsOneUser($userData->id);

            if(count($spotsSave) < 1 )
            {
                return $this->error(400, "No has creado spots");
            }

            return $this->success('Todas los spots creadas por el usuario', $spotsSave);
        }
        else
        {
            return $this->error(400, "No tienes permisos");
        }    
    }
   
    public function create()
    {
        
    }
    
    public function store(Request $request)
    {
        if ($this->checkLogin()) 
        { 
            if(!$request->filled("spotName"))
            {
                return $this->error(400, "No puede estar vacio el nombre del spot");
            }

            if(!$request->filled("spotDescription"))
            {
                return $this->error(400, "No puede estar vacio la descripcion del spot");
            }

            if(!$request->filled("dateOfStart"))
            {
                return $this->error(400, "No puede estar vacio la fecha de inicio del spot");
            }

            if(!$request->filled("dateOfEnd"))
            {
                return $this->error(400, "No puede estar vacio la fecha de fin del spot");
            }

            $userData = $this->getUserData();

            if($this->isUsedSpotName($request->spotName,$userData->id))
            {
                return $this->error(400,'El nombre del spots ya esta siendo usado');
            }
            
            $spots = new Spots();
            $spots->id_user = $userData->id;
            $spots->name = $this->deleteAllSpace($request->spotName);
            $spots->description = $request->spotDescription;
            $spots->dateOfStart = $request->dateOfStart;
            $spots->dateOfEnd = $request->dateOfEnd;
            $spots->save();
            return $this->success('Spot creado', $request->spotName);
        }
        else
        {
            return $this->error(400, "No tienes permisos");
        }    
    }

    public function show($spotName)
    {
        if ($this->checkLogin()) 
        {
            if(is_null($spotName))
            {
                return $this->error(400, "El nombre del spot tiene que estar rellenado");
            }

            $userData = $this->getUserData();

            $spotSave = $this->oneSpotOfUser($userData->id,$spotName);

            if(is_null($spotSave))
            {
                return $this->error(400, "No se ha creado ese spot");
            }

            return $this->success('El spot selecionado', $spotSave);
        }
        else
        {
            return $this->error(400, "No tienes permisos");
        } 
    }
   
    public function edit($categoryname)
    {
           
    }

    public function update(Request $request, $spot_name)
    {
        if ($this->checkLogin()) 
        { 
            if(is_null($spot_name))
            {
                return $this->error(400, "El nombre del spot tiene que estar rellenado");
            }

            if(!$request->filled("newSpotName") && !$request->filled("newSpotDescription") && !$request->filled("newSpotDateOfStart") && !$request->filled("newSpotdateOfEnd"))
            {
                return $this->error(400, "Alguno de los parametro no esta rellenado");
            }

            $userData = $this->getUserData();
            $spotSave = $this->oneSpotOfUser($userData->id,$spot_name);

            if(is_null($spotSave))
            {
                return $this->error(400, "El spot no se a encontrado");
            }

            if($this->isUsedSpotName($userData->id ,$request->newSpotName))
            {
                return $this->error(400, "El nombre ya se esta usando");
            }

            $spotSave->name = $request->newSpotName;
            $spotSave->description = $request->newSpotDescription;
            $spotSave->dateOfStart = $request->newSpotDateOfStart;
            $spotSave->dateOfEnd = $request->newSpotdateOfEnd;
            $spotSave->save();

            return $this->success('El spot  a sido actualizada', $spotSave);
        }
        else
        {
            return $this->error(400, "No tienes permisos");
        }
    }
   
    public function destroy($spot)
    {
        if ($this->checkLogin()) 
        { 
            $spotName = $spot;
            $spotSave = Spots::where('name',$spotName)->first();

            if(is_null($spotSave))
            {
                return $this->error(400, "El spot no se a encontrado");
            }
            
            $spotSave->delete();
            return $this->success('ha sido borrado el spot', "");
        }
        else
        {
            return $this->error(400, "No tienes permisos");
        }       
    }

    private function allSpotsOneUser($id)
    {
        return Spots::where('id_user', $id)->get();
    }

    private function oneSpotOfUser($id,$spotname)
    {
        $spotsSave = $this->allSpotsOneUser($id);

        foreach ($spotsSave as $spots => $spot)
        {
            if($spotname == $spot->name)
            {
                return $spot;
            }
        }
        return null;
    }

    private function isUsedSpotName($id_user,$spotName)
    {
        $spotsSave = $this-> allSpotsOneUser($id_user);
        foreach ($spotsSave as $spot => $spotSave) 
        {
            if($spotSave->name == $spotName)
            {
                return true;
            }  
        }
        return false;
    }
}
