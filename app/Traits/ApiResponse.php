<?php 	

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

trait ApiResponse 
{
	private function successResponse($data, $code)
	{
		return response()->json($data,$code);
	}

	protected function errorResponse($message, $code)
	{	
		return response()->json(['errors' => response()->json($message)], $code);
	}

	protected function showAll(Collection $collection, $code = 200)
	{	

		if ($collection->isEmpty()) {
			return $this->successResponse(['data' => $collection], $code);
		}
		
		$collection = $this->paginate($collection);

		return $this->successResponse($collection, $code);
	}

	protected function showOne(Model $instance, $code = 200)
	{
		return $this->successResponse($instance, $code);
	}

	protected function showMessage($message, $code = 200)
	{
		return response()->json(['data' => $message], $code);
	}


	protected function filterData(Collection $collection)
	{

		// foreach (request()->query() as $query => $value) {
		// 	//?nombre=salvador $query=nombre y $value=salvador
		// 	//Buscamos el valor normal de la porpiedad del modelo
		// 	$attribute = $transformer::originalAttribute($query);
		// 	//verificamos si hay valores 
		// 	if (isset($attribute, $value)) {
		// 		//buscamos si hay concidencias
		// 		$collection = $collection->where($attribute, $value);
		// 	}
		// }

		return $collection;

	}

	protected function sortData(Collection $collection)
	{

		$collection = $collection->sortby($attribute);

		return $collection;
	}

	protected function paginate(Collection $collection)
	{
		//Validamos si mandamos un tamaño para la paginacion
		Validator::make(request()->all(), [
			'per_page' => 'integer|min:2|max:50'
        ])->validate();

		//Aqui se toma el valoer page de url ?page=4
		$page = LengthAwarePaginator::resolveCurrentPage();

		//tamaño de la collecion debuelta
		$perPage = 5;
		if (request()->has('per_page')) {
			$perPage  = (int)request()->per_page;
		}
		// dd($page);
		//tamaño tolat de la collecion 
		$total = $collection->count();

		//El forPagemétodo devuelve una nueva colección que contiene los elementos que estarían presentes en un número de página determinado aqui se trae solo los elementos que se mostraran en la paginacion
		$result = $collection->slice(($page - 1) * $perPage, $perPage)->values();;

		$paginate =  new LengthAwarePaginator($result, $total, $perPage, $page, [
        	'path' => LengthAwarePaginator::resolveCurrentPath(),
  		]);

		//en el path agregmaos los demas parametros de la url para que no se pierdan por ejemplo next:apiresful.dev/users?page=2&sort_by=id....
		$paginate->appends(request()->input())->links();

		return $paginate;

	}

	//el usar transofrmaciones le da un poco mas de seguridad a nuestra BD no se expone nuestra estructura de ella
	protected function transformData($data, $transformer)
	{

		$transformation = fractal($data, new $transformer);

		return $transformation->toArray();
	}

}