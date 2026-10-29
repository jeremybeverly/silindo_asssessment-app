<?php

namespace App\Http\Resources;

use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Invitation
 */
class InvitationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'candidate_name' => $this->candidate_name,
            'candidate_email' => $this->candidate_email,
            'candidate_phone' => $this->candidate_phone,
            'candidate_phone_normalized' => $this->candidate_phone_normalized,
            'position_name' => $this->position_name,
            'position_external_id' => $this->position_external_id,
            'access_starts_at' => $this->access_starts_at->toIso8601String(),
            'access_ends_at' => $this->access_ends_at->toIso8601String(),
            'status' => $this->status,
            'revoked_at' => $this->revoked_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
