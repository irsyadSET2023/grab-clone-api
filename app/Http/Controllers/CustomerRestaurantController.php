<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddMenuToCartRequest;
use App\Models\Cart;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Restaurant;
use App\Services\Customer\CustomerRestaurantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerRestaurantController extends Controller
{
    //
    public function index(Request $request, CustomerRestaurantService $customerRestaurantService)
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $perPage;
        $search = $request->input('search', '');
        $categories = $request->input('categories', "");
        $response = $customerRestaurantService->getAllRestaurantData($search, $categories, $perPage, $page, $offset);
        return $this->sendResponse("Restaurant", $response, 200);
    }

    public function show(Restaurant $restaurant)
    {
        $menu = $restaurant->menus;

        $restaurantDetails = [
            "id" => $restaurant->id,
            "name" => $restaurant->name,
            "tenant_identifier" => $restaurant->tenant->tenant_identifier,
            "menu" => $menu

        ];
        return $this->sendResponse("Restaurant", $restaurantDetails, 200);
    }


    public function addToCart(Restaurant $restaurant, AddMenuToCartRequest $request)
    {
        $restaurant->tenant->makeCurrent();

        $user = Auth::user();
        $userId = $user->id;
        $cart = $user->cart->first();
        $menuId = $request->menu_id;
        $quantity = $request->quantity ?? 1;

        $menuPrice = Menu::find($menuId)->select(['price'])->first()->price;

        $totalPrice = $menuPrice * $quantity;

        if (!$cart) {
            DB::beginTransaction();
            $cart = Cart::create(
                ["user_id" => $userId]
            );
            $cart->cartItems()->create(
                [
                    "cart_id" => $cart->id,
                    "menu_id" => $menuId,
                    "quantity" => $quantity
                ]
            );

            $cart->update(
                [
                    "total" => $totalPrice
                ]
            );
            DB::commit();
            return $this->sendResponse("Items add", null, 200);
        }

        $cartItems = $cart->first()->cartItems;
        $previousPrice = $cart->total;

        if ($cartItems->where('menu_id', $menuId)->first()) {
            DB::beginTransaction();
            $previousQuantity = $cartItems->where('menu_id', $menuId)->first()->quantity;
            $cart->cartItems->where('menu_id', $menuId)->first()->update(
                [
                    "quantity" => $previousQuantity + $quantity
                ]
            );


            $cart->update(
                [
                    "total" => $totalPrice + $previousPrice
                ]
            );

            DB::commit();
        } else {
            DB::beginTransaction();

            $cart->cartItems()->create(
                [
                    "cart_id" => $cart->id,
                    "menu_id" => $menuId,
                    "quantity" => $quantity
                ]
            );


            $cart->update(
                [
                    "total" => $totalPrice + $previousPrice
                ]
            );

            DB::commit();
        }

        return $this->sendResponse("Items add", null, 200);
    }


    public function checkedOutCart(Restaurant $restaurant, Request $request)
    {

        //TODO:create better validation
        if (!$request->type) {
            return $this->sendError("Please choose order type", null, 400);
        }

        if (!in_array($request->type, ["DELIVERY", "PICKUP"])) {
            return $this->sendError("Please choose order type between DELIVERY or PICKUP", null, 400);
        }


        $user = Auth::user();
        $restaurant->tenant->makeCurrent();
        $activeCart = $user->cart->first();

        $activeCart->update(
            [
                "is_checked_out" => 1
            ]
        );


        //TODO:coupon feature
        if ($activeCart->coupon) {
        }

        Order::create(
            [
                "total_price" => $activeCart->total,
                "user_id" => $user->id,
                "cart_id" => $activeCart->id,
                "type" => $request->type,
                "discount_price" => 0,
                "status" => "PENDIG"
            ]
        );


        return $this->sendResponse("Cart checked out", null, 200);
    }
}
