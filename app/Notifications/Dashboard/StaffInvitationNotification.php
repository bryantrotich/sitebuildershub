<?php

namespace App\Notifications\Dashboard;

use Illuminate\Notifications\Messages\MailMessage;

/**
 * Invitation for a newly added staff member to set their dashboard password.
 */
class StaffInvitationNotification extends ResetPasswordNotification
{
    /**
     * Build the invitation email.
     *
     * @param  string  $url
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    protected function buildMailMessage($url)
    {
        return (new MailMessage)
            ->subject('You have been added to the '.config('app.name').' dashboard')
            ->line('An administrator has created a staff account for you on the '.config('app.name').' dashboard.')
            ->action('Set Your Password', $url)
            ->line('This link expires in '.config('auth.passwords.'.config('auth.defaults.passwords').'.expire').' minutes. If it expires, use "Forgot password?" on the dashboard login page to get a new one.');
    }
}
