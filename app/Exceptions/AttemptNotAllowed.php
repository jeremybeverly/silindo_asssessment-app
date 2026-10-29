<?php

namespace App\Exceptions;

use RuntimeException;

final class AttemptNotAllowed extends RuntimeException
{
    private function __construct(string $message, public readonly string $reason)
    {
        parent::__construct($message);
    }

    public static function revoked(): self
    {
        return new self('The invitation has been revoked.', 'revoked');
    }

    public static function outsideWindow(): self
    {
        return new self('The invitation is outside its access window.', 'outside_window');
    }

    public static function alreadyStarted(): self
    {
        return new self('This invitation has already been used for an attempt.', 'already_started');
    }

    public static function noQuestions(): self
    {
        return new self('No active questions are available.', 'no_questions');
    }
}
