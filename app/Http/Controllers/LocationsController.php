<?php

namespace App\Http\Controllers;

use App\Location;
use Illuminate\Http\Request;

class LocationsController extends Controller
{
    public function insertLocation(Request $request){

        // json response array
        $response = array("error" => FALSE);

        if (isset($request['user_id']) && isset($request['latitude']) && isset($request['longitude'])) {

            $lokacija = new Location();

            $lokacija->user_id = $request->user_id;
            $lokacija->latitude = $request->latitude;
            $lokacija->longitude = $request->longitude;

            $lokacija->save();

            echo json_encode($response);

        }else{
            $response["error"] = TRUE;
            $response["error_msg"] = "Required parameters (name, email or password) is missing!";
            echo json_encode($response);
        }
    }
}
