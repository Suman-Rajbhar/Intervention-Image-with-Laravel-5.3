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
    public function add_a_file()
    {
       
            $content = view('admin.add_a_file_page');
            return view('admin.admin_master')
                ->with('content', $content)
                ;        
    }

    public function save_a_file(Request $request)
    {
        
            $files = $request->file('image'); // multiple files
            $count_files = count($files); // count total files

            if($count_files > 0){
                foreach($files as $file){
                    // single file
                    $rules = array('file'=>'required|image|mimes:jpeg,png,jpg,gif,svg|max:1024');
                    $validator = Validator::make(array('file'=> $file), $rules);
                    if($validator->passes()){
                        $destination_path = 'images';
                        $input['imagename'] = "image-".$file->getClientOriginalName();
                        $img = Image::make($file->getRealPath());
                        $img->resize(50, 30, function ($constraint) {$constraint->aspectRatio();
                        })->save($destination_path.'/'.$input['imagename']);
                    }else{
                        return Redirect::to('/add-a-file')->withInput()->withErrors($validator);
                    }
                }
            }
            else{
                Session::flash('message', 'Please insert file');
                return Redirect::to('/add-a-file');
            }
            
            return back()->with('success','Images Uploaded successfully.');
        }

}
