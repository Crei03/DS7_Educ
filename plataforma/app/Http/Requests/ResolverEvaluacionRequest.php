<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResolverEvaluacionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // La autorización se maneja en el controlador
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'respuestas' => 'required|array|min:1',
            'respuestas.*' => 'required|integer|exists:opciones,id'
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'respuestas.required' => 'Debe proporcionar al menos una respuesta',
            'respuestas.array' => 'Las respuestas deben ser un array',
            'respuestas.min' => 'Debe responder al menos una pregunta',
            'respuestas.*.required' => 'Todas las respuestas son requeridas',
            'respuestas.*.integer' => 'Las respuestas deben ser números enteros',
            'respuestas.*.exists' => 'Una o más respuestas no son válidas'
        ];
    }
}
