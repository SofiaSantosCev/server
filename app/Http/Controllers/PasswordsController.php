<?php

namespace App\Http\Controllers;

use App\Passwords;
use Illuminate\Http\Request;
use \Firebase\JWT\JWT;
use App\User;
use App\Category;

class PasswordsController extends Controller
{
    const SECRETKEY = 'dalfjasdnfljksad';
        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!parent::checkLogin()){
            return parent::response('Ha ocurrido un error con su sesión', 301);
        }

        //Cambio prueba repo
        $user = parent::getUserFromToken();
        $user_id = $user->id;       
        
        $passwords = Passwords::where('user_id', $user_id)->get();
      
        return response()->json ([
                'passwords' => $passwords,
                ],200);
    }

 private function isPasswordsEmpty($userId)
    { 
        if(Passwords::where('user_id', $userId)->first()) 
        {
            return false;
        } else {
            return true; 
        }        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $title = $request->title;
        $password = $request->password;
        $category = $request->category_id;

        if (!parent::checkLogin())
        {
            return parent::response("You don't have permission",403);
        }

        if (!ctype_graph($title)) {
            return response("The title of the password cannot have any blank spaces", 400); 
        }

        if (empty($title)) {
            return response("The title of the password is empty", 400); 
        }

        $passwords = Passwords::where('user_id', parent::getUserfromToken()->id)->first();

        if ($passwords != null) {
            if ($title == $passwords->title) {
                return parent::response("This password already exists",400); 
            }
        }

        $newPassword = new Passwords;
        
        $newPassword->user_id = parent::getUserfromToken()->id;
        $newPassword->title = $request->title;
        $newPassword->password = self::encodePassword($request->password);
        
        if(isset($category)){
            if($category == "none"){
                $category = null;
            }else {
                $findCategory = Category::where('name',$category)->first();
                $newPassword->category_id = $findCategory->id;

            }
        }
        

        $newPassword->save();

        return parent::response("Password created",200);
            
    }   


    /**
     * Display the specified resource.
     *
     * @param  \App\Passwords  $passwords
     * @return \Illuminate\Http\Response
     */
    public function show(Passwords $passwords)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Passwords  $passwords
     * @return \Illuminate\Http\Response
     */
    public function edit(Passwords $passwords)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Passwords  $passwords
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Passwords $password)
    {
        if(parent::checkLogin() == false){
            return parent::response("There is a problem with your session",301);
        }

        $newCategory = Category::where('name',$request->category_id)->first();
        $password->category_id = $newCategory;

        $password->update($request->all());
        return parent::response("Password modified", 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Passwords  $passwords
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!parent::checkLogin()) 
        {
            return parent::response('Ha ocurrido un problema con su sesión.',301);
        }
        Passwords::where('id',$id)->first()->delete();
        return parent::response("password deleted", 200);
            
    }

    protected function encodePassword($password)
    {
        $hash = $hash = openssl_encrypt($password, "AES-128-ECB", self::SECRETKEY);
        return $hash;
    }

    protected function decodePassword($hash)
    {
        $password = openssl_decrypt($hash, "AES-128-ECB", self::SECRETKEY);
        return $password;
    }

    
}
