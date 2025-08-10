<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Eloquent;

class Employee extends Eloquent 
{

    protected $table = 'employee';
    protected $primaryKey = 'employee_id';


     protected $fillable = [
        'employee_code','employee_unique_id','employee_name','mobile_no','whatsapp_same_mobile_no','whatsapp_no','address','market_id','region_id','state_id','city_id','description','expertise','photo_name','review_star','status','import_image_url','created_by','created_at','updated_by','updated_at',
     ];
     
 
     public static function GetEmployeeList($name=null,$orderby=null,$search=null){
       
        $query = Employee::select('employee.*','cities.city_name','cities.pincode','states.state_name','region.region_name','market.market_name')
        ->leftjoin('market','market.market_id','employee.market_id')
        ->leftjoin('cities','cities.city_id','employee.city_id')
        ->leftjoin('states','states.state_id','employee.state_id')
        ->leftjoin('region','region.region_id','employee.region_id')
        ->where('employee.status',1);
       
        if(isset($name) && ! empty($name) && isset($orderby) && ! empty($orderby)) {
            $query = $query->orderBy($name, $orderby);
        };
        if(isset($search) && !empty($search)) {
            $query->where(function ($querys) use ($search) {
                $querys->where('market.market_name','LIKE','%'.$search.'%')
                ->orWhere('cities.city_name','LIKE','%'.$search.'%')
                ->orWhere('cities.pincode','LIKE','%'.$search.'%')
                ->orWhere('region.region_name','LIKE','%'.$search.'%')
                ->orWhere('states.state_name','LIKE','%'.$search.'%')
                ->orWhere('employee.employee_name','LIKE','%'.$search.'%')
                ->orWhere('employee.mobile_no','LIKE','%'.$search.'%')
                ->orWhere('employee.whatsapp_no','LIKE','%'.$search.'%')
                ->orWhere('employee.address','LIKE','%'.$search.'%');
            });
        };

       $result = $query->paginate(config('constants.PAGINATEVALUE'))->onEachSide(config('constants.PAGINATE_onEachSide'));

      return $result ;
    }


    public static function findEmployeeById($id){
       
        $result = Employee::select('employee.*','cities.city_name','cities.pincode','states.state_name','region.region_name','market.market_name')
        ->leftjoin('market','market.market_id','employee.market_id')
        ->leftjoin('cities','cities.city_id','employee.city_id')
        ->leftjoin('states','states.state_id','employee.state_id')
        ->leftjoin('region','region.region_id','employee.region_id')
        ->where('employee.status',1)
        ->where('employee.employee_id',$id)
        ->first();
       
        return $result ;
    }

    public static function getLatestEmpNo()
    {
        $result = Employee::select('employee_id')->where('employee_id', \DB::raw("(select max(`employee_id`) from employee)"))->first();

        return $result;
    }

    public static function getEmployeeDetailByCities($city_id = null,$skills = null,$rating = null){
       
        $query = Employee::select('employee.*','cities.city_name','cities.pincode','market.market_name')
        ->leftjoin('market','market.market_id','employee.market_id')
        ->leftjoin('cities','cities.city_id','employee.city_id')
        ->where('employee.status',1);

        if(isset($city_id) && ! empty($city_id)) {
            $query = $query->where('employee.city_id',$city_id);
        }

        if(isset($skills) && ! empty($skills)) {
            $query = $query->where('employee.expertise','LIKE','%'.$skills.'%');
        }

        if(isset($rating) && ! empty($rating)) {
            $query = $query->where('employee.review_star','>=',$rating);
        }


        $result = $query->paginate(config('constants.FRONTPAGE_PAGINATEVALUE'))->onEachSide(config('constants.PAGINATE_onEachSide'));
       
        return $result ;
    }

    public static function findEmployeeByCode($code){
        $result = Employee::where('status',1)
        ->where('employee_code',$code)
        ->first();

        return $result;
    }

    public static function getAllEmployeeDetail(){
   
        // $result = Employee::select(DB::raw('GROUP_CONCAT(DISTINCT skills.skill_name) as skname') ,DB::raw('@row:=@row+1 as row'),'employee.*','cities.city_name','cities.pincode','states.state_name','region.region_name','market.market_name')
        // ->leftjoin('market','market.market_id','employee.market_id')
        // ->leftjoin('cities','cities.city_id','employee.city_id')
        // ->leftjoin('states','states.state_id','employee.state_id')
        // ->leftjoin('region','region.region_id','employee.region_id')
        // ->leftJoin('skills', function($join){
        //     $join->on(DB::raw("find_in_set(skills.skill_id,employee.expertise)"),'>',DB::raw('0'));
        //  })
        // ->where('employee.status',1)
        // ->groupBy('3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27')
        // ->get();

        $result = DB::select('select  GROUP_CONCAT(DISTINCT skills.skill_name) as skname, @row:=@row+1 as row, `employee`.*, `cities`.`city_name`, `cities`.`pincode`, `states`.`state_name`, `region`.`region_name`, `market`.`market_name` from `employee` 
        left join `market` on `market`.`market_id` = `employee`.`market_id` 
        left join `cities` on `cities`.`city_id` = `employee`.`city_id` 
        left join `states` on `states`.`state_id` = `employee`.`state_id` 
        left join `region` on `region`.`region_id` = `employee`.`region_id` 
        left join skills on FIND_IN_SET(skills.skill_id,expertise)
        where employee.status = 1
        group by 3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29');
            
        //$result = (object)$result;
        //dd( $result );
        return $result ;
    }

    public static function getDuplicateMobile($id,$mobile){
        $result = Employee::where('status',1)
        ->where('mobile_no',(string)$mobile)
        ->where('employee_id','!=',$id)
        ->first();
        return $result;
    }

    public static function getEmployeeDetailByCitiesSearch($citysearch = null,$skills = null,$rating = null){
       
        $query = Employee::select('employee.*','cities.city_name','cities.pincode','market.market_name')
        ->leftjoin('market','market.market_id','employee.market_id')
        ->leftjoin('cities','cities.city_id','employee.city_id')
        ->where('employee.status',1);

        if(isset($citysearch) && ! empty($citysearch)) {

            $query->where(function ($querys) use ($citysearch) {
                $querys->where('cities.city_name',$citysearch)
                ->orWhere('cities.pincode',$citysearch);
            });
            
        }

        if(isset($skills) && ! empty($skills)) {
            $query = $query->where('employee.expertise','LIKE','%'.$skills.'%');
        }

        if(isset($rating) && ! empty($rating)) {
            $query = $query->where('employee.review_star','>=',$rating);
        }


        $result = $query->paginate(config('constants.FRONTPAGE_PAGINATEVALUE'))->onEachSide(config('constants.PAGINATE_onEachSide'));
       
        return $result ;
    }
   
}
