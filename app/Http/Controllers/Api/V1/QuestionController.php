<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\Http\Resources\QuestionVersionResource;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class QuestionController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $questions = Question::query()
            ->with(['latestVersion.question'])
            ->when(! $request->boolean('include_archived'), fn ($query) => $query->whereNull('archived_at'))
            ->orderBy('id')
            ->get();

        return QuestionVersionResource::collection(
            $questions->pluck('latestVersion')->filter()
        );
    }

    public function store(StoreQuestionRequest $request): JsonResponse
    {
        $question = Question::create();

        $version = $question->versions()->create([
            'version' => 1,
            'statement' => $request->string('statement')->toString(),
            'language' => $request->input('language') ?? 'id',
        ]);

        return (new QuestionVersionResource($version->load('question')))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Question $question): QuestionVersionResource
    {
        return new QuestionVersionResource(
            $question->latestVersion()->with('question')->firstOrFail()
        );
    }

    /**
     * Editing appends a new version; previous versions are never mutated (D-024).
     */
    public function update(UpdateQuestionRequest $request, Question $question): JsonResponse
    {
        $nextVersion = (int) $question->versions()->max('version') + 1;

        $version = $question->versions()->create([
            'version' => $nextVersion,
            'statement' => $request->string('statement')->toString(),
            'language' => $request->input('language') ?? $question->latestVersion?->language,
        ]);

        return (new QuestionVersionResource($version->load('question')))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    public function archive(Question $question): Response
    {
        if (! $question->isArchived()) {
            $question->archived_at = now();
            $question->save();
        }

        return response()->noContent();
    }
}
