<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Eloquent;

class Customer extends Eloquent 
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'customers';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'gender',
        'date_of_birth',
        'anniversary',
        'allow_promotional_communication',
        'allow_transactional_communication',
        'communication_channels',
        'address_line',
        'address_landmark',
        'country_code',
        'invoice_type',
        'created_by',
        'updated_by',
        'status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    // protected $casts = [
    //     'allow_promotional_communication' => 'boolean',
    //     'allow_transactional_communication' => 'boolean',
    //     'date_of_birth' => 'date',
    //     'anniversary' => 'date',
    //     'communication_channels' => 'array',
    // ];

    public static function GetCustomerList($name=null,$orderby=null,$search=null,$filter=null){
       
        $query = Customer::select('customers.*')
        ->where('customers.status',1);
       
        if(isset($name) && ! empty($name) && isset($orderby) && ! empty($orderby)) {
            $query = $query->orderBy($name, $orderby);
        };
        if(isset($search) && !empty($search)) {
            $query->where(function ($querys) use ($search) {
                $querys->where('customers.first_name','LIKE','%'.$search.'%')
                ->orWhere('customers.last_name','LIKE','%'.$search.'%')
                ->orWhere('customers.email','LIKE','%'.$search.'%')
                ->orWhere('customers.phone_number','LIKE','%'.$search.'%');
            });
        };  

        if(isset($filter['start_date']) && !empty($filter['start_date']) && isset($filter['end_date']) && !empty($filter['end_date'])) {
            $query->where(function ($querys) use ($filter) {
                $querys->whereBetween('customers.created_at', [$filter['start_date'], $filter['end_date']]);
            });
        };

        if(isset($filter['email']) && !empty($filter['email'])) {
            $query->where(function ($querys) use ($filter) {
                $querys->where('customers.email','LIKE','%'.$filter['email'].'%');
            });
        };

        if(isset($filter['name']) && !empty($filter['name'])) {
            $query->where(function ($querys) use ($filter) {
                $querys->where('customers.first_name','LIKE','%'.$filter['name'].'%')
                ->orWhere('customers.last_name','LIKE','%'.$filter['name'].'%');
            });
        };

       $result = $query->paginate(config('constants.PAGINATEVALUE'))->onEachSide(config('constants.PAGINATE_onEachSide'));

      return $result;
    }
    
    /**
     * Find a customer by email or phone number
     *
     * @param string $email
     * @param string $phone
     * @return Customer|null
     */
    public static function findCustomerByEmailPhone($email, $phone)
    {
        return self::where('email', $email)
            ->where('phone_number', $phone)
            ->first();
    }
    
    /**
     * Find a customer by ID
     *
     * @param int $id
     * @return Customer|null
     */
    public static function findCustomerById($id)
    {
        return self::where('id', $id)->first();
    }

    public static function getAllCustomerDetail(){
        $result = Customer::select('customers.*')
        ->where('customers.status',1)
        ->get();
        return $result;
    }
}