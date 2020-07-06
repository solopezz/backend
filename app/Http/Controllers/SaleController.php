<?php

namespace App\Http\Controllers;

use App\Exports\SalesExport;
use App\Http\Controllers\ApiController;
use App\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class SaleController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $search = request()->search;
        return $this->showAll(Sale::orderBy('created_at', 'DESC')->filter($search)->with('products')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        return DB::transaction(function () use($request) {

            $sale = new Sale([
                'client' => $request->client,
                'folio' => $request->folio,
                'total' => $request->total,
            ]);

            $sale->save();
            $sale->products()->syncWithoutDetaching($request->product_sale);

            return $this->showOne($sale, 201);
        });
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
    public function destroy(Sale $sale)
    {
        return DB::transaction(function () use($sale) {

            $sale->products()->detach($sale->products->pluck('id'));

            $sale->delete();

            return $this->showAll(Sale::orderBy('created_at', 'DESC')->with('products')->get());
        });
    }

    public function range()
    {

        $start = request()->start;
        $end = request()->end;

        $sales = $this->sales($start, $end);

        return response()->json($sales, 202);

    }

    public function export()
    {
        $start = request()->start;
        $end = request()->end;

        $data = $this->sales($start, $end);
        $sum = $data->pluck('total')->sum();
       
        return Excel::download(new SalesExport($data, $sum), 'sales.xlsx');
    }

    public function sales($start, $end)
    {
        return Sale::orderBy('created_at', 'ASC')
        ->date($start, $end)
        ->with('products')
        ->get();
    }

}
