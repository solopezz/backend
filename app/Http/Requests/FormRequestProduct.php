<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FormRequestProduct extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'price' => 'required|numeric',
            'img' => ' image',
            'visible' => ' bool'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre es requerido',
            'price.required'  => 'El precio es requerido',
            'price.numeric'  => 'El precio debe de ser un numero',
            'img.image'  => 'Seleccioné una imagen correcta',
            'visible.bool'  => 'El producto visble debe ser verdadero o falso',

        ];
    }
}
