<?php

namespace App\Http\Controllers;

use App\Models\Guardian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GuardianController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Guardian  $guardian
     * @return \Illuminate\Http\Response
     */
    public function show(Guardian $guardian)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Guardian  $guardian
     * @return \Illuminate\Http\Response
     */
    public function edit(Guardian $guardian)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Guardian  $guardian
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Guardian $guardian)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Guardian  $guardian
     * @return \Illuminate\Http\Response
     */
    public function destroy(Guardian $guardian)
    {
        //
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Guardian  $guardian
     * @return \Illuminate\Http\Response
     */
    public function newSms(Request $request)
    {
        $validate = $request->validate([
            'parent_id' => ['required', 'max:255'],
            'message' => ['required', 'max:255'],
        ]);
        $guardian=Guardian::where('id','=',$request->parent_id)->first();
        if($guardian!=null){
            $response=$this->sendSms($guardian,$request->message);
            return $response;
            // return back()->with('success', 'Sms sent successfully');
        }
        // return back()->withErrors([
        //     'message' => 'Something went wrong, contact system administrator',
        // ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Guardian  $guardian
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $guardian=Guardian::where('id','=',$request->parent_id)->first();
        if($guardian->delete()){
            return back()->with('success', 'Parent deleted successfully');
        }
        return back()->withErrors([
            'message' => 'Something went wrong, contact system administrator',
        ]);
    }

    public function sendSms($guardian,$message){
        $response=Http::asForm()->post('https://quicksms.advantasms.com/api/services/sendsms',[
            'apikey'=>$_ENV['SMS_API_KEY'],
            'partnerID'=>$_ENV['SMS_PATNER_ID'],
            'shortcode'=>$_ENV['SMS_SHORT_CODE'],
            'message'=>$message,
            'mobile'=>$guardian->phone,
        ]);
        if($response->successful()){
            // dd($response->json()['responses'][0]['response-description']);
            return back()->with('success', $response->json()['responses'][0]['response-description']);
        }

        // Determine if the status code is >= 400...
        if($response->failed()){

            return back()->withErrors([
                'message' => 'Something went wrong, could not send sms',
            ]);
        }

        // Determine if the response has a 400 level status code...
        if($response->clientError()){

            return back()->withErrors([
                'message' => 'Something went wrong, could not send sms',
            ]);
        }

        // Determine if the response has a 500 level status code...
        if($response->serverError()){

            return back()->withErrors([
                'message' => 'Something went wrong, could not send sms',
            ]);
        }

    }
}
