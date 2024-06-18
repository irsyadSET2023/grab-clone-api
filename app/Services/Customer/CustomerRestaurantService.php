<?php

namespace App\Services\Customer;

use App\Http\Resources\SimpleMenuResource;
use Illuminate\Support\Facades\DB;

class CustomerRestaurantService
{
    public function getAllRestaurantData($search = null, $categories = null, $perPage = 5, $page = 1, $offset = 0)
    {
        $restaurantsQuery = "
                    SELECT 
                        r.*,
                        JSON_ARRAYAGG(
                            JSON_OBJECT(
                                'name', m.name, 
                                'price', m.price
                            )
                        ) AS menus,
                        JSON_ARRAYAGG(
                            JSON_OBJECT(
                                'id', c.id,
                                'name', c.name
                            )
                        ) AS categories
                    FROM 
                        grabclone_landlord.restaurants r
                    LEFT JOIN (
                        SELECT 
                            id, 
                            name, 
                            price, 
                            restaurant_id,
                            ROW_NUMBER() OVER (PARTITION BY restaurant_id ORDER BY id) as rn
                        FROM 
                            grabclone_landlord.menus
                    ) m ON r.id = m.restaurant_id AND m.rn <= 3
                    LEFT JOIN 
                        grabclone_landlord.restaurant_categories rc ON r.id = rc.restaurant_id
                    LEFT JOIN 
                        grabclone_landlord.categories c ON rc.category_id = c.id ";

        $restaurantsCountQuery = "
                    SELECT COUNT(*) AS restaurant_count FROM 
                    (
                        SELECT 
                            r.id
                        FROM 
                            grabclone_landlord.restaurants r
                        LEFT JOIN (
                            SELECT 
                                id, 
                                name, 
                                price, 
                                restaurant_id,
                                ROW_NUMBER() OVER (PARTITION BY restaurant_id ORDER BY id) as rn
                            FROM 
                                grabclone_landlord.menus
                        ) m ON r.id = m.restaurant_id AND m.rn <= 3
                        LEFT JOIN 
                            grabclone_landlord.restaurant_categories rc ON r.id = rc.restaurant_id
                        LEFT JOIN 
                            grabclone_landlord.categories c ON rc.category_id = c.id ";

        $conditions = [];

        if ($search) {
            $conditions[] = "r.name LIKE '%$search%'";
        }

        if ($categories) {
            $conditions[] = "c.id IN ($categories)";
        }

        if (!empty($conditions)) {
            $restaurantsQuery .= "WHERE " . implode(" AND ", $conditions) . " ";
            $restaurantsCountQuery .= "WHERE " . implode(" AND ", $conditions) . " ";
        }

        $restaurantsQuery .= "GROUP BY 
        r.id
    ORDER BY 
        r.created_at
    LIMIT $perPage OFFSET $offset;";

        $restaurantsCountQuery .= "GROUP BY 
        r.id
    ORDER BY 
        r.created_at
) AS subquery;";


        // dd($restaurantsQuery);
        $restaurants = collect(DB::select(DB::raw($restaurantsQuery)));
        $restaurantsCount = collect(DB::select(DB::raw($restaurantsCountQuery)));


        $mappedRestaurants = $restaurants->map(function ($restaurant) {
            $categories
                = collect(json_decode($restaurant->categories, true))->unique();
            $menu = collect(json_decode($restaurant->menus, true))->unique();
            return ["id" => $restaurant->id, "name" => $restaurant->name, "menu" => SimpleMenuResource::collection($menu), "categories" => $categories];
        });
        $totalCount =
            $restaurantsCount[0]->restaurant_count;

        $meta = [
            'total' => $totalCount,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($totalCount / $perPage)
        ];

        return ["data" => $mappedRestaurants, "meta" => $meta];
    }
}
