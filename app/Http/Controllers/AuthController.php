<?php

namespace App\Http\Controllers;
// use illuminate\support\Facades\Hash;
use illuminate\support\facades\Auth;
use illuminate\support\facades\session;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\PasswordReset;
use Mail;
use illuminate\support\Str;
use illuminate\support\Facades\URL;
use illuminate\support\Carbon;
class AuthController extends Controller
{
    //
    public function loadRegister(){
        if(Auth::user() && Auth::user()->is_admin==1){
        return redirect('admin/dashboard');
    }
    else if(Auth::user() && Auth::user()->is_admin==0){
    return redirect('/dashboard');
    }
        return view('register');
    }

    public function studentRegister(Request $request){
        // return $request;
    $request->validate([
        'name'=>'string|required|min:3',
        'email'=>'string|required|max:100|unique:users',
        'password'=>'string|required|confirmed|min:8'

    ]);
    $user= new User;
    $user->name=$request->name;
    $user->email=$request->name;
    $user->password = Hash::make($request->password);
    $user->save();

    return back()->with('success','You have been successfully registered');
    }
public function loadLogin()
{
    if(Auth::user() && Auth::user()->is_admin==1){
        return redirect('/admin/dashboard');
    }
    else if(Auth::user() && Auth::user()->is_admin==0){
        return redirect('/dashboard');
    }
    
    return view('/login');
}

public function userLogin(Request $request)
{
    $request->validate(
        [
            'email'=>'string|required|email',
            'password'=>'string|required'
        ]
        );
    $userCredential= $request->only('email','password');
    if(Auth::attempt($userCredential)){
        if(Auth::user()->is_admin==1){
            return redirect('/admin/dashboard');
        }
        else{
            return redirect('/dashboard');
        }

    }
    else{
        return back()->with('Wrong Email or Password');
    }
}

public function loadDashboard(){
    return view('student.dashboard');
}

public function adminDashboard(){
    return view('admin.dashboard');
}

public function logout(Request $request ){
$request->session()->flush();
Auth::logout();
return redirect()->to('/');
}
public function forgetPasswordload(){
    return view('forget-password');
}
public function forgetPassword(Request $request){
    try{
        $user=User::where('email',$request->email)->get();
        if(count($user)>0){
         $token= Str::random(40);
         $domain= url::to('/');
         $url= $domain.'/reset-password?token='.$token;

         $data['url']=$url;
         $data['email']=$request->email;
         $data['title']='Password reset';
         $data['body']='Click on the link to reset your Password';

         Mail::send('forgetPasswordMail',['data'=>$data],function($message) use($data){
            $message->to($data['email'])->subject($data['title']);
         });
         
            $dateTime= Carbon::now()->format('y-m-d H:i:s');
            PasswordReset::updateOrCreate(
                ['email'=>$request->email],
                [
                    'email'=>$request->email,
                    'token'=>$token,
                    'created_at'=>$dateTime
                ]
            );
            return back()->with('success','Please check your mail to reset your password');

        }
        else{
            return back()->with('error','Email does not exist');
        }
    }
    catch(\exception $e){
        return back()->with('error',$e->getMessage());
        
    }
}

public function resetPasswordLoad(request $request){

    $resetdata= PasswordReset::where('token',$request->token)->get();
    if(isset($request->token) && count($resetdata)>0){

        user::where('email',$resetdata[0]['email'])->get();
        return view('resetPassword',compact('user'));
    }
    else{
        return view('404');
    }
}

public function resetPassword(request $request){
    $request->validate([
        'password'=>'required|string|min:8|confirmed'
    ]);
    $user= user::find($request->id);
    $user->password=$request->password;
    $user->save();

    PasswordReset::where('email',$user->email)->delete();
    return '<h2> Your Passowrd Has been reset<h2>';
}
}
