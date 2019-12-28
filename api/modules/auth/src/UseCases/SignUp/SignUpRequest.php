<?php

namespace Module\Auth\UseCases\SignUp;

use Illuminate\Foundation\Http\FormRequest;

class SignUpRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => ['bail', 'required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['bail', 'required', 'string', 'min:8', 'max:255'],
        ];
    }

    /**
     * Transform the request into the command.
     *
     * @return SignUpCommand
     */
    public function toCommand(): SignUpCommand
    {
        return new SignUpCommand($this->get('email'), $this->get('password'));
    }
}
