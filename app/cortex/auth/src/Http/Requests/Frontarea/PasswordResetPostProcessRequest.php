<?php

declare(strict_types=1);

namespace Cortex\Auth\Http\Requests\Frontarea;

class PasswordResetPostProcessRequest extends PasswordResetRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            //'token' => 'required|regex:/^([0-9a-f]*)$/',
            //'expiration' => 'required|date_format:U',
            // Do not validate `token` or `expiration` here since at this stage we can NOT generate viewable
            // error, and it is been processed in the controller through EmailVerificationBroker anyway
            'email' => 'required|email|min:3|max:150|exists:'.config('cortex.auth.tables.members').',email',
            'password' => 'required|confirmed|min:'.config('cortex.auth.password_min_chars'),
        ];
    }
}
