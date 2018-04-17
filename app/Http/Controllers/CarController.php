<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Helpers\JwtAuth;
use App\Car;

class CarController extends Controller
{
    public function index(Request $request)
    {
        $cars = Car::all()->load('user');

        return response()->json(array(
            'cars' => $cars,
            'status' => 'success'
        ), 200);
    }

    public function show($id)
    {
        $car = Car::find($id)->load('user');

        return response()->json(array(
            'car' => $car,
            'status' => 'success'
        ), 200);
    }

    public function update($id, Request $request)
    {
        $car = Car::find($id);


        $hash = $request->header('Authorization', null);
        $jwtAuth = new JwtAuth();

        $checkToken = $jwtAuth->checkToken($hash);

        if ($checkToken) {

            //Recoger parametros POST//
            $json = $request->input('json', null);
            $params = json_decode($json);
            $array_params = json_decode($json, true);

            //Validar la informacion (Usada para apis)
            $validate = \Validator::make($array_params, [
                'title' => 'required|min:5',
                'description' => 'required',
                'price' => 'required',
                'status' => 'required',
            ]);

            if ($validate->fails()) {
                return response()->json($validate->errors(), 400);
            }

            //Actualizar el coche
            $car = Car::where('id', $id)->update($array_params);

            $data = array(
                'car' => $params,
                'status' => 'success',
                'code' => 200
            );


        } else {
            //devolver un error
            $data = array(
                'message' => 'login incorrecto',
                'status' => 'error',
                'code' => 400
            );
        }

        return response()->json($data, 200);
    }


    public function store(Request $request)
    {
        $hash = $request->header('Authorization', null);
        $jwtAuth = new JwtAuth();

        $checkToken = $jwtAuth->checkToken($hash);

        if ($checkToken) {
            //Recojer datos por post
            $json = $request->input('json', null);
            //Decodificar el json
            $params = json_decode($json);
            //decodificar el json pero en un array para uso en la validacion
            $array_params = json_decode($json, true);

            //Conseguir datos del usuario identificado
            $user = $jwtAuth->checkToken($hash, true);


            /**
             *
             * Validar la informacion (No recomendada para apis)
             * $request->merge($array_params);
             * try {
             * $validate = $this->validate($request, [
             * 'title' => 'required|min:5',
             * 'description' => 'required',
             * 'price' => 'required',
             * 'status' => 'required',
             * ]);
             * } catch (\Illuminate\Validation\ValidationException $e) {
             * $e->getResponse();
             * }
             **/

            //Validar la informacion (Usada para apis)
            $validate = \Validator::make($array_params, [
                'title' => 'required|min:5',
                'description' => 'required',
                'price' => 'required',
                'status' => 'required',
            ]);

            if ($validate->fails()) {
                return response()->json($validate->errors(), 400);
            }


            //Guardar el coche
            $car = new Car;
            $car->user_id = $user->sub;
            $car->title = $params->title;
            $car->description = $params->description;
            $car->price = $params->price;
            $car->status = $params->status;
            $car->save();

            $data = array(
                'car' => $car,
                'status' => 'success',
                'code' => 200
            );


        } else {
            //devolver un error
            $data = array(
                'message' => 'login incorrecto',
                'status' => 'error',
                'code' => 400
            );
        }


        return response()->json($data, 200);

    }


    public function destroy($id, Request $request)
    {
        $hash = $request->header('Authorization', null);

        $jwtToken = new JwtAuth();
        $checkToken = $jwtToken->checkToken($hash);

        if ($checkToken) {
            //Comprobar que existe el registro
            $car = Car::find($id);

            //Borrarlo
            $car->delete();

            //Retornar el registro borrado
            $data = array(
                'car' => $car,
                'message' => 'Registro Eliminado',
                'status' => 'success'
            );
        } else {
            $data = array(
                'message' => 'Login Incorrecto',
                'status' => 'error',
                'code' => 400
            );
        }
 

        return response()->json($data, 200);
    }
}
