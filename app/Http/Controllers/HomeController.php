<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use App\Models\Customer;
use App\Models\User;

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
        // $this->middleware(['auth'],['except'=>['demo']]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {   
        $data['custcount'] = Customer::where('status',1)->get()->count();
        $data['admincount'] = User::where('status',1)->where('user_role',2)->get()->count();
        $data['usercount'] = User::where('status',1)->where('user_role',3)->get()->count();
        return view('dashboard.index',$data);
    }

    public function demo()
    {
        return view('dashboard.index');
    }
}
