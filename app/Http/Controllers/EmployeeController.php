<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasRoles;
use App\Models\State;
use App\Models\Region;
use App\Models\Cities;
use App\Models\Market;
use App\Models\Employee;
use App\Models\Skills;
use App\Models\Ratings;
use Rap2hpoutre\FastExcel\FastExcel;
use Auth;
use DB;
use Hash;
use Carbon\Carbon;

class EmployeeController extends Controller
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
        $data['employeelist'] = Employee::GetEmployeeList($name,$orderby,$search);
        return view('employee.index',$data);
    }


    public function create()
    {
        $data['regions'] = Region::where('status',1)->get();
        $data['skillslists'] = Skills::getSkillsAllList();
        return view('employee.add',$data);
    }


 
    public function store(Request $request)
    {
        
        $this->validate($request, [
            'employee_unique_id'  => 'required|unique:employee,employee_unique_id',
            'region_id' => 'required|numeric',
            'state_id' => 'required|numeric',
            'city_id' => 'required|numeric',
            'pincode' => 'bail|required|numeric|digits:6',
            'mobile_no' => 'bail|required|numeric|digits:10|unique:employee,mobile_no',
            'whatsapp_no' => 'bail|required_unless:whatsapp_same_mobile_no,!=,1',
            'name' => 'required',
            //'logo.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $latest_emp_id =Employee::getLatestEmpNo();
        $suffix = 'ELE-';
        $additional = 1001;
        if(!empty($latest_emp_id)):
            $empcode = $suffix.($additional+$latest_emp_id->employee_id);
        else:
            $empcode = $suffix.$additional;
        endif;        

        $data = array(
            'employee_code' => $empcode,
            'employee_unique_id' => $request->input('employee_unique_id'),
            'employee_name' => $request->input('name'),
            "mobile_no" => $request->input('mobile_no'),
            "whatsapp_same_mobile_no"=> (null != ($request->input('whatsapp_same_mobile_no'))) ? $request->input('whatsapp_same_mobile_no') : 0,
            "whatsapp_no"=> (isset($request->whatsapp_same_mobile_no) && $request->whatsapp_same_mobile_no == 1) ? null : $request->input('whatsapp_no'),
            "address"=>$request->input('address'),
            "region_id"=>$request->input('region_id'),
            "state_id"=>$request->input('state_id'),
            "city_id"=>$request->input('city_id'),
            "market_id"=>$request->input('market_id'),
            "description"=>$request->input('description'),
            "expertise"=>(null != $request->input('expertise') ? implode(',',$request->input('expertise')) : null),
            "created_by"=>Auth::id(),
            "created_at"=>Carbon::now(),
        );
        
        if($request->input('logo')){

            $image_parts = explode(";base64,", $request->input('logo'));
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $date = date("Ymdhms");
            $num = rand(0, 999);
            $file_name = $date . '_' . $num;
            $destinationPath = '/uploads/profile_photo/';

            $file = public_path(). $destinationPath . $file_name . '.png';
            file_put_contents($file, $image_base64);
            $data['photo_name'] =  $file_name .'.png';
        }

        Employee::create($data);
        
        return redirect('employee')->with('message','Employee created successfully');

    }
  
    public function edit($id)
    {
        $data['employee_data'] = Employee::findEmployeeById($id);
        if(isset($data['employee_data']) && !empty($data['employee_data'])){
            $data['skillslists'] = Skills::getSkillsAllList();
            return view('employee.edit', $data);
        } else {
            return redirect('/employee')->with('alert','Employee not Found');
        }
        
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'employee_unique_id'  => 'required',
            'region_id' => 'required|numeric',
            'state_id' => 'required|numeric',
            'city_id' => 'required|numeric',
            'pincode' => 'bail|required|numeric|digits:6',
            'mobile_no' => 'bail|required|numeric|digits:10',
            'whatsapp_no' => 'bail|required_unless:whatsapp_same_mobile_no,!=,1',
            'name' => 'required',
        ]);

        $employee_data = Employee::find($request->input('employee_id'));

        if(isset($employee_data) && !empty($employee_data)){

            if($employee_data->mobile_no !== $request->input('mobile_no')):
                $mobile_no_exist = Employee::where('mobile_no',$request->input('mobile_no'))->first();
                if(isset($mobile_no_exist) && !empty($mobile_no_exist)){
                    return back()->with('alert','Mobile Number is already taken');
                }
            endif;

            //dd($employee_data->employee_unique_id,$request->input('employee_unique_id'),(string)$employee_data->employee_unique_id !== $request->input('employee_unique_id'));
            if((string)$employee_data->employee_unique_id !== $request->input('employee_unique_id')):
                $unique_id_exist = Employee::where('employee_unique_id',$request->input('employee_unique_id'))->first();
                if(isset($unique_id_exist) && !empty($unique_id_exist)){
                    return back()->with('alert','Unique ID is already taken');
                }
            endif;

            $data = array(
                'employee_unique_id' => $request->input('employee_unique_id'),
                "employee_name" => $request->input('name'),
                "mobile_no" => $request->input('mobile_no'),
                "whatsapp_same_mobile_no"=> (null != ($request->input('whatsapp_same_mobile_no'))) ? $request->input('whatsapp_same_mobile_no') : 0,
                "whatsapp_no"=> (isset($request->whatsapp_same_mobile_no) && $request->whatsapp_same_mobile_no == 1) ? null : $request->input('whatsapp_no'),
                "address"=>$request->input('address'),
                "region_id"=>$request->input('region_id'),
                "state_id"=>$request->input('state_id'),
                "city_id"=>$request->input('city_id'),
                "market_id"=>$request->input('market_id'),
                "description"=>$request->input('description'),
                "expertise"=>(null != $request->input('expertise') ? implode(',',$request->input('expertise')) : null),
                "updated_by"=>Auth::id(),
                "updated_at"=>Carbon::now(),
            );

            if($request->input('logo')){

                $image_parts = explode(";base64,", $request->input('logo'));
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);
                $date = date("Ymdhms");
                $num = rand(0, 999);
                $file_name = $date . '_' . $num;
                $destinationPath = '/uploads/profile_photo/';
    
                $file = public_path(). $destinationPath . $file_name . '.png';
                file_put_contents($file, $image_base64);
                $data['photo_name'] =  $file_name .'.png';

                if($employee_data->photo_name){
                    if (file_exists($destinationPath.$employee_data->photo_name)) {
                        unlink($destinationPath.$employee_data->photo_name);
                    }
                }
               
            }
           
            $employee_data->update($data);
           
            return redirect('/employee')->with('message','Employee Data Updated successfully');
            
        } else {
            return redirect('/employee')->with('alert','Employee not Found');
        }
   
     }
    
    public function destroy($id)
    {
        $employee_data = Employee::find($id);
        if(isset($employee_data) && !empty($employee_data)){

            $data = array(
                'status' =>0,
                "updated_by"=>Auth::id(),
                "updated_at"=>Carbon::now(),
            );

            $employee_data->update($data);

            return redirect('/employee')->with('message','Employee deleted successfully');

        } else {
            return redirect('/employee')->with('alert','Employee not Found');
        }
    }

    public function show($id)
    {
        $data['employee_data'] = Employee::findEmployeeById($id);
        if(isset($data['employee_data']) && !empty($data['employee_data'])){
            $data['expertise_list'] = Skills::getSkillsAllList($id);
            $data['emp_rating'] = Ratings::getRatingByEmployeeId($id);
            return view('employee.show', $data);
        } else {
            return redirect('/employee')->with('alert','Employee not Found');
        }
        
    }

    public function import()
    {
        return view('employee.import');
    }

    public function sampleData(){

        $suburb_list = array(
            array(
            'No' => 'No',
            'Emp Code' => 'Emp Code If empty then new emp will be created',
            'Unique ID' => '1111',
            'Employee Name' => 'Emp Name',
            'Mobile Number' =>'1111111111',
            'WhatsApp Number' => '1111111111',
            'Address' => 'Address',
            //'Description' => 'Description',
            'Expertise' => 'Expertise Name comma seperated',
            'Market Place' => 'Market Place Name',
            'Region' => 'Region Name',
            'State' => 'State Name',
            'City' => 'City Name',
            'Pincode' => '222222',
            'Photo' => 'URL of Image',
            ));
        return (new FastExcel($suburb_list))->download('ElectricianMasterSample'.date('d_M_Y-h-i-s-A').'.xlsx');

    }

    public function importData(Request $request) 
    {
        $this->validate($request, [
            'import_file' => ['required']
        ]);

        $constant_headers = config('constants.EMPLOYEE_FILE_HEADER');
        $constant_headers = array_map('trim', $constant_headers);
        $constant_headers = array_map('strtolower', $constant_headers);

        $file=$request->file('import_file');


        $path = $file->getRealPath();

        $data = (new FastExcel())->import($path);

        $errors = array();

        // if(count($data) > 500) {
        //     return back()->with('alert', 'Employee Import File contains more than 500 rows');
        // }

        foreach ($data as $data_key => $line) {
            $trimmed_array = array_map('trim', array_keys($line));

            $line = array_change_key_case($line, CASE_LOWER);
            $headers =  array_keys($line);
            $headers = array_filter($headers);


            if (count($headers) > 0 && !empty($headers)) {
                if ($headers == $constant_headers) {
                    if((empty($line['no'])) || (empty($line['employee name'])) || (empty($line['mobile number'])) || (empty($line['market place'])) || (empty($line['region'])) || (empty($line['state'])) || (empty($line['city'])) || (empty($line['pincode']))) {
                        $not_exists = array(
                            'line_no' => $line['no'],
                            'reason' => 'Missing Fields Column Values',
                            'created_at' => date('Y-m-d'),
                            'updated_by' => Auth::id(),
                        );
                        array_push($errors, $not_exists);

                        continue;
                    }

                    // Region
                    $region = $line['region'];

                    $region_id = Region::getRegionByName($region);

                    if($region_id == 0) {
                        $region_id = Region::create(
                            array(
                                'region_name'=> $region,
                                'status'=>1,
                                "created_by"=>Auth::id(),
                                "created_at"=>Carbon::now(),
                            )
                        )->region_id;
                    }

                    // State
                    $state = $line['state'];

                    $state_id = State::getStateByNameRegion($region_id, $state);

                    if($state_id == 0) {
                        $state_id = State::create(
                            array(
                                'state_name' => $state,
                                "region_id" => $region_id,
                                "country_id"=>1,
                                "created_by"=>Auth::id(),
                                "created_at"=>Carbon::now(),
                            )
                        )->state_id;
                    }


                    /// City
                    $city = $line['city'];
                    $pincode = $line['pincode'];

                    $city_id = Cities::getCityByNamePincodeRegionState($region_id, $state_id, $city, $pincode);

                    if($city_id == 0) {
                        $city_id = Cities::create(
                            array(
                                'city_name' => $city,
                                "pincode" => $pincode,
                                "state_id"=>$state_id,
                                "status"=>1,
                                "created_by"=>Auth::id(),
                                "created_at"=>Carbon::now(),
                            )
                        )->city_id;
                    }

                    /// Market
                    $market = $line['market place'];

                    $market_id = Market::getMarketByNameRegionStateCity($region_id, $state_id, $city_id, $market);

                    if($market_id == 0) {
                        $market_id = Market::create(
                            array(
                                'market_name' => $market,
                                "city_id"=>$city_id,
                                "status"=>1,
                                "created_by"=>Auth::id(),
                                "created_at"=>Carbon::now(),
                            )
                        )->market_id;
                    }

                    // Expertise

                    $expertise_data = $line['expertise'];
                    $expertise_list = explode(',', $expertise_data);
                    $skills_ids = array();

                    if(count($expertise_list) > 0) {
                        foreach($expertise_list as $expertise) {
                            $expertise = trim($expertise, " ");

                            $skill_id = Skills::getSkillByName($expertise);

                            if($skill_id >0) {
                                array_push($skills_ids, $skill_id);
                            }
                        }
                    }


                    $data = array(
                        'employee_name' => $line['employee name'],
                        'employee_unique_id' => $line['unique id'],
                        "mobile_no" => (string)$line['mobile number'],
                        "whatsapp_same_mobile_no"=> (($line['mobile number'] == $line['whatsapp number']) || empty($line['whatsapp number'])) ? 1 : 0,
                        "whatsapp_no"=> ($line['mobile number'] != $line['whatsapp number'] && !empty($line['whatsapp number'])) ? (string)$line['whatsapp number'] : null,
                        "address"=>$line['address'],
                        "region_id"=>$region_id,
                        "state_id"=>$state_id,
                        "city_id"=>$city_id,
                        "market_id"=>$market_id,
                        //"description"=>$line['description'],
                        "expertise"=>((count($skills_ids)>0) ? implode(',', $skills_ids) : null),
                    );

                    if(!empty($line['photo'])){
                        $data["import_image_url"] = $line['photo'];
                    }

                    $employee_exist = Employee::findEmployeeByCode($line['emp code']);

                    // dd($line);

                    if($employee_exist) {
                        if($employee_exist->mobile_no != $line['mobile number']):
                            $mobile_no_exist = Employee::getDuplicateMobile($employee_exist->employee_id,$line['mobile number']);
                            if(isset($mobile_no_exist) && !empty($mobile_no_exist)) {
                                $not_exists = array(
                                    'line_no' => $line['no'],
                                    'reason' => 'Mobile Number already taken.',
                                    'created_at' => date('Y-m-d'),
                                    'updated_by' => Auth::id(),
                                );
                                array_push($errors, $not_exists);
                                continue;
                            }
                        endif;

                        if($employee_exist->employee_unique_id != $line['unique id']):
                            $unique_id_exist = Employee::where('employee_unique_id',$line['unique id'])->first();
                            if(isset($unique_id_exist) && !empty($unique_id_exist)) {
                                $not_exists = array(
                                    'line_no' => $line['no'],
                                    'reason' => 'Unique Id already taken.',
                                    'created_at' => date('Y-m-d'),
                                    'updated_by' => Auth::id(),
                                );
                                array_push($errors, $not_exists);
                                continue;
                            }
                        endif;

                        $data['updated_by'] = Auth::id();
                        $data['updated_at'] = Carbon::now();



                        Employee::where('employee_id',$employee_exist->employee_id)->update($data);
                    } else {

                        $mobile_no_exist = Employee::where('mobile_no',$line['mobile number'])->first();
                        if(isset($mobile_no_exist) && !empty($mobile_no_exist)) {
                            $not_exists = array(
                                'line_no' => $line['no'],
                                'reason' => 'Mobile Number already taken.',
                                'created_at' => date('Y-m-d'),
                                'updated_by' => Auth::id(),
                            );
                            array_push($errors, $not_exists);
                            continue;
                        }

                        $unique_id_exist = Employee::where('employee_unique_id',$line['unique id'])->first();
                        if(isset($unique_id_exist) && !empty($unique_id_exist)) {
                            $not_exists = array(
                                'line_no' => $line['no'],
                                'reason' => 'Unique Id already taken.',
                                'created_at' => date('Y-m-d'),
                                'updated_by' => Auth::id(),
                            );
                            array_push($errors, $not_exists);
                            continue;
                        }


                        $latest_emp_id =Employee::getLatestEmpNo();
                        $suffix = 'ELE-';
                        $additional = 1001;
                        if(!empty($latest_emp_id)):
                            $empcode = $suffix.($additional+$latest_emp_id->employee_id);
                        else:
                            $empcode = $suffix.$additional;
                        endif;

                        $data['employee_code'] = $empcode;
                        $data['created_by'] = Auth::id();
                        $data['created_at'] = Carbon::now();

                        //if($line['no'] == 2)dd($data,$latest_emp_id);
                        Employee::create($data);
                    }
                } else {
                    return back()->with('alert', 'Invalid Header Format');
                }
            }
        }

       
        if (count($errors) > 0 && !empty($errors)) {
            return redirect('employee/import')->with('failoverwritelist', $errors);
        }

        return redirect('employee')->with('message', 'File Uploaded Successfully');
    }

    public function exportEmployee(){
        $datalists = Employee::getAllEmployeeDetail();
        if(!empty($datalists)){
            $i = 1;
            $data = array();
            foreach($datalists as $key =>$txn){
            
                $data[] = array(
                        'No' => $i,
                        'Emp Code' => $txn->employee_code,
                        'Unique ID' => $txn->employee_unique_id,
                        'Employee Name' => $txn->employee_name,
                        'Mobile Number' => $txn->mobile_no,
                        'WhatsApp Number' => $txn->whatsapp_no,
                        'Address' => $txn->address,
                        //'Description' => $txn->description,
                        'Expertise' => $txn->skname,
                        'Market Place' => $txn->market_name,
                        'Region' => $txn->region_name,
                        'State' => $txn->state_name,
                        'City' => $txn->city_name,
                        'Pincode' => $txn->pincode,
                        'Photo' => $txn->import_image_url,
                );
                $i++;
            }
                
            return (new FastExcel($data))->download('ElectricianMaster'.date('d_M_Y-h-i-s-A').'.xlsx');
        }else{
            return Redirect::back()->with('alert', 'Employee Data Not Found');
        }
    }
    

}
