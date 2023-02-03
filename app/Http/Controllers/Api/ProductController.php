<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index (Request $request)
    {
        // Filter data if category is sent
        if (request()->filled('category')) {
            return Product::where('category_id', '=', $request->category)->get();
        }
        return Product::all();
    }
}
