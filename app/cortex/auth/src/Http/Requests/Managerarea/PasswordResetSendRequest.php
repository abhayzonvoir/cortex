<?php

declare(strict_types=1);

namespace Cortex\Auth\Http\Requests\Managerarea;

class PasswordResetSendRequest extends PasswordResetRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|min:3|max:150|exists:'.config('cortex.auth.tables.managers').',email',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getRedirectUrl()
    {
        return $this->redirector->getUrlGenerator()->route('managerarea.passwordreset.request');
    }
}
