<?php

namespace App\Http\Controllers\API;

use App\Models\Food;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\ResponseFormatter;

class FoodController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit');
        $name = $request->input('name');
        $types = $request->input('types');

        $rate_from = $request->input('rate_from');
        $rate_to = $request->input('rate_to');

        $price_from = $request->input('price_from');
        $price_to = $request->input('price_to');

        if ($id) {
            $food = Food::find($id);

            if ($food) {
                return ResponseFormatter::success(
                    [$food],
                    'Food Product is retrieved successfully'
                );
            } else {

                return ResponseFormatter::error(
                    null,
                    'Food Product was not found',
                    404
                );
            }
        }

        $food = Food::query();

        if ($name) {
            $food->where('name', 'like', '%' . $name . '%');
        }

        if ($types) {
            $food->where('types', 'like', '%' . $types . '%');
        }

        if ($price_from) {
            $food->where('price', '>=', $price_from);
        }

        if ($price_to) {
            $food->where('price', '<=', $price_to);
        }

        if ($rate_from) {
            $food->where('rate', '>=', $rate_from);
        }

        if ($rate_to) {
            $food->where('rate', '<=', $rate_to);
        }

        return ResponseFormatter::success(
            $food->paginate($limit),
            'Food Products retrieved successfully',
        );
    }
}
