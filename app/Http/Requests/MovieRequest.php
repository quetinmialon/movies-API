<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MovieRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => [Rule::requiredIf($this->isMethod('POST')), 'string', 'min:3', 'max:255'],
            'synopsis' => [Rule::requiredIf($this->isMethod('POST')), 'string', 'min:3'],
            'release' => [Rule::requiredIf($this->isMethod('POST')), 'date'],
            'duration' => [Rule::requiredIf($this->isMethod('POST')), 'integer', 'min:1'],
            'director_id' => [Rule::requiredIf($this->isMethod('POST')), 'integer', 'exists:directors,id'],
            'actors' => ['array', 'exists:actors,id'],
        ];
    }
}
