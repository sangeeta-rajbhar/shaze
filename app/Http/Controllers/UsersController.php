<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasRoles;
use App\Models\User;
use Auth;
use DB;
use Hash;
use Carbon\Carbon;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

  
    public function index(Request $request)
    {
        $name = $request->name;
        $orderby = $request->orderby;
        $search =  $request->search;
        $query = User::select('*')->where('status',1)->where('id','>',1);
        if(isset($name) && !empty($name) && isset($orderby) && !empty($orderby)) {
            $query = $query->orderBy($name, $orderby);
        } else {
            $query = $query->orderBy('id', 'DESC');
        }
        if(isset($search) && !empty($search)) {
            $query->where('name','LIKE','%'.$search.'%'); 
        }
        $result = $query->paginate(config('constants.PAGINATEVALUE'))->onEachSide(config('constants.PAGINATE_onEachSide'));
        $data['users'] = $result;
    
        return view('users.index',$data);
    }


    public function create()
    {
        $data['roles']  = Role::select('id','name','guard_name')->get();
        return view('users.add',$data);
    }


 
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'role' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required|min:6',
            'confirm_password' => 'required|min:6|same:password',
        ]);

        $data = array(
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'user_role' => $request->input('role'),
            "created_by"=>Auth::id(),
            "created_at"=>Carbon::now(),
        );

        $roles = config('constants.ROLES');

        $user = User::create($data);
        $user->assignRole($roles[$request->input('role')]);
        
        return redirect('users')->with('message','User created successfully');

    }
  
    public function edit($id)
    {
        $data['user_data'] = User::find($id);
        if(isset($data['user_data']) && !empty($data['user_data'])){
            $data['roles']  = Role::select('id','name','guard_name')->get();
            return view('users.edit', $data);
        } else {
            return redirect('/users')->with('alert','User not Found');
        }
        
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'confirm_password' => 'same:password',
        ]);

        $user = User::find($request->input('user_id'));

        if(isset($user) && !empty($user)){

            if($user->email !== $request->input('email')):
                $email_exist = User::where('email',$request->input('email'))->first();
                if(isset($email_exist) && !empty($email_exist)){
                    return redirect('/users')->with('alert','Email Id is already taken');
                }
            endif;

            $data = array(
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'user_role' => $request->input('role'),
                "updated_by"=>Auth::id(),
                "updated_at"=>Carbon::now(),
            );

            if(null !== $request->input('password') && $request->input('password') == $request->input('confirm_password')){
                $data['password'] = Hash::make($request->input('password'));
            } 
            
            $roles = config('constants.ROLES');

            $user->removeRole($roles[$user->user_role]);
            
            $user->update($data);

            $user->assignRole($roles[$request->input('role')]);

            return redirect('/users')->with('message','User Updated successfully');
            
        } else {
            return redirect('/users')->with('alert','User not Found');
        }
   
     }
    
    public function destroy($id)
    {
        $userdata = User::find($id);
        if(isset($userdata) && !empty($userdata)){

            $data = array(
                'status' =>0,
                "updated_by"=>Auth::id(),
                "updated_at"=>Carbon::now(),
            );

            $userdata->update($data);

            return redirect('/users')->with('message','User deleted successfully');

        } else {
            return redirect('/users')->with('alert','User not Found');
        }
    }
}
