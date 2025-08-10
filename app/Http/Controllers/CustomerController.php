<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Customer;
use Rap2hpoutre\FastExcel\FastExcel;
use Auth;
use DB;
use Hash;
use Carbon\Carbon;

class CustomerController extends Controller
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
        $filter['email'] = $request->email;
        $filter['name'] = $request->name;

        if ($request->filled('daterange')) {
            $dates = explode(' - ', $request->daterange);
        
            if (count($dates) === 2) {
                $startDate = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                $endDate = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();
        
                $filter['start_date'] = $startDate;
                $filter['end_date'] = $endDate;
            }
        }

        if($request->action == 'export'){
            return $this->exportCustomer($filter);
        }

        $data['customerlist'] = Customer::GetCustomerList($name,$orderby,$search,$filter);

        return view('customer.index',$data);
    }

    public function show($id)
    {
        $data['customer_data'] = Customer::findCustomerById($id);
        if(isset($data['customer_data']) && !empty($data['customer_data'])){
            return view('customer.show', $data);
        } else {
            return redirect('/customer')->with('alert','Customer not Found');
        }
        
    }

    public function import()
    {
        return view('customer.import');
    }

    public function sampleData(){

        $sample_data = array(
            array(
            'First Name' => 'ABCD',
            'Last Name' => 'EFGH',
            'Email' => 'test@gmail.com',
            'Phone Number' => '1234567890',
            'Gender' => 'Male',
            'Date of Birth' => '12-01-2000',
            'Anniversary' => '12-01-2000',
            'Address Line' => 'Address Line',
            'Address Landmark' => 'Address Landmark',
            'Country Code' => '91',
            'Invoice Type' => 'Sale',
            'Allow Promotional Communication' => 'Yes',
            'Allow Transactional Communication' => 'Yes',
            'Communication Channel' => 'SMS'
            ));

        return (new FastExcel($sample_data))->download('CustomerMasterSample'.date('d_M_Y-h-i-s-A').'.xlsx');

    }

    public function importData(Request $request) 
    {
        $this->validate($request, [
            'import_file' => ['required','mimes:xlsx,xls']
        ]);

        $constant_headers = config('constants.CUSTOMERHEADER');
        $constant_headers = array_map('trim', $constant_headers);
        $constant_headers = array_map('strtolower', $constant_headers);

        $file=$request->file('import_file');


        $path = $file->getRealPath();
        $data = (new FastExcel())->import($path);

        $errors = array();
        $imported = 0;
        $updated = 0;

        foreach ($data as $index => $line) {
            $rowNum = $index + 2; // Excel row number (accounting for header row)
            
            $trimmed_array = array_map('trim', array_keys($line));
            $line = array_change_key_case($line, CASE_LOWER);
            $headers = array_keys($line);
            $headers = array_filter($headers);

            if (count($headers) > 0 && !empty($headers)) {
                // Check if headers match expected format
                if ($headers == $constant_headers) {
                    if((empty($line['no'])) || (empty($line['first name'])) || (empty($line['email'])) || (empty($line['phone number'])) || (empty($line['gender']))) {
                        $not_exists = array(
                            'line_no' => $line['no'],
                            'reason' => 'Missing First Name/Email/Phone Number/Gender Column Values',
                            'created_at' => date('Y-m-d'),
                            'updated_by' => Auth::id(),
                        );
                        array_push($errors, $not_exists);

                        continue;
                    }

                    // Prepare customer data
                    $data = [
                        'first_name' => $line['first name'],
                        'last_name' => $line['last name'] ?? null,
                        'email' => $line['email'],
                        'phone_number' => $line['phone number'],
                        'gender' => $line['gender'] ?? null,
                        'date_of_birth' => !empty($line['date of birth']) ? $line['date of birth'] : null,
                        'anniversary' => !empty($line['anniversary']) ? $line['anniversary'] : null,
                        'address_line' => $line['address line'] ?? null,
                        'address_landmark' => $line['address landmark'] ?? null,
                        'country_code' => $line['country code'] ?? null,
                        'invoice_type' => $line['invoice type'] ?? null,
                        'allow_promotional_communication' => strtolower($line['allow promotional communication'] ?? '') === 'yes',
                        'allow_transactional_communication' => strtolower($line['allow transactional communication'] ?? '') === 'yes',
                        'communication_channels' => $line['communication channel'] ?? null,
                        'status' => 1,
                    ];

                    // Check if customer already exists by email or phone
                    $existingCustomer = Customer::findCustomerByEmailPhone($line['email'], $line['phone number']);

                    if ($existingCustomer) {
                        // Check if another customer has the same email (but not this one)
                        $duplicateEmail = Customer::where('email', $line['email'])
                            ->where('id', '!=', $existingCustomer->id)
                            ->first();
                            
                        if ($duplicateEmail) {
                            $errors[] = [
                                'line_no' => $rowNum,
                                'reason' => 'Email already exists for another customer',
                                'created_at' => date('Y-m-d'),
                                'updated_by' => Auth::id(),
                            ];
                            continue;
                        }
                        
                        // Check if another customer has the same phone (but not this one)
                        $duplicatePhone = Customer::where('phone_number', $line['phone number'])
                            ->where('id', '!=', $existingCustomer->id)
                            ->first();
                            
                        if ($duplicatePhone) {
                            $errors[] = [
                                'line_no' => $rowNum,
                                'reason' => 'Phone number already exists for another customer',
                                'created_at' => date('Y-m-d'),
                                'updated_by' => Auth::id(),
                            ];
                            continue;
                        }
                        
                        // Update existing customer
                        $data['updated_by'] = Auth::id();
                        $data['updated_at'] = Carbon::now();
                        
                        Customer::where('id', $existingCustomer->id)->update($data);
                        $updated++;
                    } else {
                        // Check for duplicate email or phone before creating
                        $duplicateEmail = Customer::where('email', $line['email'])->first();
                        if ($duplicateEmail) {
                            $errors[] = [
                                'line_no' => $rowNum,
                                'reason' => 'Email already exists',
                                'created_at' => date('Y-m-d'),
                                'updated_by' => Auth::id(),
                            ];
                            continue;
                        }
                        
                        $duplicatePhone = Customer::where('phone_number', $line['phone number'])->first();
                        if ($duplicatePhone) {
                            $errors[] = [
                                'line_no' => $rowNum,
                                'reason' => 'Phone number already exists',
                                'created_at' => date('Y-m-d'),
                                'updated_by' => Auth::id(),
                            ];
                            continue;
                        }
                        
                        // Create new customer
                        $data['created_by'] = Auth::id();
                        $data['created_at'] = Carbon::now();
                        
                        Customer::create($data);
                        $imported++;
                    }
                } else {
                    return back()->with('alert', 'Invalid file format. Please use the sample file as a template.');
                }
            }
        }

        $message = "Import completed. {$imported} customers imported, {$updated} customers updated.";
        
        if (count($errors) > 0) {
            return redirect('customer/import')->with('failoverwritelist', $errors)->with('message', $message);
        }
        
        return redirect('customer')->with('message', $message);
    }

    public function exportCustomer($filter){
        $datalists = Customer::GetCustomerList(null,null,null,$filter);

        if(!empty($datalists)){
            $i = 1;
            $data = array();
            foreach($datalists as $key =>$txn){
            
                $data[] = array(
                        'No' => $i,
                        'First Name' => $txn->first_name,
                        'Last Name' => $txn->last_name,
                        'Email' => $txn->email,
                        'Phone Number' => $txn->phone_number,
                        'Gender' => $txn->gender,
                        'Date of Birth' => $txn->date_of_birth,
                        'Anniversary' => $txn->anniversary,
                        'Address Line' => $txn->address_line,
                        'Address Landmark' => $txn->address_landmark,
                        'Country Code' => $txn->country_code,
                        'Invoice Type' => $txn->invoice_type,
                        'Allow Promotional Communication' => $txn->allow_promotional_communication == 1 ? 'Yes' : 'No',
                        'Allow Transactional Communication' => $txn->allow_transactional_communication == 1 ? 'Yes' : 'No',
                        'Communication Channel' => $txn->communication_channels,
                );
                $i++;
            }
                
            return (new FastExcel($data))->download('CustomerMaster'.date('d_M_Y-h-i-s-A').'.xlsx');
        }else{
            return Redirect::back()->with('alert', 'Employee Data Not Found');
        }
    }
    

}
