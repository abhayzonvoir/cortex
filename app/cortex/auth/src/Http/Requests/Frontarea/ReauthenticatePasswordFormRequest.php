<?php

declare(strict_types=1);

namespace Cortex\Auth\Http\Requests\Frontarea;

use Illuminate\Foundation\Http\FormRequest;

class ReauthenticatePasswordFormRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'password' => 'required|password',
        ];
    }
}
