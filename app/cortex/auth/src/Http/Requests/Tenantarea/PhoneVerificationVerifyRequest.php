<?php

declare(strict_types=1);

namespace Cortex\Auth\Http\Requests\Tenantarea;

use Cortex\Foundation\Exceptions\GenericException;

class PhoneVerificationVerifyRequest extends PhoneVerificationRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @throws \Cortex\Foundation\Exceptions\GenericException
     *
     * @return bool
     */
    public function authorize(): bool
    {
        parent::authorize();

        $user = $this->user($this->route('guard'))
                ?? $this->attemptUser($this->route('guard'))
                   ?? app('cortex.auth.member')->whereNotNull('phone')->where('phone', $this->get('phone'))->first();

        if (! $user) {
            // User instance required to detect active TwoFactor methods
            throw new GenericException(trans('cortex/foundation::messages.session_required'), route('tenantarea.login'));
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [];
    }
}
