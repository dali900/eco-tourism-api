<?php

namespace App\Http\Controllers;

use App\Notifications\ContactConfirmationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class BannersController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function banner1Contact(Request $request, $app)
    {
        $attr = $request->validate(
            [
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'email' => 'required|string',
                'company_name' => 'required|string',
                'phone_number' => 'required|string',
            ], 
            [], 
            [
                'company_name' => 'Kompanija',
                'phone_number' => 'Kontakt telefon'
            ]
        );
        $details = $request->all();
        $details['app'] = $app;
        
        $user = auth()->user();
        if ($user) {
            if (empty($user->company_name) && !empty($details['company_name'])) {
                $user->company_name = $details['company_name'];
            }
            if (empty($user->phone_number) && !empty($details['phone_number'])) {
                $user->phone_number = $details['phone_number'];
            }
            if ($user->isDirty()) {
                $user->save();
            }
            $user->notify((new ContactConfirmationNotification($app)));
        } else if (!empty($details['email'])){
            Mail::to($details['email'])->send(new \App\Mail\ContactConfirmationMail($app));
        }

        if(config('app.env') === 'production' || str_contains(config('mail.mailers.smtp.host'), 'mailtrap')){
            Mail::to('bzrportal@actamedia.rs')
                ->send(new \App\Mail\ContactBanner1Mail($details));
        }

        return $this->responseSuccess([
            'message' => "Success sent mail"
        ]);
    }
}
