<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User2;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        dd('test');
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
     * Encrypting password
     * @param password
     * returns salt and encrypted password
     */
    public function hashSSHA($password) {

        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
        $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
        $hash = array("salt" => $salt, "encrypted" => $encrypted);
        return $hash;
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        // json response array
        $response = array("error" => FALSE);

        if (isset($request['name']) && isset($request['email']) && isset($request['password'])) {
            $name = $request->name;
            $email = $request->email;
            $password = $request->password;

            $uuid = uniqid('', true);
            $hash = $this->hashSSHA($password);
            $encrypted_password = $hash["encrypted"]; // encrypted password
            $salt = $hash["salt"]; // salt

            $existedUser = User2::where('email', $email)->get();

            if (!$existedUser->isEmpty()) {
                // user already existed
                $response["error"] = TRUE;
                $response["error_msg"] = "User already existed with " . $email;
                echo json_encode($response);
            }else{
                $user = new User2();

                $user->unique_id = $uuid;
                $user->name = $name;
                $user->email = $email;
                $user->encrypted_password = $encrypted_password;
                $user->salt = $salt;

                $user->save();

                $response["uid"] = $user["unique_id"];
                $response["user"]["name"] = $user["name"];
                $response["user"]["email"] = $user["email"];
                $response["user"]["created_at"] = $user["created_at"];
                $response["user"]["updated_at"] = $user["updated_at"];

                echo json_encode($response);
            }
        }else{
            $response["error"] = TRUE;
            $response["error_msg"] = "Required parameters (name, email or password) is missing!";
            echo json_encode($response);
        }
    }

    public function checkhashSSHA($salt, $password) {

        $hash = base64_encode(sha1($password . $salt, true) . $salt);

        return $hash;
    }

    public function getUserByEmailAndPassword($email, $password) {

        $user = User2::where('email', $email)->get();

        if (!$user->isEmpty()) {

            // verifying user password
            $salt = $user[0]->salt;
            $encrypted_password = $user[0]->encrypted_password;
            $hash = $this->checkhashSSHA($salt, $password);

            // check for password equality
            if ($encrypted_password == $hash) {
                // user authentication details are correct
                return $user[0];
            }
        } else {
            return NULL;
        }
    }

    public function login(Request $request){

        // json response array
        $response = array("error" => FALSE);

        if (isset($request['email']) && isset($request['password'])) {

            // receiving the post params
            $email = $_POST['email'];
            $password = $_POST['password'];

            // get the user by email and password
            $user = $this->getUserByEmailAndPassword($email, $password);
//            dd($user);
            if ($user) {
                // use is found
                $response["error"] = FALSE;
                $response["uid"] = $user["unique_id"];
                $response["user"]["name"] = $user["name"];
                $response["user"]["email"] = $user["email"];
                $response["user"]["created_at"] = $user["created_at"];
                $response["user"]["updated_at"] = $user["updated_at"];
                echo json_encode($response);
            } else {
                // user is not found with the credentials
                $response["error"] = TRUE;
                $response["error_msg"] = "Login credentials are wrong. Please try again!";
                echo json_encode($response);
            }
        } else {
            // required post params is missing
            $response["error"] = TRUE;
            $response["error_msg"] = "Required parameters email or password is missing!";
            echo json_encode($response);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        dd('test');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
