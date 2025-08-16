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

        if ($request->filled('datefilter')) {
            $dates = explode(' - ', $request->datefilter);
        
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
            'gender' => 'Male',
            'allowPromotionalCommunication' => 'Yes',
            'anniversary' => '12-01-2000',
            'countryCode' => '91',
            'invoiceType' => 'Sale',
            'communicationChannels' => 'SMS',
            'address.landmark' => 'Address Line',
            'firstName' => 'ABCD',
            'email' => 'test@gmail.com',
            'phoneNumber' => '1234567890',
            'address.addressLine' => 'Address Line',
            'lastName' => 'EFGH',
            'dateOfBirth'=> '12-01-2000',
            'allowTransactionalCommunication'=> 'Yes',
            ));

        return (new FastExcel($sample_data))->download('CustomerMasterSample'.date('d_M_Y-h-i-s-A').'.xlsx');

    }

    public function importData(Request $request) 
    {
        $this->validate($request, [
            'import_file' => ['required', 'file', 'mimes:xlsx,xls,csv']
        ]);

        $constant_headers = config('constants.CUSTOMERHEADER');
        $constant_headers = array_map('trim', $constant_headers);
        $constant_headers = array_map('strtolower', $constant_headers);

        $file = $request->file('import_file');
        $path = $file->getRealPath();
        $extension = $file->getClientOriginalExtension();
        
        try {
            // Handle CSV files differently from Excel files
            if (strtolower($extension) === 'csv') {
                // Store the uploaded file to a more accessible location
                $fileName = time() . '.csv';
                $uploadPath = public_path('uploads/temp');
                
                // Create directory if it doesn't exist
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                
                $filePath = $uploadPath . '/' . $fileName;
                $file->move($uploadPath, $fileName);
                
                // Parse CSV file manually
                $data = [];
                if (($handle = fopen($filePath, 'r')) !== false) {
                    $headers = fgetcsv($handle, 0, ',');
                    $headers = array_map('trim', $headers);
                    $headers = array_map('strtolower', $headers);
                    
                    while (($row = fgetcsv($handle, 0, ',')) !== false) {
                        if (count($headers) === count($row)) {
                            $data[] = array_combine($headers, $row);
                        }
                    }
                    fclose($handle);
                }
                
                // Delete the temporary file
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            } else {
                // For Excel files, use FastExcel
                $data = (new FastExcel())->import($path);
            }
            
            if (empty($data)) {
                return back()->with('alert', 'The uploaded file appears to be empty or in an incorrect format.');
            }
            
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
                    // if (isset($line['firstname']) && isset($line['email']) && isset($line['phonenumber']) && isset($line['gender']) && isset($line['allowpromotionalcommunication']) && isset($line['allowtransactionalcommunication']) && isset($line['communicationchannels']) && isset($line['address.addressLine']) && isset($line['address.landmark']) && isset($line['countrycode']) && isset($line['invoicetype']) && isset($line['dateofbirth'])) {
                        if((empty($line['firstname'])) || (empty($line['email'])) || (empty($line['phonenumber'])) || (empty($line['gender']))) {
                            $not_exists = array(
                                'line_no' => $rowNum,
                                'reason' => 'Missing First Name/Email/Phone Number/Gender Column Values',
                                'created_at' => date('Y-m-d'),
                                'updated_by' => Auth::id(),
                            );
                            array_push($errors, $not_exists);

                            continue;
                        }

                        // Prepare customer data
                        $data = [
                            'first_name' => isset($line['firstname']) ? $line['firstname'] : null,
                            'last_name' => isset($line['lastname']) ? $line['lastname'] : null,
                            'email' => isset($line['email']) ? $line['email'] : null,
                            'phone_number' => isset($line['phonenumber']) ? $line['phonenumber'] : null,
                            'gender' => isset($line['gender']) ? $line['gender'] : null,
                            'date_of_birth' => isset($line['dateofbirth']) && !empty($line['dateofbirth']) ? $line['dateofbirth'] : null,
                            'anniversary' => isset($line['anniversary']) && !empty($line['anniversary']) ? $line['anniversary'] : null,
                            'address_line' => isset($line['address.addressLine']) && !empty($line['address.addressLine']) ? $line['address.addressLine'] : null,
                            'address_landmark' => isset($line['address.landmark']) && !empty($line['address.landmark']) ? $line['address.landmark'] : null,
                            'country_code' => isset($line['countrycode']) && !empty($line['countrycode']) ? $line['countrycode'] : null,
                            'invoice_type' => isset($line['invoicetype']) && !empty($line['invoicetype']) ? $line['invoicetype'] : null,
                            'allow_promotional_communication' => isset($line['allowpromotionalcommunication']) ? strtolower($line['allowpromotionalcommunication'] ?? '') === 'yes' : false,
                            'allow_transactional_communication' => isset($line['allowtransactionalcommunication']) ? strtolower($line['allowtransactionalcommunication'] ?? '') === 'yes' : false,
                            'communication_channels' => isset($line['communicationchannels']) ? $line['communicationchannels'] : null,
                            'status' => 1,
                        ];

                        // Check if customer already exists by email or phone
                        $existingCustomer = Customer::findCustomerByEmailPhone($line['email'], $line['phonenumber']);

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
                            $duplicatePhone = Customer::where('phone_number', $line['phonenumber'])
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
                            
                            $duplicatePhone = Customer::where('phone_number', $line['phonenumber'])->first();
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
                    // } else {
                    //     return back()->with('alert', 'Invalid file format. Please use the sample file as a template.');
                    // }
                }
            }

            $message = "Import completed. {$imported} customers imported, {$updated} customers updated.";
            
            if (count($errors) > 0) {
                return redirect('customer/import')->with('failoverwritelist', $errors)->with('message', $message);
            }
            
            return redirect('customer')->with('message', $message);
        } catch (\Exception $e) {
            return back()->with('alert', 'An error occurred while processing the file: ' . $e->getMessage());
        }
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
