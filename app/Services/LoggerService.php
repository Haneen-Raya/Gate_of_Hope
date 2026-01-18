<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Spatie\Activitylog\Models\Activity;

/**
 * LoggerService
 *
 * A centralized service for structured and contextual activity logging
 * using Spatie's activitylog package.
 *
 * Supports default logging with contextual data (IP, URL, User Agent),
 * and includes specialized methods for authentication and forbidden access logging.
 *
 * @package App\Services
 */
class LoggerService
{
    /**
     * Logs an activity with contextual information
     *
     * @param string $event      The event identifier (e.g., 'login', 'register', 'forbidden')
     * @param string $message    Descriptive log message
     * @param array $properties  Additional structured data for the log (optional)
     * @param string $logName    Name of the log channel (default: 'default')
     * @param mixed|null $subject The target entity or model (optional)
     * @param mixed|null $user    The actor responsible for the action. Defaults to authenticated user
     *
     * @return Activity The logged activity instance
     */
    public function log(
        string $event,
        string $message,
        array $properties = [],
        string $logName = 'default',
        $subject = null,
        $user = null
    ): Activity {
        $logger = activity()
            ->causedBy($user ?? Auth::user())
            ->withProperties(array_merge($properties, $this->defaultContext()))
            ->useLog($logName)
            ->event($event);

        if ($subject != null)
            $logger->performedOn($subject);

        return $logger->log($message);
    }

    /**
     * Logs an authentication-related event
     *
     * @param string $event     The auth event name (e.g., 'login', 'logout')
     * @param string $message   Description of the event
     * @param array $properties Extra context for the event (optional)
     *
     * @return Activity
     */
    public function auth(string $event, string $message, array $properties = [], $subject = null): Activity
    {
        return $this->log($event, $message, $properties, 'auth', $subject);
    }

    /**
     * Logs a forbidden access attempt
     *
     * @param string $message   The message to log
     * @param array $properties Additional details (e.g., route, user role)
     *
     * @return Activity
     */
    public function security(string $event, string $message, array $properties = []): Activity
    {
        return $this->log($event, $message, $properties, 'security');
    }

    /**
     * Logs a failed validation
     *
     * @param string $message   The message to log
     * @param array $properties Additional details (e.g., route, user role)
     *
     * @return Activity
     */
    public function failedValidation(string $event, string $message, array $properties = []): Activity
    {
        return $this->log($event, $message, $properties, 'failed-validation');
    }

    /**
     * Returns default context for all logs (IP, URL, user agent)
     *
     * @return array{
     *     ip: string|null,
     *     url: string|null,
     *     agent: string|null
     * }
     */
    protected function defaultContext(): array
    {
        return [
            'ip'    => Request::ip(),
            'url'   => Request::fullUrl(),
            'agent' => Request::userAgent(),
        ];
    }
}
