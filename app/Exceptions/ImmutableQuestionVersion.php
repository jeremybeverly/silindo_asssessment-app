<?php

namespace App\Exceptions;

use RuntimeException;

final class ImmutableQuestionVersion extends RuntimeException
{
    public static function cannotUpdate(): self
    {
        return new self('Question versions are immutable. Edit the question to create a new version.');
    }

    public static function cannotDelete(): self
    {
        return new self('Question versions cannot be deleted. Archive the question instead.');
    }
}
