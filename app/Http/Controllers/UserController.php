<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Crypt;
use Auth;

class UserController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(){
        return view('users.index');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendInvitation(Request $request){
        $request->validate([
            'email' => 'required|email|unique:users,email'
        ]);

        Invitation::create([
            'email' => $request->email,
            'link' => '/invitation/'.Crypt::encrypt($request->email)
        ]);

        //Email Code

        return redirect()->route('sendinvitation')->with('status', 'Invite sent!');
    }

    /**
     * @param $enc_email
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function invitation($enc_email,Request $request){
        $email = Crypt::decrypt($enc_email);
        $invite = Invitation::where('email',$email)->first();
        if(!$invite){
            return redirect()->route('login')->withErrors(['Invalid email']);
        }
        return view('users.signup-form',['email' => $email]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function signup (Request $request){
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'user_name' => 'required|min:4|max:20|unique:users,user_name'
        ]);

        $email_verification_code = rand(0,1000000);
        User::create([
            'user_name' => $request->user_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_role' => 'user',
            'email_verification_code' => $email_verification_code,
            'email_verification_flag' => 0,
            'registered_at' => Carbon::now()
        ]);

        return redirect('verification/'.Crypt::encrypt($request->email))->with('status', 'Check email for verification');
    }

    /**
     * @param $enc_email
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function verificationView($enc_email,Request $request)
    {
        $email = Crypt::decrypt($enc_email);
        $user = User::where('email',$email)->first();
        if(!$user){
            return redirect()->route('login')->withErrors(['Invalid email address']);
        }

        return view('users.verification',['email' => $email]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verification(Request $request){
        $request->validate([
            'email_verification_code' => 'required|min:6|max:6',
        ],[
            'email_verification_code.required' => 'The Otp code is required',
            'email_verification_code.min' => 'The Otp code must be at least 6 characters.',
            'email_verification_code.max' => 'The Otp code must not be greater than 6 characters.'
        ]);

        $user = User::where('email',$request->email)->first();
        if(!$user){
            return redirect()->route('login')->withErrors(['Invalid email address']);
        }

        $user->email_verification_flag = 1;
        $user->save();
        return redirect()->route('login')->with('status', 'Email verified successfully, Now you can login the application');

    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function profileView(){

        return view('users.profile',[
            'user' => Auth::user()
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function profileUpdate(Request $request){

        $request->validate([
            'name' => 'required|min:2|max:255',
            'avatar' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048|dimensions:min_width=256,min_height=256,max_width=256,max_height=256',
        ]);

        $user = User::find(Auth::user()->id);
        if(!$user){
            return redirect()->route('login')->withErrors(['User doesn\'t exist']);
        }
        if($request->hasfile('avatar')) {
            $file = $request->file('avatar');
            $avatar = $file->getClientOriginalName();
            $file->move(public_path() . '/uploads/', $avatar);
            $user->avatar = $avatar;
        }
        $user->name = $request->name;
        $user->save();

        return redirect()->route('profile')->with('status', 'Profile updated successfully');
    }
}
