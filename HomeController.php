<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use Session;
use Redirect;
use Form;
use DB;
use Validator;
use Illuminate\Support\Facades\Input;
use Image;



class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::user()->role == "Admin"){
            $content = view('admin.home_content');
            return view('admin.admin_master')
                ->with('content', $content)
                ;
        }else {
            Session::flash('message', 'You are not authorized!');
            return Redirect::to('/customer');
        }
    }

    public function add_new_product()
    {
        if(Auth::user()->role == "Admin"){
            $content = view('admin.product_add');
            return view('admin.admin_master')
                ->with('content', $content)
                ;
        }else {
            Session::flash('message', 'You are not authorized!');
            return Redirect::to('/customer');
        }
    }

    public function add_new_category()
    {
        if(Auth::user()->role == "Admin"){
            $content = view('admin.category_add');
            return view('admin.admin_master')
                ->with('content', $content)
                ;
        }else {
            Session::flash('message', 'You are not authorized!');
            return Redirect::to('/customer');
        }
    }

    public function save_new_category(Request $request)
    {
        if(Auth::user()->role == "Admin"){

            $cat_name = $request->input('cat_name');

            $rules = array(

                'cat_name' => 'required|unique:categorys,category_name|max:5',
                'cat_details' => 'required|unique:categorys,category_name|max:5'

            );

            $msg = array(
                'cat_name.required' => 'Category Name is required!',
                'cat_details.required' => 'Details Name is required!'
            );

            $validator = Validator::make($request->all(), $rules, $msg);

            if($validator->fails()) {
                return Redirect::to('add-new-category')->withErrors($validator)->withInput();
            } else {
                DB::table('categorys')->insert(
                    [
                        'category_id' => "CAT-".rand(00,999),
                        'category_name' => $cat_name,
                        'entry_by' => Auth::user()->role,
                        'created_at' => date('Y-m-d h:i:s A'),
                        'updated_at' => date('Y-m-d h:i:s A'),
                        'status' => 1,
                    ]
                );

                Session::flash('message', 'Category added Succesfully!');
                return Redirect::to('/add-new-category');
            }



        }else {
            Session::flash('message', 'You are not authorized!');
            return Redirect::to('/customer');
        }
    }

    public function save_pro_picx(Request $request)
    {
        if(Auth::user()->role == "Admin"){


            $files = $request->file('mytext');
//            $files = Input::file('mytext');

            $count = count($files);
            $limit = 0;

//            echo "<pre>";
//            print_r($_FILES['mytext']['size']);
//            exit;
            $rules = array('mytext'=>'required');
            $msg = array(
                'mytext.required' => 'Category Name is required!',
//                    'cat_details.required' => 'Details Name is required!'
            );
            $validator = Validator::make($request->all(), $rules, $msg);

            if($_FILES['mytext']['size'][0] > 0){
                foreach($files as $file){


//                echo "<pre>";
//                print_r($file);
                    $rules = array('file'=>'required');
//                $rules = array(
//                    'cat_name' => 'required|unique:categorys,category_name|max:5',
//                    'cat_details' => 'required|unique:categorys,category_name|max:5'
//                );

                    $msg = array(
                        'file.required' => 'Category Name is required!',
//                    'cat_details.required' => 'Details Name is required!'
                    );
//                $validator = Validator::make(array('file'=>$file), $rules);
                    $validator = Validator::make($request->all(), $rules, $msg);
//
                    if($validator->passes()){

//                    $destination = 'UpImage';
//                    $imageName = time().'.'.$file->getClientOriginalExtension();
                        $imageName = $file->getClientOriginalName();
                        $file->move(public_path('images'), $imageName);
                        $limit++;
//
                    }

                }
            }else{
                return Redirect::to('add-new-product')->withErrors($validator);
            }

//            exit;




//            foreach($files as $file){
//                $rules = array('file'=>'required');
//                $validator = Validator::make(array('file'=>$file), $rules);
//                if($validator->passes()){
//                    $imageName = $file->getClientOriginalName();
//                    $file->move(public_path('images'), $imageName);
//                    $limit++;
//                }
//            }



            if($limit == $count){
                return back()
                ->with('success','Image Uploaded successfully.');
//                ->with('path',$imageName);
            }else{
                return Redirect::to('add-new-product')->withErrors($validator);
            }
//            print_r($_FILES);
//            exit;
//
//            $file->store('avatar/', Auth::user()->id, 'avt.jpg');
//
//            return back();

//            $this->validate($request, [
//                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:1000',
//            ]);

//            foreach($files as $key => $file){

//                $imageName = time().'.'.$file->getClientOriginalExtension();
//                $file->move(public_path('images'), $imageName);
//            }


//            return back()
//                ->with('success','Image Uploaded successfully.')
//                ->with('path',$imageName);



        }else {
            Session::flash('message', 'You are not authorized!');
            return Redirect::to('/customer');
        }
    }

    public function save_pro_pic()
    {
        if(Auth::user()->role == "Admin"){

            $files = Input::file('mytext');
            $file_count = count($files);
            $up_count = 0;

            if($file_count > 0){
                foreach($files as $file){
                    $rules = array('file'=>'required|image|mimes:jpeg,png,jpg,gif,svg|max:1000');
                    $validator = Validator::make(array('file'=> $file), $rules);
                    if($validator->passes()){

                        $destination_path = 'images';
                        $file_name = $file->getClientOriginalName();
                        $upload_success = $file->move($destination_path, $file_name);
                        $up_count++;

                        DB::table('gallerys')->insert(
                            ['category_name' => $file_name ]
                        );
                    }else{
                        return Redirect::to('add-new-product')->withInput()->withErrors($validator);
                    }
            }


            }else{
                return back()->with('success','NO');
            }

            if($up_count == $file_count){
                return back()->with('success','Image Uploaded successfully.');
            }else{
                return Redirect::to('add-new-product')->withInput()->withErrors($validator);
            }

        }else {
            Session::flash('message', 'You are not authorized!');
            return Redirect::to('/customer');
        }
    }


    public function add_a_category()
    {
        if(Auth::user()->role == "Admin"){


            $content = view('admin.add_a_cat_page');
            return view('admin.admin_master')
                ->with('content', $content)
                ;


        }else {
            Session::flash('message', 'You are not authorized!');
            return Redirect::to('/customer');
        }
    }


    public function save_a_category(Request $request)
    {
        if(Auth::user()->role == "Admin"){

            $cat_name = $request->input('cat_name');

            $rules = array(
                'cat_name' => 'required|unique:categorys,category_name',
                'cat_id' => 'required',
            );

            $messages = array(
                'cat_name.required' => 'Category is required.',
                'cat_id.required' => 'Category ID is required.'
            );

            $validator = Validator::make($request->all(), $rules, $messages);

            if($validator->fails()) {
                return Redirect::to('/add-a-category')->withErrors($validator)->withInput();
            }






            DB::table('categorys')->insert(
                [
                    'category_id' => "CAT-".rand(00,999),
                    'category_name' => $cat_name,
                    'entry_by' => Auth::user()->role,
                    'created_at' => date('Y-m-d h:i:s A'),
                    'updated_at' => date('Y-m-d h:i:s A'),
                    'status' => 1,
                ]
            );

            Session::flash('message', 'Successfully Category inserted!');
            return Redirect::to('/add-a-category');


        }else {
            Session::flash('message', 'You are not authorized!');
            return Redirect::to('/customer');
        }
    }

    public function add_a_file()
    {
        if(Auth::user()->role == "Admin"){


            $content = view('admin.add_a_file_page');
            return view('admin.admin_master')
                ->with('content', $content)
                ;


        }else {
            Session::flash('message', 'You are not authorized!');
            return Redirect::to('/customer');
        }
    }

    public function save_a_file(Request $request)
    {
        if(Auth::user()->role == "Admin"){



            $files = Input::file('image'); // multiple file
//            $files = $request->file('image'); // multiple file
            $count_files = count($files);

            if($count_files > 0){
                foreach($files as $file){
                    // single file
                    $rules = array('file'=>'required|image|mimes:jpeg,png,jpg,gif,svg|max:1000');
                    $validator = Validator::make(array('file'=> $file), $rules);
                    if($validator->passes()){

                        $input['imagename'] = "image-".rand(000,6666).$file->getClientOriginalName();
                        // file rename here



                        $destinationPath = public_path('/images'); // save folder

                        $img = Image::make($file->getRealPath());
                        $img->rotate(-90);
//                        $img->text('hello', 120, 100);
//                        $img->text('foo', 0, 0, function($font) {
//                            $font->size(24);
//                            $font->color('#fdf6e3');
//                            $font->align('center');
//                            $font->valign('top');
//                            $font->angle(45);
//                        });

                        $img->resize(500, 500, function ($constraint) {
                            $constraint->aspectRatio();
                        })
                            ->save($destinationPath.'/'.$input['imagename']);

                        DB::table('gallerys')->insert(
                            [
                                'category_name' => "images/".$input['imagename'],
                                'entry_by' => Auth::user()->role,
                                'created_at' => date('Y-m-d h:i:s A'),
                                'updated_at' => date('Y-m-d h:i:s A'),
                                'status' => 1,
                            ]
                        );
                    }else{
                        return Redirect::to('/add-a-file')->withInput()->withErrors($validator);
                    }
                }
            }

            else{
                Session::flash('message', 'Please insert file');
                return Redirect::to('/add-a-file');
            }


            return back()
                ->with('message','Image Upload successful');


//            exit;


//            $rules = array('file'=>'required|mimes:docx');
//            $validator = Validator::make(array('file'=> $file), $rules);
//            if($validator->passes()) {
//                $file_name = $file->getClientOriginalName();
//
//                $file->move(public_path('images'), $file_name);
//
//                Session::flash('message', 'File uploaded!');
//                return Redirect::to('/add-a-file');
//
//            }else{
//                return Redirect::to('/add-a-file')->withErrors($validator)->withInput();
//            }



        }else {
            Session::flash('message', 'You are not authorized!');
            return Redirect::to('/customer');
        }
    }


}
