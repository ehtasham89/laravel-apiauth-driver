<?php

namespace LvApiAuth\Reminders;

use AceTicket\LvApiAuth\Reminders\User;

interface ReminderRepositoryInterface
{
    /**
     * Create a new reminder record and token.
     *
     * @param \Illuminate\Auth\Reminders\RemindableInterface $user
     *
     * @return string
     */
    public function create(User $user);

    /**
     * Determine if a reminder record exists and is valid.
     *
     * @param \Illuminate\Auth\Reminders\RemindableInterface $user
     * @param string                                         $token
     *
     * @return bool
     */
    public function exists(User $user, $token);

    /**
     * Delete a reminder record by token.
     *
     * @param string $token
     *
     * @return void
     */
    public function delete($token);

    /**
     * Delete expired reminders.
     *
     * @return void
     */
    public function deleteExpired();
}
