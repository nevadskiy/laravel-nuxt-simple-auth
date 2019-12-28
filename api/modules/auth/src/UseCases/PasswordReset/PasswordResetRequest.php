<?php

namespace Module\Auth\UseCases\PasswordReset;

use Illuminate\Foundation\Http\FormRequest;

class PasswordResetRequest extends FormRequest
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
            'token' => ['bail', 'required', 'string', 'max:255'],
            'email' => ['bail', 'required', 'string', 'email', 'max:255'],
            'password' => ['bail', 'required', 'string', 'min:8', 'max:255'],
        ];
    }

    /**
     * Transform the request into the command.
     *
     * @return PasswordResetCommand
     */
    public function toCommand(): PasswordResetCommand
    {
        return new PasswordResetCommand($this->get('email'), $this->get('password'), $this->get('token'));
    }
}
