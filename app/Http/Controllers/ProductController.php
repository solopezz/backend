<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiController;
use App\Http\Requests\FormRequestProduct;
use App\Product;
use App\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->sales) {
            $products = Product::orderBy('name')->get();
            $count = Sale::withTrashed()->get()->count();
            $count = $count ? ++$count : 1;
            return response()->json([
                'products' => $products,
                'count' => $count,
            ], 200);
        }
        return $this->showAll(Product::orderBy('name')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FormRequestProduct $request)
    {
        $validated = $request->validated();

        if ($request->has('img')) {
            $validated['img'] = $request->img->store('');

        }else{
            $validated['img'] = 'default.jpg';
        }

        Product::create($validated);

        return $this->showAll(Product::orderBy('name')->get());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return $this->showOne($product, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(FormRequestProduct $request, Product $product)
    {

        $product->fill($request->only([
            'name',
            'price',
            'visible',
        ]));

        if ($request->has('img')) {

            $url = explode('/', $product->img);
            
            if ($url[4] != 'default.jpg') {
                Storage::delete($url[4]);
            }

            $product->img = $request->img->store('');

        }

        if ($product->isClean()) {
            return $this->errorResponse('Se debe de especificar al menos un cambio', 422);
        }

        $product->save();

        return $this->showOne($product, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {

        $url = explode('/', $product->img);
        if ($url[4] != 'default.jpg') {
            Storage::delete($url[4]);
        }

        $product->delete();

        return $this->showAll(Product::orderBy('name')->get());

    }
}
