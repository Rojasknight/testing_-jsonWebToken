<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Requests;
use App\User;
use App\Helpers\JwtAuth;

class UserController extends Controller
{
    public function register(Request $request)
    {
        //Recoger post


        $json = $request->input('json', null);
        $params = json_decode($json);


        $email = (!is_null($json) && isset($params->email)) ? $params->email : null;
        $name = (!is_null($json) && isset($params->name)) ? $params->name : null;
        $surname = (!is_null($json) && isset($params->surname)) ? $params->surname : null;
        $role = 'ROLE_USER';
        $password = (!is_null($json) && isset($params->password)) ? $params->password : null;


        if (!is_null($email) && !is_null($name) && !is_null($password)) {

            //Crear el usuario
            $user = new User();

            $user->email = $email;
            $user->name = $name;
            $user->surname = $surname;
            $user->role = $role;

            $pwd = hash('sha256', $password);
            $user->password = $pwd;

            //Comprobar el usuario
            $isser_user = User::where('email', '=', $email)->first();

            if (count($isser_user) == 0) {
                //guardarlo

                $user->save();

                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'usuario creado correctamente'
                );
            } else {
                //no guardarlo
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'usuario no duplicado, no puede registrarse'
                );

            }

        } else {
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'usuario no creado'
            );
        }


        return response()->json($data, 200);
    }

    public function login(Request $request)
    {
        $jwtAuth = new JwtAuth();

        //request POST
        $json = $request->input('json');

        $params = json_decode($json);

        $email = (!is_null($json) && isset($params->email) ? $params->email : null);
        $password = (!is_null($json) && isset($params->password) ? $params->password : null);
        $getToken = (!is_null($json) && isset($params->gettoken) ? $params->gettoken : null);

        //password encrypt
        $pwd = hash('sha256', $password);

        if (!is_null($email) && !is_null($password) && ($getToken == null || $getToken == 'false')) {
            $singup = $jwtAuth->singup($email, $pwd);

        } elseif ($getToken != null) {
            $singup = $jwtAuth->singup($email, $pwd, $getToken);

        } else {
            $singup = array(
                'status' => 'error',
                'message' => 'envia tus datos por post'
            );
        }


        return response()->json($singup, 200);
    }
}
