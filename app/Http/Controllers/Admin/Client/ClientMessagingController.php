<?php

namespace App\Http\Controllers\Admin\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Auth;

use App\Models\Admin;
use App\Models\ActivitiesLog;
use App\Models\Note;
use App\Models\ClientPhone;
use App\Mail\ClientVerifyMail;
use App\Mail\GoogleReviewMail;

use GuzzleHttp\Client;

/**
 * Client email and SMS messaging
 * 
 * Methods moved from ClientsController:
 * - uploadmail (TODO - still in ClientsController)
 * - enhanceMessage
 * - sendmsg
 * - fetchClientContactNo
 * - isgreviewmailsent
 * - updateemailverified
 * - emailVerify
 * - emailVerifyToken (public, no auth)
 * - thankyou (public, no auth)
 */
class ClientMessagingController extends Controller
{
    protected $openAiClient;

    public function __construct()
    {
        $this->middleware('auth:admin')->except(['emailVerifyToken', 'thankyou']);
        
        $this->openAiClient = new Client([
            'base_uri' => 'https://api.openai.com/v1/',
            'headers' => [
                'Authorization' => 'Bearer ' . config('services.openai.api_key'),
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * Update email to be verified wrt client id
     */
    public function updateemailverified(Request $request)
    {
        $data = $request->all();
        $recExist = Admin::where('id', $data['client_id'])
        ->update(['manual_email_phone_verified' => $data['manual_email_phone_verified']]);
         if($recExist){
             $response['status'] 	= 	true;
             $response['message']	=	'Record updated successfully';
         } else {
             $response['status'] 	= 	false;
             $response['message']	=	'Please try again';
         }
         echo json_encode($response);
    }
    
    /**
     * Send email verification email to client
     */
    public function emailVerify(Request $request)
    {
        try {
            // Validate input
            $request->validate([
                'client_email' => 'required|email',
                'client_id' => 'required|integer',
                'client_fname' => 'required|string'
            ]);
            
            // Verify client exists
            $client = Admin::find($request->client_id);
            if (!$client) {
                return response()->json([
                    'status' => false,
                    'message' => 'Client not found.'
                ], 404);
            }
            
            // Prepare email details
            $details = [
                'fullname' => $request->client_fname,
                'title' => 'Please verify your email address by clicking the button below.',
                'client_id' => $request->client_id
            ];
            
            // Send verification email using .env configuration (smtp mailer)
            Mail::mailer('smtp')->to($request->client_email)->send(new ClientVerifyMail($details));
            
            return response()->json([
                'status' => true,
                'message' => 'Verification email sent successfully.'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->errors();
            $errorMessages = [];
            foreach ($errors as $field => $messages) {
                $errorMessages[] = implode(', ', $messages);
            }
            return response()->json([
                'status' => false,
                'message' => 'Validation failed: ' . implode(' ', $errorMessages)
            ], 422);
        } catch (\Exception $e) {
            Log::error('Verification email error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to send verification email: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Process email verification token from email link
     */
    public function emailVerifyToken($token)
    {
        try {
            // Decode token with error handling for PHP 8.x compatibility
            $base64_decoded = base64_decode($token);
            if ($base64_decoded === false) {
                return redirect('/')->withErrors(['error' => 'Invalid verification link.']);
            }
            
            $client_id = @convert_uudecode($base64_decoded);
            if ($client_id === false || $client_id === '' || !is_numeric($client_id)) {
                return redirect('/')->withErrors(['error' => 'Invalid verification link.']);
            }
            
            // Convert to integer for safety
            $client_id = (int)$client_id;
            
            // Find client
            $client = Admin::find($client_id);
            if (!$client) {
                return redirect('/')->withErrors(['error' => 'Client not found.']);
            }
            
            // Update verification status (using update() to avoid mass assignment issues)
            Admin::where('id', $client_id)->update([
                'manual_email_phone_verified' => 1,
                'email_verified_at' => now()
            ]);
            
            // Redirect to thank you page
            return redirect()->route('emailVerify.thankyou')->with('success', 'Email verified successfully!');
            
        } catch (\Throwable $e) {
            return redirect('/')->withErrors(['error' => 'Invalid verification link.']);
        }
    }
    
    /**
     * Thank you page after email verification
     */
    public function thankyou()
    {
        return view('thankyou');
    }

    /**
     * Fetch all contact list of any client at create note popup
     */
    public function fetchClientContactNo(Request $request){
        if( ClientPhone::where('client_id', $request->client_id)->exists())
        {
            //Fetch All client contacts
            $clientContacts = ClientPhone::select('client_phone','client_country_code','contact_type')->where('client_id', $request->client_id)->where('contact_type', '!=', 'Not In Use')->get();
            if( !empty($clientContacts) && count($clientContacts)>0 ){
                $response['status'] 	= 	true;
                $response['message']	=	'Client contact is successfully fetched.';
                $response['clientContacts']	=	$clientContacts;
            } else {
                $response['status'] 	= 	false;
                $response['message']	=	'Please try again';
                $response['clientContacts']	=	array();
            }
        }
        else
        {
            if( Admin::where('id', $request->client_id)->exists()){
                //Fetch All client contacts
                $clientContacts = Admin::select('phone as client_phone','country_code as client_country_code','contact_type')->where('id', $request->client_id)->get();
                if( !empty($clientContacts) && count($clientContacts)>0 ){
                    $response['status'] 	= 	true;
                    $response['message']	=	'Client contact is successfully fetched.';
                    $response['clientContacts']	=	$clientContacts;
                } else {
                    $response['status'] 	= 	false;
                    $response['message']	=	'Please try again';
                    $response['clientContacts']	=	array();
                }
            }
            else {
                $response['status'] 	= 	false;
                $response['message']	=	'Please try again';
                $response['clientContacts']	=	array();
            }
        }
        echo json_encode($response);
	}
  
    /**
     * Send message
     */
    public function sendmsg(Request $request){
        $obj = new Note;
        $obj->client_id = $request->client_id;
        $obj->user_id = Auth::user()->id;
        $subject = 'sent a message';
        $obj->title =  $subject;
        $obj->description = $request->message;
        $obj->type = $request->vtype;
        $obj->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
        $obj->folloup = 0; // Required NOT NULL field (0 = not a followup, 1 = followup)
        $obj->status = 0; // Required NOT NULL field (0 = active/open, 1 = closed/completed)
        $saved = $obj->save();
		if($saved){
            if($request->vtype == 'client'){
                $objs = new ActivitiesLog;
                $objs->client_id = $request->client_id;
                $objs->created_by = Auth::user()->id;
                $objs->description = '<span class="text-semi-bold">'.$subject.'</span><p>'.$request->message.'</p>';
                $objs->subject = $subject;
                $objs->task_status = 0;
                $objs->pin = 0;
                $objs->save();
            }
            // When SMS is sent with application_id: record SMS reminder and log for filters
            if (!empty($request->application_id)) {
                $app = \App\Models\Application::find((int)$request->application_id);
                if ($app && $app->client_id) {
                    \App\Models\ApplicationReminder::create([
                        'application_id' => $app->id,
                        'type' => 'sms',
                        'reminded_at' => now(),
                        'user_id' => Auth::user()->id,
                    ]);
                    $smsSentDate = now()->format('d/m/Y');
                    $objs = new ActivitiesLog;
                    $objs->client_id = $app->client_id;
                    $objs->created_by = Auth::user()->id;
                    $objs->subject = 'SMS reminder sent';
                    $objs->description = 'SMS reminder sent on ' . $smsSentDate;
                    $objs->task_status = 0;
                    $objs->pin = 0;
                    $objs->save();
                }
            }
            $response['status'] 	= 	true;
            $response['message']	=	'You have successfully sent message';
        }else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
        echo json_encode($response);
	}
  
    /**
     * Google review email sent
     */
    public function isgreviewmailsent(Request $request){
        $data = $request->all();
        if($data['is_greview_mail_sent'] != 1){
            $userInfo = Admin::select('first_name','email')->where('id', $data['id'])->first();
            if($userInfo){
                //Google review email link To Customer
                $details = [
                    'title' => 'Invitation For Google Review At Bansal Immigration',
                    'body' => 'This is for testing email using smtp',
                    'fullname' => $userInfo->first_name,
                    'email'=> $userInfo->email,
                    'reviewLink'=> env('GOOGLE_REVIEW_LINK')
                ];

                if( \Mail::to($userInfo->email)->send(new GoogleReviewMail($details)) ) {
                    $recExist = Admin::where('id', $data['id'])->update(['is_greview_mail_sent' => 1]);
                    if($recExist){
                        $objs = new ActivitiesLog;
                        $objs->client_id = $data['id'];
                        $objs->created_by = Auth::user()->id;
                        $objs->description = '<span class="text-semi-bold">Google review inviatation sent successfully</span>';
                        $objs->subject = "Google review inviatation";
                        $objs->task_status = 0;
                        $objs->pin = 0;
                        $objs->save();

                        $response['status'] 	= 	true;
                        $response['message']	=	'Google review inviatation sent successfully';
                        $response['is_greview_mail_sent'] 	= 	$data['is_greview_mail_sent'];
                    }
                } else {
                    $response['status'] 	= 	false;
                    $response['message']	=	'Please try again';
                    $response['is_greview_mail_sent'] 	= 	$data['is_greview_mail_sent'];
                }
            } else {
                $response['status'] 	= 	false;
                $response['message']	=	'Please try again';
                $response['is_greview_mail_sent'] 	= 	$data['is_greview_mail_sent'];
            }
        }
        echo json_encode($response);
    }
   
  	/**
     * ChatGPT enhance message
     */
    public function enhanceMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        try {
            $response = $this->openAiClient->post('chat/completions', [
                'json' => [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are a professional email writer. Rewrite the following content in a more professional and polished manner:'
                        ],
                        [
                            'role' => 'user',
                            'content' => $request->message
                        ]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 500,
                ],
            ]);

            $result = json_decode($response->getBody(), true);
            $enhancedMessage = $result['choices'][0]['message']['content'];

            return response()->json(['enhanced_message' => $enhancedMessage]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to enhance message: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Upload/record sent mail
     */
    public function uploadmail(Request $request){
		$requestData 		= 	$request->all();
		$obj				= 	new \App\Models\MailReport;
		$obj->user_id		=	Auth::user()->id;
		$obj->from_mail 	=  $requestData['from'];
		$obj->to_mail 		=  $requestData['to'];
		$obj->subject		=  $requestData['subject'];
		$obj->message		=  $requestData['message'];
		$obj->mail_type		=  1;
		$obj->client_id		=  @$requestData['client_id'];
		$saved				=	$obj->save();
		if(!$saved)
			{
				return redirect()->back()->with('error', \Config::get('constants.server_error'));
			}

			else
			{
				return redirect()->back()->with('success', 'Email uploaded Successfully');

			}
	}

}
