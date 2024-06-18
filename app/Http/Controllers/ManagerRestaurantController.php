<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Requests\AssignCategoriesRequest;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManagerRestaurantController extends Controller
{

    public function assignCategories(AssignCategoriesRequest $request)
    {
        $restaurant = Auth::user()->restaurant;
        $categories = $request->categories;
        if (!$request->categories) {
            $restaurant->categories()->detach();
            return $this->sendResponse("Successfully remove restaurant category", null, 200);
        }
        $restaurant->categories()->sync($categories);
        return $this->sendResponse("Successfully update restaurant category", null, 200);
    }
}
