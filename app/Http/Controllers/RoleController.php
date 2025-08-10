<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
// use Spatie\Permission\Traits\HasRoles;
use Auth;
use DB;


class RoleController extends Controller
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $name = $request->name;
        $orderby = $request->orderby;
        $search =  $request->search;
        $query = Role::select('id','name','guard_name')->where('id','>',1);
        if(isset($name) && !empty($name) && isset($orderby) && !empty($orderby)) {
            $query = $query->orderBy($name, $orderby);
        } else {
            $query = $query->orderBy('id', 'DESC');
        }
        if(isset($search) && !empty($search)) {
            $query->where('name','LIKE','%'.$search.'%'); 
        }
        $result = $query->paginate(config('constants.PAGINATEVALUE'))->onEachSide(config('constants.PAGINATE_onEachSide'));
        $data['roles'] = $result;
    
        return view('roles.list_roles',$data);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permission = Permission::get();
        return view('roles.create_roles',compact('permission'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);

        $role = Role::create(['name' => $request->input('name')]);
        $permis = $request->input('permission');

        foreach($permis as $p){

            $permission = Permission::find($p);

            $role->givePermissionTo($permission);

        }
        // dd($permission);
        // exit;
        // $role->syncPermissions($request->input('permission'));
        // app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect('role')->with('message','Role created successfully');

    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function show($id)
    // {
    //     $role = Role::find($id);
    //     if(isset($role) && !empty($role)){
    //         $rolePermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
    //         ->where("role_has_permissions.role_id",$id)
    //         ->get();
    //         return view('roles.show',compact('role','rolePermissions'));
    //     } else {
    //         return redirect('role')->with('alert','Role Id not Found');
    //     }
        
    // }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::find($id);
        if(isset($role) && !empty($role)){
            $permission = Permission::get();
            $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
                ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
                ->all();
            return view('roles.edit',compact('role','permission','rolePermissions'));
        } else {
            return redirect('role')->with('alert','Role Id not Found');
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'permission' => 'required',
        ]);

        $role = Role::find($id);
        $role->name = $request->input('name');
        if($role->save()){
            $role->syncPermissions($request->input('permission'));
            return redirect('role')->with('message','Role updated successfully');
        } else {
            return redirect()->back()->withInput('alert','Role Failed to update');
        }
   
     }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table("roles")->where('id',$id)->delete();
        return redirect('role')->with('message','Role deleted successfully');
    }
}
