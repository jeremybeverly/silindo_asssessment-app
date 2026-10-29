<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvitationRequest;
use App\Http\Resources\InvitationResource;
use App\Models\Invitation;
use App\Support\PhoneNumber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class InvitationController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $invitations = Invitation::query()
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->when($request->filled('search'), function ($query) use ($request): void {
                $term = '%'.$request->string('search')->toString().'%';

                $query->where(function ($query) use ($term): void {
                    $query->where('candidate_name', 'like', $term)
                        ->orWhere('candidate_email', 'like', $term)
                        ->orWhere('candidate_phone_normalized', 'like', $term);
                });
            })
            ->latest('id')
            ->get();

        return InvitationResource::collection($invitations);
    }

    public function store(StoreInvitationRequest $request): JsonResponse
    {
        $normalized = PhoneNumber::normalize($request->string('candidate_phone')->toString());

        if ($normalized === '') {
            throw ValidationException::withMessages([
                'candidate_phone' => 'Nomor telepon tidak valid.',
            ]);
        }

        $token = Str::random(64);

        $invitation = Invitation::create([
            'token_hash' => hash('sha256', $token),
            'candidate_name' => $request->string('candidate_name')->toString(),
            'candidate_email' => $request->string('candidate_email')->toString(),
            'candidate_phone' => $request->string('candidate_phone')->toString(),
            'candidate_phone_normalized' => $normalized,
            'position_name' => $request->string('candidate_position')->toString(),
            'position_external_id' => $request->input('position_external_id'),
            'access_starts_at' => $request->date('access_starts_at'),
            'access_ends_at' => $request->date('access_ends_at'),
            'status' => Invitation::STATUS_ISSUED,
        ]);

        return response()->json([
            'invitation' => (new InvitationResource($invitation))->resolve(),
            // Raw token is returned once; only its hash is stored.
            'token' => $token,
        ], Response::HTTP_CREATED);
    }

    public function show(Invitation $invitation): InvitationResource
    {
        return new InvitationResource($invitation);
    }

    public function revoke(Invitation $invitation): InvitationResource
    {
        if (! $invitation->isRevoked()) {
            $invitation->status = Invitation::STATUS_REVOKED;
            $invitation->revoked_at = now();
            $invitation->save();
        }

        return new InvitationResource($invitation);
    }
}
