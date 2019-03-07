<?php
namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use App\Validator;
use Illuminate\Support\Facades\Input;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() //shows all categories 
    {
        if(parent::checkLogin()){
             
            return response()->json([
                'categories' => Category::where('user_id', parent::getUserfromToken()->id)->get()
            ]);
            
        } else {
            return parent::response("You have to login", 403);
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!parent::checkLogin())
        {
            return parent::response("You don't have permission",403);
        }
        
        $name = $_POST['name'];
        $user_id =  parent::getUserfromToken()->id;

        if (!Validator::hasOnlyOneWord($name)) {
            return parent::response("The name of the category can't have any blank space",400);
        }

        if (Validator::isStringEmpty($name)) {
            return parent::response("The name of the category is empty",400);
        }

        if(Validator::exceedsMaxLength($name, 50))
        {
            return parent::response("The name is too long",400);
        }

        if (self::isCategoryNameInUse($name, $user_id))
        {
            return parent::response("This category already exists",400);
        }

        $category = new Category;

        $category->name = $name;
        $category->user_id = $user_id;
        
        $category->save();

        return parent::response("Category created",200);

        }

        private function isCategoryNameInUse($name, $userId)
        {           
            $categories = Category::where('user_id', $userId)->get();
            foreach ($categories as &$category) 
            {
                if ($category->name == $name) 
                {
                    return true; 
                }
            }
        }

    /**
     * Display the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
         if (!parent::checkLogin())
        {
            return parent::response("You don't have permission",403);
        }

        
        $name = $request->name;

        $user = parent::getUserFromToken();
        $user_id = $user->id; 
            
        $category = Category::where('id',$id)->first();

        if (self::isCategoryNameInUse($name, $user_id) and $category->name != $name)
        {
            return parent::response("This category already exists",400); 
        }
        
        if (!Validator::hasOnlyOneWord($name))
        {
            return parent::response("The name of the category can't have any blank space",400); 
        }

        if (Validator::isStringEmpty($name))
        {
            return parent::response("The name of the category is empty",400); 
        }

        if(Validator::exceedsMaxLength($name, 50)){
            return parent::response("The name is too long",400);
        }

        $category->name = $name;
        $category->update($request->all());
        return parent::response("Category modified", 200);
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        if (!parent::checkLogin())
        {
            return parent::response('There is a problem with your session',301);
        }
        
        $category->delete();
        return parent::response('Category deleted', 200);
    }
}
