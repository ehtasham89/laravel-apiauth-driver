<?php

namespace Ehtasham89\LvApiAuth\Reminders;

use Carbon\Carbon;
use Config;
use Illuminate\Database\Connection;

class DatabaseReminderRepository implements ReminderRepositoryInterface
{
    /**
     * The hashing key.
     *
     * @var string
     */
    protected $hashKey;

    /**
     * The number of seconds a reminder should last.
     *
     * @var int
     */
    protected $expires;

    /**
     * Create a new reminder repository instance.
     *
     * @param \Illuminate\Database\Connection $connection
     * @param string                          $table
     * @param string                          $hashKey
     * @param int                             $expires
     *
     * @return void
     */
    private $guzzle;

    private $options;

    public function __construct(Connection $connection, $table, $hashKey, $expires = 60)
    {
        $this->hashKey = $hashKey;
        $this->expires = $expires * 60;

        $this->guzzle = new \GuzzleHttp\Client(['base_url' => Config::get('laravel-apiauth-driver::base_url')]);

        $this->options = [
            'debug'      => false,
            'headers'    => [
                'Authorization' => 'Bearer '.Config::get('laravel-apiauth-driver::static_token'),
            ],
        ];
    }

    /**
     * Create a new reminder record and token.
     *
     * @param \Illuminate\Auth\Reminders\RemindableInterface $user
     *
     * @return string
     */
    public function create(User $user)
    {
        $email = $user->getReminderEmail();

        $this->deleteExisting($user);

        // We will create a new, random token for the user so that we can e-mail them
        // a safe link to the password reset form. Then we will insert a record in
        // the database so that we can verify the token within the actual reset.
        $token = $this->createNewToken($user);

        $this->options['query'] = $this->getPayload($email, $token);

        try {
            $this->guzzle->post(Config::get('laravel-apiauth-driver::insert_reminder_ep'), $this->options);
        } catch (\Exception $e) {
            throw $e;
        }

        return $token;
    }

    /**
     * Delete all existing reset tokens from the database.
     *
     * @param \Illuminate\Auth\Reminders\RemindableInterface $user
     *
     * @return int
     */
    protected function deleteExisting(User $user)
    {
        $this->options['query'] = [
            'email' => $user->getReminderEmail(),
        ];

        try {
            $response = $this->guzzle->post(Config::get('laravel-apiauth-driver::del_reminders_ep'), $this->options);

            $response = $response->getBody();

            $response = json_decode($response);

            if (isset($response->status) && $response->status) {
                return true;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Build the record payload for the table.
     *
     * @param string $email
     * @param string $token
     *
     * @return array
     */
    protected function getPayload($email, $token)
    {
        return ['email' => $email, 'reminder_token' => $token, 'created_at' => new Carbon()];
    }

    /**
     * Determine if a reminder record exists and is valid.
     *
     * @param \Illuminate\Auth\Reminders\RemindableInterface $user
     * @param string                                         $token
     *
     * @return bool
     */
    public function exists(User $user, $token)
    {
        $this->options['query'] = [
            'email'          => $user->getReminderEmail(),
            'reminder_token' => $token,
        ];

        try {
            $response = $this->guzzle->post(Config::get('laravel-apiauth-driver::exits_reminder_ep'), $this->options);

            $response = $response->getBody();

            $response = json_decode($response);

            if (isset($response->status) && $response->status) {
                $reminder = (array) $response->data;

                return $reminder && !$this->reminderExpired($reminder);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Determine if the reminder has expired.
     *
     * @param array $reminder
     *
     * @return bool
     */
    protected function reminderExpired($reminder)
    {
        $createdPlusHour = strtotime($reminder['created_at']) + $this->expires;

        return $createdPlusHour < $this->getCurrentTime();
    }

    /**
     * Get the current UNIX timestamp.
     *
     * @return int
     */
    protected function getCurrentTime()
    {
        return time();
    }

    /**
     * Delete a reminder record by token.
     *
     * @param string $token
     *
     * @return void
     */
    public function delete($token)
    {
        $this->options['query'] = [
            'reminder_token' => $token,
        ];

        try {
            $this->guzzle->post(Config::get('laravel-apiauth-driver::del_reminders_ep'), $this->options);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete expired reminders.
     *
     * @return void
     */
    public function deleteExpired()
    {
        $expired = Carbon::now()->subSeconds($this->expires);

        $this->options['query'] = [
            'created_at' => $expired,
        ];

        try {
            $this->guzzle->post(Config::get('laravel-apiauth-driver::del_reminders_ep'), $this->options);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Create a new token for the user.
     *
     * @param \Illuminate\Auth\Reminders\RemindableInterface $user
     *
     * @return string
     */
    public function createNewToken(User $user)
    {
        $email = $user->getReminderEmail();

        $value = str_shuffle(sha1($email.spl_object_hash($this).microtime(true)));

        return hash_hmac('sha1', $value, $this->hashKey);
    }
}
