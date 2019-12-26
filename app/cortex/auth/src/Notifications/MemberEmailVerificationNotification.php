<?php

declare(strict_types=1);

namespace Cortex\Auth\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class MemberEmailVerificationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The callback that should be used to build the mail message.
     *
     * @var \Closure|null
     */
    public static $toMailCallback;

    /**
     * The email verification token.
     *
     * @var string
     */
    public $token;

    /**
     * The email verification expiration date.
     *
     * @var int
     */
    public $expiration;

    /**
     * Create a notification instance.
     *
     * @param string $token
     * @param int    $expiration
     */
    public function __construct(string $token, int $expiration)
    {
        $this->token = $token;
        $this->expiration = $expiration;
    }

    /**
     * Get the notification's channels.
     *
     * @param mixed $notifiable
     *
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable);
        }

        $email = $notifiable->getEmailForVerification();
        $link = route('frontarea.verification.email.verify')."?email={$email}&expiration={$this->expiration}&token={$this->token}";

        return (new MailMessage())
            ->subject(trans('cortex/auth::emails.verification.email.subject'))
            ->line(trans('cortex/auth::emails.verification.email.intro', ['expire' => now()->createFromTimestamp($this->expiration)->diffForHumans()]))
            ->action(trans('cortex/auth::emails.verification.email.action'), $link)
            ->line(trans('cortex/auth::emails.verification.email.outro', ['created_at' => now(), 'ip' => request()->ip(), 'agent' => request()->userAgent()]));
    }

    /**
     * Set a callback that should be used when building the notification mail message.
     *
     * @param \Closure $callback
     *
     * @return void
     */
    public static function toMailUsing($callback)
    {
        static::$toMailCallback = $callback;
    }
}
