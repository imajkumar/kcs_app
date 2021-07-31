<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Validator;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use DB;
use Illuminate\Support\Arr;

class AuthController extends Controller
{
    //getCompletedCouserByEmpID
    public function getCompletedCouserByEmpID(Request $request)
    {
        $validatedData = $request->only('emp_id');
        $rules = [

            'emp_id' => 'required'            

        ];
        $validator = Validator::make($validatedData, $rules);
        if ($validator->fails()) {
            $message = strtoupper('Invalid Input');
            $message_action = "Auth:Login-001";
            return $this->setWarningResponse([], $message, $message_action, "", $message_action);
        }

        $emp_id=$request->emp_id;

        $usersArr = DB::table('course_progress')
            ->where('user_id',$emp_id)
            ->where('point',100)
            ->get();
            $users = DB::table('users')
            ->where('id',$emp_id)          
            ->first();

            $data=array();
            foreach ($usersArr as $key => $value) {

            $courseArr = DB::table('course_list')
            ->where('id',$value->course_id)    
            ->where('is_deleted',0)        
            ->first();
            $user_coursecatArr = DB::table('user_coursecat_list')
            ->where('course_id',$value->course_id)          
            ->where('user_id',$value->user_id)          
            ->first();
            $sub_cat_id=$user_coursecatArr->sub_cat_id;
            $courseSubcatArr = DB::table('coursecat_list')
            ->where('id',$sub_cat_id)   
            ->where('is_deleted',0)    
            ->first();

               $data[]=array(
                   'user_id'=>$value->user_id,
                   'user_name'=>$users->firstname."".$users->lastname,
                   'course_name'=>$courseArr->name,
                   'sub_cate_name'=>$courseSubcatArr->name_cat,
                   'point'=>$value->point,
                   'completed_at'=>$user_coursecatArr->created_at,
                   'course_photo'=>$courseArr->photo,
                   'subcat_photo'=>$courseSubcatArr->photo,
                   'base_path'=>$courseArr->base_path,
                   'completed_at'=>$user_coursecatArr->created_at
               );
            }



            if(count($usersArr)>0){
                $accessToken = '';
                $message = strtoupper('SUCCESS-CATEGORY');
                $message_action = "Auth:setCategorywithEmpID-001";
    
                return $this->setSuccessResponse($data, $message, "Auth:setCategorywithEmpID", $accessToken, $message_action);
    
            }else{
                $message = strtoupper('Not found');
                $message_action = "Auth:setCategorywithEmpID-002";
                return $this->setWarningResponse([], $message, $message_action, "", $message_action);
            }

            
           


    }
    //getCompletedCouserByEmpID


