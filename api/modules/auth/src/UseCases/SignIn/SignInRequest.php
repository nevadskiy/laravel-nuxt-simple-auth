<?php

namespace Module\Auth\UseCases\SignIn;

use Illuminate\Foundation\Http\FormRequest;

class SignInRequest extends FormRequest
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
            'email' => ['bail', 'required', 'string', 'email', 'max:255'],
            'password' => ['bail', 'required', 'string', 'max:255'],
        ];
    }

    /**
     * Transform the request into the command.
     *
     * @return SignInCommand
     */
    public function toCommand(): SignInCommand
    {
        return new SignInCommand($this->get('email'), $this->get('password'), $this->ip());
    }
}
