<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;
use Validator;

class DemoController extends Controller
{
    
    public function dataStore(Request $request)
    {
        try {
             /* Validation Check -  Required Fields */
            $rules = [
                'firstname' => 'required',
                'lastname' => 'required',
                'email'=>'required|email:rfc,dns',
                'phonenumber'=>'required|max:10|min:10',
                'date_of_birth'=>'nullable|date|date_format:Y/m/d|before:tomorrow',
                'is_vaccinated'=>"in:yes,no",
                'vaccine_name'=>"required_if:is_vaccinated,yes|in:COVAXIN,COVISHIELD"
            ];

            $messages = [
                'firstname.required' => "FirstName is Required.",
                'lastname.required' => 'LastName is Required.',
                'email' => 'EmailAddress is Required.',
                'phonenumber'=> 'phonenumber is Required',
                'phonenumber.min'=> 'PhoneNumber Min 10 Digits Required',
                'phonenumber.max'=> 'PhoneNumber max 10 Digits Allowed',
                'date_of_birth.date_format'=> 'The date of birth does not match the format Y/m/d.',
                'date_of_birth.after'=> 'Future Date is not a Selected.',
                'is_vaccinated.in'=>"The vaccine field is required."
            ]; 
            
            $validator = Validator::make($request->json()->all(),$rules, $messages);
            if ($validator->fails())
            {
                return response()->json(["status-code"=> 422,"validatation-error" => $validator->errors()],422);    // validation fails send response
            }
            else
            {
                /* Address not required */
                if ($request->has('address')) {
                    if ($request->address == '') {
                        $address ='';
                    } else {
                        $address = $request->address;
                    }

                    $data = [
                        'firstname' => $request->firstname,
                        'lastname' => $request->lastname,
                        'email' => $request->email,
                        'phoneno' => $request->phonenumber,
                        'address' => $address,
                        'date_of_birth' => $request->date_of_birth
                    ];

                } else {
                    $data = [
                        'firstname' => $request->firstname,
                        'lastname' => $request->lastname,
                        'email' => $request->email,
                        'phoneno' => $request->phonenumber,
                    ];
                }

                /* Birthdate is date format is required y/m//d */
                if ($request->has('date_of_birth')) {
                    if ($request->date_of_birth == '') {
                    $date_of_birth = '';
                    } else {
                        $date_of_birth = $request->date_of_birth;
                    }
                    
                    $data = [
                        'firstname' => $request->firstname,
                        'lastname' => $request->lastname,
                        'email' => $request->email,
                        'phoneno' => $request->phonenumber,
                        'address' => $address,
                        'date_of_birth' => $request->date_of_birth
                    ];

                } else {
                    $data = [
                        'firstname' => $request->firstname,
                        'lastname' => $request->lastname,
                        'email' => $request->email,
                        'phoneno' => $request->phonenumber,
                        'address' => $address,
                    ];
                }
            
                /* vaccinated - yes (vaccine Name is required validation) */
                if($request->has('is_vaccinated')){
                    if ($request->is_vaccinated == 'yes') {         // is_vaccinated - YES
                        if($request->has('vaccine_name')){
                            if ($request->vaccine_name == 'COVAXIN' || $request->vaccine_name == 'COVISHIELD' ) {
                                $vaccine_name = $request->vaccine_name;
                            }
             
                            $data = [
                                'firstname' => $request->firstname,
                                'lastname' => $request->lastname,
                                'email' => $request->email,
                                'phoneno' => $request->phonenumber,
                                'address' => $address,
                                'date_of_birth' => $request->date_of_birth,
                                'is_vaccinated'=> $request->is_vaccinated,
                                'vaccine_name'=> $vaccine_name
                            ];
                        }    
                    } else {                                                // is_vaccinated == no
                        $data = [
                            'firstname' => $request->firstname,
                            'lastname' => $request->lastname,
                            'email' => $request->email,
                            'phoneno' => $request->phonenumber,
                            'address' => $address,
                            'date_of_birth' => $request->date_of_birth,
                            'is_vaccinated'=> $request->is_vaccinated,
                        ];
                    }
                    
                } 
                    return response()->json(["statuscode"=> 200 ,"message"=>"Data Send Successfully.","Data"=>$data]);  // Response Send 
                }
        } catch (\Exception\Database\QueryException $e)
        {
            Log::info("Query: ".$e->getSql());
            Log::info("Query: Bindings: ".$e->getBindings());
            Log::info("Error: Code: ".$e->getCode());
            Log::info("Error: Message: ".$e->getMessage());
            return response()->json(["status-code"=> 500,"error_message" => "Internal Server Error"],500);    // validation fails send response
        } 

    }
}
