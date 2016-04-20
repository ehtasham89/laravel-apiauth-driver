<?php

namespace Ehtasham89\LvApiAuth\Reminders;

use Illuminate\Auth\Reminders\RemindableInterface;

class User implements RemindableInterface
{
    protected $user;

    public function __construct($userInfo)
    {
        $this->user = $userInfo;
    }

    /**
     * Get the e-mail address where password reminders are sent.
     *
     * @return string
     */
    public function getReminderEmail()
    {
        return isset($this->user->email) ? $this->user->email : null;
    }

    public function getId()
    {
        return isset($this->user->id) ? $this->user->id : null;
    }
}
