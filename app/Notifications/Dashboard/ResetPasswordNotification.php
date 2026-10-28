<?php

namespace App\Notifications\Dashboard;

use Illuminate\Auth\Notifications\ResetPassword;

/**
 * Password reset email for dashboard staff, linking to the dashboard reset page.
 */
class ResetPasswordNotification extends ResetPassword
{
    /**
     * Get the reset URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function resetUrl($notifiable)
    {
        return route('dashboard.password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);
    }
}
