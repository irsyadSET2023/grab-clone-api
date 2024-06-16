<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    //
    public function myAccount()
    {
        $user = Auth::user();

        return $this->sendResponse("My account", $user, 200);
    }
}
