<?php
namespace App\Http\Controllers;

use App\Models\Namer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Client;
use Carbon\Carbon;

class OTPController extends Controller {
    // Step 1: Phone number input and validation

    public function showLoginForm() {
        return view( 'auth.login' );
        // Form for entering phone number
    }

    public function sendOTP( Request $request ) {
        // Validate phone number
        $validator = Validator::make( $request->all(), [
            'phone_number' => 'required|digits:10',
        ] );

        if ( $validator->fails() ) {
            return redirect()->back()->withErrors( $validator )->withInput();
        }

        // Check if the phone number exists in the database
        $namer = Namer::where( 'phone_number', $request->phone_number )->first();

        // If the number doesn't exist, create a new entry
        if (!$namer) {
            $namer = Namer::create(['phone_number' => $request->phone_number]);
        }

        // Generate OTP and set expiry (e.g., 5 minutes)
        $otp = rand(1000, 9999);
        $expiry = Carbon::now()->addMinutes(5);

        // Update OTP and expiry in the database
        $namer->update([
            'otp' => $otp,
            'otp_expiry' => $expiry,
        ]);

        // Send OTP using Fast2SMS API
        $this->sendOtpToPhone($request->phone_number, $otp);

        // Redirect to OTP verification page
        return view('auth.verify_otp', ['phone_number' => $request->phone_number]);
    }

    // Step 2: Send OTP via Fast2SMS API
    private function sendOtpToPhone($phone_number, $otp)
    {
        $client = new Client();
        $response = $client->get('https://www.fast2sms.com/dev/bulkV2', [
            'query' => [
                'authorization' => 'HTvjlB9YtEdsUKiynPzSQ0pXACN3Ga6hZVF1MbWxJ2moe5uR48hl1we6K3FGcPsEVkqujybAzmtJMdTX',  // Replace with your Fast2SMS API key
                'sender_id' => 'FSTSMS',
                'message' => "Your OTP is $otp",
                'language' => 'english',
                'route' => 'p',
                'numbers' => $phone_number,
            ]
        ]);

        return $response;
    }

    // Step 3: Verify OTP entered by user
    public function verifyOTP(Request $request)
    {
        // Validate OTP input
        $validator = Validator::make($request->all(), [
            'otp' => 'required|digits:4',
            'phone_number' => 'required|digits:10',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Retrieve user based on phone number
        $namer = Namer::where('phone_number', $request->phone_number)->first();

        if ($namer) {
            // Check OTP expiration
            if (Carbon::now()->greaterThan($namer->otp_expiry)) {
                return back()->withErrors(['otp' => 'OTP has expired. Please request a new one.']);
            }

            // Check if OTP matches
            if ($request->otp == $namer->otp) {
                // OTP is correct
                return redirect()->route('home')->with('message', 'Phone number verified successfully!');
            } else {
                // OTP is incorrect
                return back()->withErrors(['otp' => 'Invalid OTP.']);
            }
        }

        return back()->withErrors(['phone_number' => 'Phone number not found.' ] );
    }
}
