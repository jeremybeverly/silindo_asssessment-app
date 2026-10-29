<?php

namespace App\Http\Resources;

use App\Models\QuestionVersion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin QuestionVersion
 */
class QuestionVersionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'question_id' => $this->question_id,
            'version' => $this->version,
            'statement' => $this->statement,
            'language' => $this->language,
            'archived' => $this->whenLoaded('question', fn (): bool => $this->question->isArchived()),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
