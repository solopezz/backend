<?php

namespace App;

use App\Sale;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
	use SoftDeletes;

	protected $dates = ['deleted_at'];
	
	protected $fillable = [
		'name', 
		'price',
		'img',
		'visible',
	];

	public function getNameAttribute($value)
	{
		return ucfirst($value);
	}

	public function getImgAttribute($value)
	{
		return asset('img/' . $value);
	}

	public function getVisibleAttribute($value)
	{
		return $value ? 1 : 0;
	}

	public function sales()
	{
		return $this->belongsToMany(Sale::class);
	}
}
