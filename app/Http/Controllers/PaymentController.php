<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Payment;


class PaymentController extends Controller
{
    public function index(Request $request){
        return view('page.payment');
    }//end method

    public function verify(Request $request){
        $transaction_id = $request->transaction_id;
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.flutterwave.com/v3/transactions/{$transaction_id}/verify",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            // CURLOPT_POSTFIELDS => $data_string,
            CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',  
            "Authorization: Bearer FLWSECK_TEST-3d7ef21f812ecb3f31f5eb3ae6bfcef7-X"
            ),
        ));

        // Disable SSL verification (not recommended for production)
        // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);
        echo "Raw API Response: " . $response; // Add this line for debugging
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            // Handle cURL error
            echo "cURL Error: " . $err;
        } else {
          
            // Process the response
            $res = json_decode($response);
            //return [$res];

            if ($res->status == 'success') {
                $name = $request->input('name');
                $email = $request->input('email');
                $phone = $request->input('phone');

                // Create a new user record
                $user = new User();
                $user->name = $name;
                $user->email = $email;
                $user->phone = $phone;
                 // Store the card token in the user record
                $user->card_token = $res->data->authorization->authorization_code;
                $user->save();

                  // Create a new payment record
                $payment = new Payment();
                $payment->user_id = $user->id; // Associate the payment with the user
                $payment->transaction_id = $transaction_id; // Assuming you want to store the transaction ID
                $payment->save();

                // Redirect to a success page
                return redirect('/payment-successful');

            }else {
                // Payment verification failed
                // Handle the error and display an error message to the user
                $errorMessage = "Payment verification failed. Please try again later.";
                return view('error', ['errorMessage' => $errorMessage]);
            }

        }

    }//end method

    public function Reoccure(Request $request){
        $data = [
            'token' => $request->token,
            'email' =>  $request->email,
            'currency' => 'NGN',
            'amount' => $request->amount,
            'tx_ref' => substr(rand(0, time()), 0,10),
        ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.flutterwave.com/v3/tokenized-charges",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            // CURLOPT_POSTFIELDS => $data_string,
            CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',  
            "Authorization: "
            ),
        ));

        // Disable SSL verification (not recommended for production)
        // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);
        echo "Raw API Response: " . $response; // Add this line for debugging
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            // Handle cURL error
            echo "cURL Error: " . $err;
        } else {
          
            // Process the response
            $res = json_decode($response);
            return [$res];
        }
    }//end method



}
