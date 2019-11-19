<?php

namespace App\Auth\Http\Requests;

use App\Auth\UseCases\SignUp\Command;
use Illuminate\Foundation\Http\FormRequest;

class SignUpStoreRequest extends FormRequest
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
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
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
        return new Command($this->get('email'), $this->get('password'));
    }
}
