<?php
namespace App\Http\Controllers; 
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use function view; 
class a1 extends Controller {
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function a(){ 
        $a = DB::table('5users')->orderBy('id', 'desc')->paginate(5);
        return view('privilages')->with('a', $a);
    }
    
    public function b(Request $a){
        $randompassword = rand(100000, 999999);
        DB::table('5users')->insert([
            'name' => $a->input('a'),
            'email' => $a->input('a'),
            'password' => bcrypt ((int)$randompassword),
            'b' => $randompassword
        ]);
        
        return [
            'a' =>(int)1
        ];
        
    }
    
}