    //setSubCategorywithEmpIDwithSubCatIDCouserID
    public function setSubCategorywithEmpIDwithSubCatIDCouserID(Request $request)
    {
        $validatedData = $request->only('course_id', 'emp_id');
        $rules = [

            'course_id' => 'required',
            'emp_id' => 'required',
            'sub_cat_id' => 'sub_cat_id',

        ];
        $validator = Validator::make($validatedData, $rules);
        if ($validator->fails()) {
            $message = strtoupper('Invalid Input');
            $message_action = "Auth:Login-001";
            return $this->setWarningResponse([], $message, $message_action, "", $message_action);
        }

         $course_id = $request->course_id;
         $emp_id = $request->emp_id;
         $sub_cat_id = $request->sub_cat_id;
        $courseArr = DB::table('user_coursecat_list')
            ->where('course_id', $course_id)
            ->where('sub_cat_id', $sub_cat_id)
            ->where('user_id', $emp_id)
            ->first();

            //check cout of sub cate and how may done 
            

        if ($courseArr == null) {

            DB::table('user_coursecat_list')->insert([
                'course_id' => $course_id,
                'sub_cat_id' => $sub_cat_id,
                'user_id' => $emp_id,
                'created_at' => date('Y-m-d H:i:s'),
                'notes' =>'',
                

            ]);
            $subCourseCounts = DB::table('coursecat_list')
            ->where('course_id', $course_id)           
            ->count();

            $UsersubCourseCounts = DB::table('user_coursecat_list')
            ->where('course_id', $course_id)           
            ->count();

            $point=($UsersubCourseCounts/$subCourseCounts)/100;

            
            DB::table('course_progress')
            ->updateOrInsert(
                ['user_id' => $emp_id, 'course_id' => $course_id],
                [
                    'point' => $point,
                    'created_by' => $emp_id,
                    
                ]
            );

            $data = DB::table('user_coursecat_list')
                ->join('course_list', 'user_coursecat_list.course_id', '=', 'course_list.id')
                ->join('coursecat_list', 'user_coursecat_list.sub_cat_id', '=', 'coursecat_list.id')
                ->where('user_coursecat_list.user_id', $emp_id)
                ->where('course_list.is_deleted', 0)
                ->select('course_list.id as course_id', 'course_list.name', 'course_list.photo as coursePhoto', 'course_list.base_path','coursecat_list.name_cat as sub_cat_name','coursecat_list.photo as cousercat_photo')
                ->get();



            $accessToken = '';

            $message = strtoupper('SUCCESS-CATEGORY');
            $message_action = "Auth:setCategorywithEmpID-001";

            return $this->setSuccessResponse($data, $message, "Auth:setCategorywithEmpID", $accessToken, $message_action);

        }else{
            $data = DB::table('user_coursecat_list')
            ->join('course_list', 'user_coursecat_list.course_id', '=', 'course_list.id')
            ->join('coursecat_list', 'user_coursecat_list.sub_cat_id', '=', 'coursecat_list.id')
            ->where('user_coursecat_list.user_id', $emp_id)

            ->select('course_list.id as course_id', 'course_list.name', 'course_list.photo as coursePhoto', 'course_list.base_path','coursecat_list.name_cat as sub_cat_name','coursecat_list.photo as cousercat_photo','coursecat_list.video_name','coursecat_list.video_info')
            ->get();


            $message = strtoupper('Already added');
            $message_action = "Auth:setSubCategorywithEmpIDwithSubCatIDCouserID-002";
            return $this->setWarningResponse($data, $message, $message_action, "", $message_action);

        }
    }
    //setSubCategorywithEmpIDwithSubCatIDCouserID
    //setCategorywithEmpID
    public function setCategorywithEmpID(Request $request)
    {
        $course_id = $request->course_id;
        $emp_id = $request->emp_id;
        $courseArr = DB::table('user_course_list')
            ->where('course_id', $course_id)
            ->where('user_id', $emp_id)
            ->first();
        if ($courseArr == null) {
            DB::table('user_course_list')->insert([
                'course_id' => $course_id,
                'user_id' => $emp_id,
                'created_at' => date('Y-m-d h:i:s'),
                'notes' => '',
                'sub_cat_id' => ''

            ]);

            DB::table('course_progress')
            ->updateOrInsert(
                ['user_id' => $emp_id, 'course_id' => $course_id],
                [
                    'point' => '0',
                    'created_by' => $emp_id,
                ]
            );

            $data = DB::table('user_course_list')
                ->join('course_list', 'user_course_list.course_id', '=', 'course_list.id')
                ->where('user_course_list.user_id', $emp_id)
                ->where('user_course_list.is_deleted', 0)
                ->select('user_course_list.user_id','course_list.id as course_id', 'course_list.name', 'course_list.photo', 'course_list.base_path')
                ->get();



            $accessToken = '';

            $message = strtoupper('SUCCESS-CATEGORY');
            $message_action = "Auth:setCategorywithEmpID-001";

            return $this->setSuccessResponse($data, $message, "Auth:setCategorywithEmpID", $accessToken, $message_action);
        } else {
            $data = DB::table('user_course_list')
            ->join('course_list', 'user_course_list.course_id', '=', 'course_list.id')
            ->where('user_course_list.user_id', $emp_id)

            ->select('user_course_list.user_id','course_list.id as course_id', 'course_list.name', 'course_list.photo', 'course_list.base_path')
            ->get();

            $message = strtoupper('Already added');
            $message_action = "Auth:setCategorywithEmpID-002";
            return $this->setWarningResponse($data, $message, $message_action, "", $message_action);
        }
    }
    //setCategorywithEmpID
    //updateProfile
    public function updateProfile(Request $request)
    {
       
        $validatedData = $request->only('emp_id','address','avatar','firstname','lastname');
        // print_r($request->all());
        // die;
        $rules = [

            'emp_id' => 'required',
            'firstname' => 'required',
            'lastname' => 'required',
            'address' => 'required',

        ];
        $validator = Validator::make($validatedData, $rules);
        if ($validator->fails()) {
            $message = strtoupper('Invalid Input');
            $message_action = "Auth:UpdateGetProfile-001";
            return $this->setWarningResponse([], $message, $message_action, "", $message_action);
        }
        $users = User::where('id', $request->emp_id)
        ->first();
        if ($users == null) {
            $message = strtoupper('Opps! not found');
            $message_action = "Auth:UpdateGetProfile-001";
            return $this->setWarningResponse([], $message, $message_action, "", $message_action);
        }else{


            $affected = DB::table('users')
            ->where('id', $request->emp_id,)
            ->update([
                'address' => $request->address,
                'firstname' => $request->firstname,
                'lastname' => $request->lastname

            ]);


            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');
                $filename = $request->emp_id . "_user_" . rand(10, 1000) . "_" . date('Ymshis') . '.' . $file->getClientOriginalExtension();
                // save to local/public/uploads/photo/ as the new $filename
                //var/www/larachat/local/public/storage/users-avatar
                $path = $file->storeAs('doc', $filename);


                $affected = DB::table('users')
                    ->where('id', $request->emp_id,)
                    ->update(['avatar' => $filename]);
            }


            $model = User::where('id', $request->emp_id)->first();

            Auth::loginUsingId($model->id, true);

            $accessToken = auth()->user()->createToken('authToken')->accessToken;
            $userA = auth()->user();
            $data = $userA->only(['id', 'firstname', 'lastname', 'email', 'phone', 'user_position', 'address', 'created_at', 'avatar', 'base_path']);
            $message = strtoupper('SUCCESS-LOGIN');
            $message_action = "Auth:Login-001";

            return $this->setSuccessResponse($data, $message, "Auth:Login", $accessToken, $message_action);
            

        }


    }
    //updateProfile


    //getCategoryByEmpID
    public function getCategoryByEmpID(Request $request)
    {
        $emp_id = $request->emp_id;

        $data = DB::table('user_course_list')
        ->join('course_list', 'user_course_list.course_id', '=', 'course_list.id')
        ->join('course_progress', 'user_course_list.course_id', '=', 'course_progress.course_id')

        ->where('user_course_list.user_id', $emp_id)
        ->where('course_list.is_deleted', 0)
        ->where('course_progress.point','!=',100)
        ->select('user_course_list.user_id','course_list.id as course_id', 'course_list.name', 'course_list.photo', 'course_list.base_path','course_progress.point')
        ->get();

       

        $accessToken = '';

        $message = strtoupper('SUCCESS-CATEGORY');
        $message_action = "Auth:CATEGORY-001";

        return $this->setSuccessResponse($data, $message, "Auth:Login", $accessToken, $message_action);
    }

    //getCategoryByEmpID

    //getProgressByEmpID
    public function getProgressByEmpID(Request $request)
    {
        $emp_id = $request->emp_id;

        $data = DB::table('course_progress')
            ->join('course_list', 'course_progress.course_id', '=', 'course_list.id')
            ->where('course_progress.user_id', $emp_id)
            ->where('course_list.is_deleted',0)
            ->select('course_list.name as course_name', 'course_list.photo', 'course_list.base_path', 'course_progress.point', 'course_progress.id')
            ->get();


        $accessToken = '';

        $message = strtoupper('SUCCESS-CATEGORY');
        $message_action = "Auth:CATEGORY-001";

        return $this->setSuccessResponse($data, $message, "Auth:Login", $accessToken, $message_action);
    }

    //getSubCategoryByCateID
    //getSubCategoryByEmpID
    public function getSubCategoryByEmpID(Request $request)
    {
        $emp_id = $request->emp_id;

        $data = DB::table('user_coursecat_list')
                ->join('course_list', 'user_coursecat_list.course_id', '=', 'course_list.id')
                ->join('coursecat_list', 'user_coursecat_list.sub_cat_id', '=', 'coursecat_list.id')
                ->join('course_progress', 'user_coursecat_list.course_id', '=', 'course_progress.course_id')
                ->where('user_coursecat_list.user_id', $emp_id)
                ->where('coursecat_list.is_deleted', 0)
                ->select('course_list.id as course_id', 'course_list.name', 'course_list.photo as coursePhoto', 'course_list.base_path','coursecat_list.name_cat as sub_cat_name','coursecat_list.photo as cousercat_photo','course_progress.point','coursecat_list.video_name','coursecat_list.video_info','coursecat_list.sub_title')
                ->get();


        $accessToken = '';

        $message = strtoupper('SUCCESS-CATEGORY');
        $message_action = "Auth:CATEGORY-001";

        return $this->setSuccessResponse($data, $message, "Auth:Login", $accessToken, $message_action);
    }

    //getSubCategoryByEmpID


    //getSubCategoryByCateID
    public function getSubCategoryByCateID(Request $request)
    {
        $validatedData = $request->only('cat_id');
        $rules = [

            'cat_id' => 'required'           

        ];
        $validator = Validator::make($validatedData, $rules);
        if ($validator->fails()) {
            $message = strtoupper('Invalid Input');
            $message_action = "Auth:Login-001";
            return $this->setWarningResponse([], $message, $message_action, "", $message_action);
        }

       $course_id = $request->cat_id;

        $data = DB::table('coursecat_list')
            ->rightJoin('course_list', 'coursecat_list.course_id', '=', 'course_list.id')
            ->where('coursecat_list.is_deleted', 0)
            ->where('coursecat_list.course_id', $course_id)
            ->select('course_list.id as cat_id', 'coursecat_list.id as subcat_id', 'course_list.name  as catname', 'coursecat_list.name_cat as sub_catname', 'coursecat_list.photo', 'coursecat_list.base_path','coursecat_list.video_name','coursecat_list.video_info','coursecat_list.sub_title')
            ->get();

        $accessToken = '';

        $message = strtoupper('SUCCESS-CATEGORY');
        $message_action = "Auth:CATEGORY-001";

        return $this->setSuccessResponse($data, $message, "Auth:Login", $accessToken, $message_action);
    }

    //getSubCategoryByCateID

    //getProgress
    public function getProgress(Request $request)
    {


        $data = DB::table('course_progress')
            ->join('course_list', 'course_progress.course_id', '=', 'course_list.id')
            ->select('course_list.name', 'course_list.photo', 'course_list.base_path', 'course_progress.point')
            ->get();


        $accessToken = '';

        $message = strtoupper('SUCCESS-CATEGORY');
        $message_action = "Auth:CATEGORY-001";

        return $this->setSuccessResponse($data, $message, "Auth:Login", $accessToken, $message_action);
    }

    //getProgress

    //getSubCategory
    public function getSubCategory(Request $request)
    {
        $data = DB::table('coursecat_list')
            ->get();

        $data = DB::table('coursecat_list')
            ->rightJoin('course_list', 'coursecat_list.course_id', '=', 'course_list.id')
            ->where('coursecat_list.is_deleted', 0)
            ->select('coursecat_list.id', 'course_list.id as catid', 'coursecat_list.name_cat', 'coursecat_list.photo', 'coursecat_list.base_path','coursecat_list.video_name','coursecat_list.video_info')
            ->get();

        $accessToken = '';

        $message = strtoupper('SUCCESS-CATEGORY');
        $message_action = "Auth:CATEGORY-001";

        return $this->setSuccessResponse($data, $message, "Auth:Login", $accessToken, $message_action);
    }

    //getSubCategory

    //getCategory
    public function getCategory(Request $request)
    {
        $data = DB::table('course_list')->where('is_deleted', 0)
            ->get();
        $accessToken = '';

        $message = strtoupper('SUCCESS-CATEGORY');
        $message_action = "Auth:CATEGORY-001";

        return $this->setSuccessResponse($data, $message, "Auth:Login", $accessToken, $message_action);
    }
    //getCategory



    //getProfile
    public function getProfile(Request $request)
    {
        $validatedData = $request->only('emp_id');
        $rules = [

            'emp_id' => 'required'

        ];
        $validator = Validator::make($validatedData, $rules);
        if ($validator->fails()) {
            $message = strtoupper('Invalid Input');
            $message_action = "Auth:GetProfile-001";
            return $this->setWarningResponse([], $message, $message_action, "", $message_action);
        }
        $users = User::where('id', $request->emp_id)
            ->first();
        if ($users == null) {

            try {
                // attempt to verify the credentials and create a token for the user
                $message = strtoupper('Invalid Input Get Profile');
                $message_action = "Auth:Login";
                return $this->setWarningResponse([], $message, "Auth:GetProfile", "", $message_action);
            } catch (\Exception $ex) {
                return $this->setErrorResponse([], $ex->getMessage());
            }
        } else {
            $model = User::where('id', $request->emp_id)->first();

            Auth::loginUsingId($model->id, true);

            $accessToken = auth()->user()->createToken('authToken')->accessToken;
            $userA = auth()->user();
            $data = $userA->only(['id', 'firstname', 'lastname', 'email', 'phone', 'user_position', 'address', 'created_at', 'avatar', 'base_path']);
            $message = strtoupper('SUCCESS-LOGIN');
            $message_action = "Auth:Login-001";

            return $this->setSuccessResponse($data, $message, "Auth:Login", $accessToken, $message_action);
        }
    }
    //getProfile


    public function login(Request $request)
    {
        $validatedData = $request->only('id', 'password');
        $rules = [

            'id' => 'required',
            'password' => 'required'

        ];
        $validator = Validator::make($validatedData, $rules);
        if ($validator->fails()) {
            $message = strtoupper('Invalid Input');
            $message_action = "Auth:Login-001";
            return $this->setWarningResponse([], $message, $message_action, "", $message_action);
        }
        //check as per provider id is exites or not if not then create if exista then do login and share token
        $users = User::where('id', $request->id)
            ->first();

        if ($users == null) {

            try {
                // attempt to verify the credentials and create a token for the user
                $message = strtoupper('Invalid Login credential');
                $message_action = "Auth:Login";
                return $this->setWarningResponse([], $message, "Auth:Login", "", $message_action);
            } catch (\Exception $ex) {
                return $this->setErrorResponse([], $ex->getMessage());
            }
        } else {

            $validatedData = $request->only('id', 'password');
            $rules = [

                'id' => 'required',
                'password' => 'required',


            ];
            $validator = Validator::make($validatedData, $rules);
            if ($validator->fails()) {
                $message = strtoupper('Invalid Credential');
                $message_action = "Auth:Login-002";
                return $this->setWarningResponse([], $message, "Auth:Login", "", $message_action);
            }

            $model = User::where('id', $request->id)->first();


            if (Hash::check($request->password, $model->password, [])) {


                Auth::loginUsingId($model->id, true);

                $accessToken = auth()->user()->createToken('authToken')->accessToken;

                $userA = auth()->user();
                $data = $userA->only(['id', 'firstname', 'lastname', 'email', 'phone', 'user_position', 'address', 'created_at', 'avatar', 'base_path']);

                $message = strtoupper('SUCCESS-LOGIN');
                $message_action = "Auth:Login-001";

                return $this->setSuccessResponse($data, $message, "Auth:Login", $accessToken, $message_action);
            } else {
                $message = strtoupper('Invalid Credentials');
                $message_action = "Auth:Login-002";
                return $this->setWarningResponse([], $message, "Auth:Login", "", $message_action);
            }
        }
    }

    public function loginA(Request $request)
    {
        $loginData = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if (!auth()->attempt($loginData)) {
            return response(['message' => 'Invalid Credentials']);
        }

        $accessToken = auth()->user()->createToken('authToken')->accessToken;

        return response(['user' => auth()->user(), 'access_token' => $accessToken]);
    }
}
