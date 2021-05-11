<?php

namespace App\Http\Controllers;

use App\Models\Smstemplete;
use App\Models\Stream;
use Illuminate\Http\Request;

class InitializeController extends Controller
{
    //
    public function initialize()
    {

        $enter_templete = new Smstemplete();
        $enter_templete->name = "Enter school";
        $enter_templete->content = ".";
        $enter_templete2 = new Smstemplete();
        $enter_templete2->name = "Exit school";
        $enter_templete2->content = ".";

        if (sizeof(Smstemplete::all()) != 0) {
            // return "Data already present";
        } else {
            $enter_templete->save();
            $enter_templete2->save();
        }
        if (sizeof(Stream::all()) != 0) {
            return "Success data 1";
        }
        $streams = new Stream();
        $streams->save();

        return "Success 2";
    }
}
