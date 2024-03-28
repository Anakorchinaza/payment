<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\MyMail;
use Illuminate\Support\Facades\Mail;


class EmailController extends Controller
{

    public function SendEmail1(){

        $data = [
            'key' => 'value',
        ];

         // Change recipient based on your needs
        $recipientEmail = 'recipient@gmail.com';
    
        Mail::to($recipientEmail)->send(new MyMail($data));
    
        //return "Email sent!";
        return "Email sent to $recipientEmail!";
        
    }//end method

    public function sendEmail()
    {
        $data = [
            'key' => 'value',
        ];

        // Use Mailtrap credentials by default
        $mailer = 'mailtrap';
        $email = 'rubyututu@gmail.com';

        // Check if the email is a Gmail address
        if (strpos($email, 'gmail.com') !== false) {
            // Switch to Gmail credentials
            $mailer = 'gmail';
        }

        // Dynamically switch mail configuration
        config([
            'mail.mailer' => config("mail.mailers.$mailer.mailer"),
            'mail.host' => config("mail.mailers.$mailer.host"),
            'mail.port' => config("mail.mailers.$mailer.port"),
            'mail.username' => config("mail.mailers.$mailer.username"),
            'mail.password' => config("mail.mailers.$mailer.password"),
            'mail.encryption' => config("mail.mailers.$mailer.encryption"),
            'mail.from.address' => config("mail.mailers.$mailer.from.address"),
            'mail.from.name' => config("mail.mailers.$mailer.from.name"),
        ]);

        // Send the email
        Mail::to($email)->send(new MyMail($data));

        return 'Email sent successfully';
    }






}

