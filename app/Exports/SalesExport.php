<?php

namespace App\Exports;

use App\Sale;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SalesExport implements FromView
{
	private $data;
	private $sum;
	public function __construct($data, $sum)
	{
		$this->data = $data;
		$this->sum = $sum;
	}


    /**
    * @return \Illuminate\Support\Collection
    */

    public function view(): View
    {
    	return view('exports.sales', [
    		'data' => $this->data,
    		'sum' => $this->sum
    	]);
    }

}
