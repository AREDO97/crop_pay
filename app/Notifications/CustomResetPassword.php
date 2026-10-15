<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;

class CustomResetPassword extends ResetPassword
{
    public function __construct(
        $token,
        protected string $frontendUrl
    ) {
        parent::__construct($token);
    }

    protected function resetUrl($notifiable)
    {
        return $this->frontendUrl;
    }
}