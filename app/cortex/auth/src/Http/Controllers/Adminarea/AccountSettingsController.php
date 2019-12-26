<?php

declare(strict_types=1);

namespace Cortex\Auth\Http\Controllers\Adminarea;

use Illuminate\Http\Request;
use Cortex\Auth\Http\Requests\Adminarea\AccountSettingsRequest;
use Cortex\Foundation\Http\Controllers\AuthenticatedController;

class AccountSettingsController extends AuthenticatedController
{
    /**
     * Redirect to account settings..
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        return view('cortex/auth::adminarea.pages.account-index');
    }

    /**
     * Edit account settings.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\View\View
     */
    public function edit(Request $request)
    {
        $countries = collect(countries())->map(function ($country, $code) {
            return [
                'id' => $code,
                'text' => $country['name'],
                'emoji' => $country['emoji'],
            ];
        })->values();
        $languages = collect(languages())->pluck('name', 'iso_639_1');
        $genders = ['male' => trans('cortex/auth::common.male'), 'female' => trans('cortex/auth::common.female')];

        return view('cortex/auth::adminarea.pages.account-settings', compact('countries', 'languages', 'genders'));
    }

    /**
     * Update account settings.
     *
     * @param \Cortex\Auth\Http\Requests\Adminarea\AccountSettingsRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(AccountSettingsRequest $request)
    {
        $data = $request->validated();
        $currentUser = $request->user($this->getGuard());

        ! $request->hasFile('profile_picture')
        || $currentUser->addMediaFromRequest('profile_picture')
                       ->sanitizingFileName(function ($fileName) {
                           return md5($fileName).'.'.pathinfo($fileName, PATHINFO_EXTENSION);
                       })
                       ->toMediaCollection('profile_picture', config('cortex.foundation.media.disk'));

        ! $request->hasFile('cover_photo')
        || $currentUser->addMediaFromRequest('cover_photo')
                       ->sanitizingFileName(function ($fileName) {
                           return md5($fileName).'.'.pathinfo($fileName, PATHINFO_EXTENSION);
                       })
                       ->toMediaCollection('cover_photo', config('cortex.foundation.media.disk'));

        // Update profile
        $currentUser->fill($data)->save();

        return intend([
            'back' => true,
            'with' => ['success' => trans('cortex/auth::messages.account.updated_account')]
                      + (isset($data['two_factor']) ? ['warning' => trans('cortex/auth::messages.verification.twofactor.phone.auto_disabled')] : []),
        ]);
    }
}
