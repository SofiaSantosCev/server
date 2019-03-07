<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Validator;
use \Firebase\JWT\JWT;
use Auth;
use Mail;
use Hash;

class UserController extends Controller
{
    const ROLE_ID = 2;
    const TOKEN_KEY = 'bHH2JilgwA3YxOqwn';

//El usuario se registra
public function register(Request $request)
{
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    
    //Comprueba que no haya campos vacíos
    if(Validator::isStringEmpty($email) or Validator::isStringEmpty($name) or Validator::isStringEmpty($password))
    {
        return parent::response("The text fields cannot be empty",400);
    }
    
    //Comprueba que el email no esté en uso
    if (self::isEmailInUse($email)) 
    {
        return parent::response("The email already exists", 400); 
    }
    
    //mínimo 8 caracteres en la contraseña
    if(Validator::reachesMinLength($password, 8))
    {
        return parent::response("Invalid password. It must be at least 8 characters long.", 400); 
    }

    $user = new User;
    $user->name = $name;
    $user->email = $email;

    if (isset($rol_id)){
      $user->rol_id = $rol_id;
    } else {
      $user->rol_id = self::ROLE_ID;
    }

    $user->password = password_hash($user->password, PASSWORD_DEFAULT);
    $user->save();

    //Si queremos loguearnos directamente sin pasar por el login
    $token = self::generateToken($email, $password);
        return response()->json ([
            'token' => $token,
            'role_id' => $user->role_id
        ]);
}

    //Logea al usuario que coño pasa
    public function login()
    {
        $email = $_POST['email'];
        $password = $_POST['password'];

        if (Validator::isStringEmpty($email) or Validator::isStringEmpty($password)) {
            return parent::response("All fields have to be filled",400);
        }
        
        $user = User::where('email', $email)->first();
        $id = $user->id;
        $role_id = $user->rol_id;
        
        if ($user->email == $email and password_verify($password, $user->password))
        {
            $token = self::generateToken($email, $password);
            
            return response()->json([
                'token' => $token,
                'user_id'=> $id, 
                'role_id' => $role_id
            ]);

        } else {
            return parent::response("You don't have access",400); 
        }
    }

    //Genera el token con los datos introducidos
    protected function generateToken($email, $password)
    {
        $dataToken = [
            'email' => $email,
            'password' => $password,
            'random' => time()
        ];

        $token = JWT::encode($dataToken, self::TOKEN_KEY);         
        return $token;
    }

    //Comprueba si el email ya está utilizado
    private function isEmailInUse($email)
    {
      $users = User::where('email', $email)->get();
      foreach ($users as $user) 
      {
            if($user->email == $email)
            {
                return true;
            }
        }  
    }

    public function deleteUser()
    {
        if (parent::checkLogin())
        {
            $user = parent::getUserFromToken();
            $user->delete();
            return parent::response('Su cuenta ha sido eliminada.', 200);
        }
        else 
        {
            return parent::response('Ha ocurrido un error con su sesión.', 301);
        }
        
    }

}