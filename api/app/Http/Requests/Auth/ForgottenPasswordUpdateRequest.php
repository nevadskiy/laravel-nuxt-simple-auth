<?php

namespace App\Http\Requests\Auth;

use App\UseCases\Auth\ResetPassword\Command;
use Illuminate\Foundation\Http\FormRequest;

class ForgottenPasswordUpdateRequest extends FormRequest
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
            'token' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
        ];
    }

    /**
     * Transform the request into the command.
     *
     * @return Command
     */
    public function toCommand(): Command
    {
        return new Command($this->get('email'), $this->get('password'), $this->get('token'));
    }
}
