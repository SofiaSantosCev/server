<?php
namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use \Firebase\JWT\JWT;
use App\User;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected const TOKEN_KEY = 'bHH2JilgwA3YxOqwn';

    protected function findUser($email)
    {
        $user = User::where('email',$email)->first();       
        return $user; 
    }

    //Comprueba si el token es válido
    protected function checkLogin()
    {
        $headers = getallheaders();
        if(!isset($headers['Authorization']))
        { 
            return false;
        }

        $tokenDecoded = self::decodeToken();
        $user = self::getUserFromToken();
        if ($tokenDecoded->password == $user->password and $tokenDecoded->email == $user->email) 
        {
            return true;
        } else {
            return self::response('You dont have permission',301);
        }
    }

    protected function getUserfromToken()
    {
        $tokenDecoded = self::decodeToken();
        $user = self::findUser($tokenDecoded->email);
        return $user;
    }

    //Respuesta personalizable para success o error
    protected function response($text, $code){
        return response()->json([
            'message' => $text
        ],$code);
    }

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

    protected function decodeToken() 
    {
        $headers = getallheaders();
        if(isset($headers['Authorization']))
        {
            $token = $headers['Authorization'];
            $tokenDecoded = JWT::decode($token, self::TOKEN_KEY, array('HS256'));
            return $tokenDecoded;
        }
    }

    protected function encodePassword($password){
        if(!$password){return false;}
        $text = $password;
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->skey, $text, MCRYPT_MODE_ECB, $iv);
        return trim($this->safe_b64encode($crypttext)); 
    }

    protected function decodePassword($password){
        if(!$password){return false;}
        $crypttext = $this->safe_b64decode($password); 
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->skey, $crypttext, MCRYPT_MODE_ECB, $iv);
        return trim($decrypttext);
    }
    
    protected function randomString($size){
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $count = mb_strlen($chars);
        for ($i = 0, $result = ''; $i < $size; $i++) {
            $index = rand(0, $count - 1);
            $result .= mb_substr($chars, $index, 1);
        }
        return $result;
    }

    
}
   	
