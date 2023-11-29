<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductImageResource;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;

class ProductImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Product $product, Request $request){

        $request->validate([
            'product_id' => ['exists:products,id'],
            'file' => 'required|mimes:jpeg,jpg,png|max:2048',
        ]);

        if($request->file()) {
            $fileName = time().'_'.$request->file->getClientOriginalName();
            $filePath = $request->file('file')->storeAs('uploads', $fileName, 'public');

            $productImage = new ProductImage();
            $productImage->product_id = $request->product_id;
            $productImage->name = $request->file->getClientOriginalName();
            $productImage->path = "storage/$filePath";
            $productImage->save();

            $product = $productImage->product;
            if(!$product->primary_image && $product->images){
                $product->latest_image->update([
                    'is_primary' => 1
                ]);
            }



            return new ProductImageResource($productImage);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductImage $productImage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductImage $productImage)
    {
        //
    }


    public function update(Request $request, ProductImage $productImage)
    {
        if(isset($request->is_primary) && $request->is_primary){
            ProductImage::where('product_id',$productImage->product_id)
            ->update(['is_primary' => 0]);

            $productImage->is_primary = 1;
            $productImage->save();
        }

        return new ProductImageResource($productImage);
    }


    public function destroy(ProductImage $productImage)
    {
        $productImage->delete();
        return response()->json([
            'type' => 'success',
            'message' => 'The record has been removed'
        ]);
    }
}
