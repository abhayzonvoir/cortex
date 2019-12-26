<?php

declare(strict_types=1);

namespace Cortex\Auth\Http\Controllers\Tenantarea;

use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;
use Cortex\Foundation\Http\Controllers\AuthenticatedController;
use Cortex\Auth\Http\Requests\Tenantarea\AccountTwoFactorPhoneRequest;
use Cortex\Auth\Http\Requests\Tenantarea\AccountTwoFactorTotpBackupRequest;
use Cortex\Auth\Http\Requests\Tenantarea\AccountTwoFactorTotpProcessRequest;

class AccountTwoFactorController extends AuthenticatedController
{
    /**
     * Show account TwoFactor settings.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $twoFactor = $request->user($this->getGuard())->getTwoFactor();

        return view('cortex/auth::tenantarea.pages.account-twofactor', compact('twoFactor'));
    }

    /**
     * Enable TwoFactor TOTP authentication.
     *
     * @param \Illuminate\Http\Request      $request
     * @param \PragmaRX\Google2FA\Google2FA $totpProvider
     *
     * @return \Illuminate\View\View
     */
    public function enableTotp(Request $request, Google2FA $totpProvider)
    {
        $currentUser = $request->user($this->getGuard());
        $twoFactor = $currentUser->getTwoFactor();

        if (! $secret = array_get($twoFactor, 'totp.secret')) {
            $twoFactor['totp'] = [
                'enabled' => false,
                'secret' => $secret = $totpProvider->generateSecretKey(),
            ];

            $currentUser->fill(['two_factor' => $twoFactor])->forceSave();
        }

        $qrCode = $totpProvider->getQRCodeInline(config('app.name'), $currentUser->email, $secret);

        return view('cortex/auth::tenantarea.pages.account-twofactor-totp', compact('secret', 'qrCode', 'twoFactor'));
    }

    /**
     * Disable TwoFactor TOTP authentication.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function disableTotp(Request $request)
    {
        $currentUser = $request->user($this->getGuard());
        $twoFactor = $currentUser->getTwoFactor();
        $twoFactor['totp'] = [];

        $currentUser->fill(['two_factor' => $twoFactor])->forceSave();

        return intend([
            'back' => true,
            'with' => ['success' => trans('cortex/auth::messages.verification.twofactor.totp.disabled')],
        ]);
    }

    /**
     * Process the TwoFactor TOTP enable form.
     *
     * @param \Cortex\Auth\Http\Requests\Tenantarea\AccountTwoFactorTotpProcessRequest $request
     * @param \PragmaRX\Google2FA\Google2FA                                            $totpProvider
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function updateTotp(AccountTwoFactorTotpProcessRequest $request, Google2FA $totpProvider)
    {
        $currentUser = $request->user($this->getGuard());
        $twoFactor = $currentUser->getTwoFactor();
        $secret = array_get($twoFactor, 'totp.secret');
        $backup = array_get($twoFactor, 'totp.backup');
        $backupAt = array_get($twoFactor, 'totp.backup_at');

        if ($totpProvider->verifyKey($secret, $request->get('token'))) {
            $twoFactor['totp'] = [
                'enabled' => true,
                'secret' => $secret,
                'backup' => $backup ?? $this->generateTotpBackups(),
                'backup_at' => $backupAt ?? now()->toDateTimeString(),
            ];

            // Update TwoFactor settings
            $currentUser->fill(['two_factor' => $twoFactor])->forceSave();

            return intend([
                'back' => true,
                'with' => ['success' => trans('cortex/auth::messages.verification.twofactor.totp.enabled')],
            ]);
        }

        return intend([
            'back' => true,
            'withErrors' => ['token' => trans('cortex/auth::messages.verification.twofactor.totp.invalid_token')],
        ]);
    }

    /**
     * Process the TwoFactor OTP backup.
     *
     * @param \Cortex\Auth\Http\Requests\Tenantarea\AccountTwoFactorTotpBackupRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function backupTotp(AccountTwoFactorTotpBackupRequest $request)
    {
        $currentUser = $request->user($this->getGuard());
        $twoFactor = $currentUser->getTwoFactor();
        $twoFactor['totp']['backup'] = $this->generateTotpBackups();
        $twoFactor['totp']['backup_at'] = now()->toDateTimeString();

        $currentUser->fill(['two_factor' => $twoFactor])->forceSave();

        return intend([
            'back' => true,
            'with' => ['success' => trans('cortex/auth::messages.verification.twofactor.totp.rebackup')],
        ]);
    }

    /**
     * Enable TwoFactor Phone authentication.
     *
     * @param \Cortex\Auth\Http\Requests\Tenantarea\AccountTwoFactorPhoneRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function enablePhone(AccountTwoFactorPhoneRequest $request)
    {
        $currentUser = $request->user($this->getGuard());
        $currentUser->routeNotificationForAuthy();
        $twoFactor = $currentUser->getTwoFactor();
        $twoFactor['phone']['enabled'] = true;

        $currentUser->fill(['two_factor' => $twoFactor])->forceSave();

        return intend([
            'back' => true,
            'with' => ['success' => trans('cortex/auth::messages.verification.twofactor.phone.enabled')],
        ]);
    }

    /**
     * Disable TwoFactor Phone authentication.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function disablePhone(Request $request)
    {
        $currentUser = $request->user($this->getGuard());
        $twoFactor = $currentUser->getTwoFactor();
        $twoFactor['phone']['enabled'] = false;

        $currentUser->fill(['two_factor' => $twoFactor])->forceSave();

        return intend([
            'back' => true,
            'with' => ['success' => trans('cortex/auth::messages.verification.twofactor.phone.disabled')],
        ]);
    }

    /**
     * Generate TwoFactor OTP backup codes.
     *
     * @return array
     */
    protected function generateTotpBackups(): array
    {
        $backup = [];

        for ($x = 0; $x <= 9; $x++) {
            $backup[] = str_pad((string) random_int(0, 9999999999), 10, '0', STR_PAD_BOTH);
        }

        return $backup;
    }
}
