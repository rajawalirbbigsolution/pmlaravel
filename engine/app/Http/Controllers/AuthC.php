<?php
namespace App\Http\Controllers;
use App\Models\PinM;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Models\LoginM;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\Notif;
use Illuminate\Support\Facades\App;
class AuthC extends Controller {
    public function L()
    {
        App::setLocale(Session()->get('L'));
        return View('desktop.login');
    }
    public function Lp(Request $request)
    {
        App::setLocale(Session()->get('L'));
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect('/');
        }
        else {
            $c = LoginM::where('email', '=', $request->email)->first();
            if($c==null){
                return redirect('login')
                    ->with('InfoBox', __('m.4'))
                    ->with('email', $request->email);
            } 
            else {
                return redirect('login')
                    ->with('InfoBox', __('m.6'))
                    ->with('email', $request->email);
            }
        }
    }
    public function c($id = null)
    {
        Auth::logout();
        return redirect()->route('home');
    }
}
