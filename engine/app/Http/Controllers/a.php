<?php
namespace App\Http\Controllers; 
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use function view; 
class a extends Controller {

    public function a(){
        return view('register');
    }
    public function b(Request $a){ 
        if(empty($a->input('a'))){
            return [
                'a' => 'Email kosong',
                'b' => 3
            ];
        }
        else if(empty($a->input('b'))){
            return [
                'a' => 'Password kosong',
                'b' => 4
            ];
        }
        else if(empty($a->input('c'))){
            return [
                'a' => 'No. SMS kosong',
                'b' => 5
            ];
        }
        else{
            $b = DB::table('5users')->where('email', $a->input('a'))->first();
            if(empty($b)){
                DB::table('5users')->insert([
                    'name' => $a->input('a'),
                    'email' => $a->input('a'),
                    'password' => bcrypt ($a->input('b')),
                    'a' => $a->input('c')
                ]);
                return [
                    'a' => 'Akun didaftarkan',
                    'b' => 1,
                    'c' =>  $a->input('a')
                ];
            }
            else {
                return [
                    'a' => 'Email telah terdaftar',
                    'b' => 0
                ];
            }
        }

    }
}
