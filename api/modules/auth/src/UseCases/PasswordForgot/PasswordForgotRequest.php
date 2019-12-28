<?php

namespace Module\Auth\UseCases\PasswordForgot;

use Illuminate\Foundation\Http\FormRequest;

class PasswordForgotRequest extends FormRequest
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
        ];
    }

    /**
     * Transform the request into the command.
     *
     * @return PasswordForgotCommand
     */
    public function toCommand(): PasswordForgotCommand
    {
        return new PasswordForgotCommand($this->get('email'));
    }
}
