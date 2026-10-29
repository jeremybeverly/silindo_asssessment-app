<?php

namespace App\Actions\Assessment;

use App\Exceptions\AttemptNotAllowed;
use App\Models\Attempt;
use App\Models\Invitation;
use App\Models\Question;
use App\Models\QuestionVersion;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Starts the single attempt allowed for an invitation and freezes the
 * randomized question order for it (D-019, D-020).
 */
final class StartAttempt
{
    public function __invoke(Invitation $invitation): Attempt
    {
        if ($invitation->isRevoked()) {
            throw AttemptNotAllowed::revoked();
        }

        $now = now();

        if (! $invitation->isWithinWindow($now)) {
            throw AttemptNotAllowed::outsideWindow();
        }

        if ($invitation->attempt()->exists()) {
            throw AttemptNotAllowed::alreadyStarted();
        }

        $versions = $this->latestActiveVersions();

        if ($versions->isEmpty()) {
            throw AttemptNotAllowed::noQuestions();
        }

        return DB::transaction(function () use ($invitation, $now, $versions): Attempt {
            $attempt = $invitation->attempt()->create([
                'state' => Attempt::STATE_IN_PROGRESS,
                'started_at' => $now,
            ]);

            foreach ($versions as $index => $version) {
                $attempt->questions()->create([
                    'question_version_id' => $version->id,
                    'position' => $index + 1,
                ]);
            }

            return $attempt->load('questions');
        });
    }

    /**
     * @return Collection<int, QuestionVersion>
     */
    private function latestActiveVersions(): Collection
    {
        return Question::query()
            ->whereNull('archived_at')
            ->with('latestVersion')
            ->get()
            ->pluck('latestVersion')
            ->filter()
            ->shuffle()
            ->values();
    }
}
