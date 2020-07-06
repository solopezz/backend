<?php

namespace App;

use App\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
	use SoftDeletes;
	protected $softDelete = true;

	protected $dates = ['deleted_at'];
	// protected $casts = [
	// 	'created_at' => 'datetime:d/m/Y',
	// ];
	protected $fillable = [
		'client',
		'folio',
		'total',
	];

	public function getClientAttribute($value)
	{
		return ucfirst($value);
	}

	public function getCreatedAtAttribute($value)
	{
		return $value;
		// dd($value);
		// $date = Carbon::parse($value);
		// return $date->format("d/m/Y");
	}


	public function scopeFilter($query, $string){

		if (!$string) {
			return;
		}

		$field = ['client', 'folio', 'created_at'];
		$query->where(function ($query) use($field, $string) {
			for ($i = 0; $i < count($field); $i++){
				if ($field[$i] == 'created_at') {
					$var = explode('/', $string);
					$string = count($var) == 3 ? date('Y-m-d', strtotime(str_replace('/', '-', $string))) : $string;
				}
				$query->orwhere($field[$i], 'like',  '%'. $string .'%');
			}      
		});
	}

	public function scopeDate($query, $start, $end){

		if (!$start) {
			return;
		}

		$query->whereBetween('created_at', [$start.' 00:00:00', $end.' 23:59:59']);
	}

	public function products()
	{
		return $this->belongsToMany(Product::class)->withPivot('quantity','amount');
	}
}
