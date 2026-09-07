<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subjects\StoreSubjectRequest;
use App\Http\Requests\Subjects\UpdateSubjectRequest;
use App\Http\Resources\SubjectResource;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class SubjectController extends Controller
{
    public function index(
        Request $request
    ): AnonymousResourceCollection {
        Gate::authorize('viewAny', Subject::class);

        $allowedSorts = [
            'code',
            'name',
            'created_at',
            'updated_at',
        ];

        $requestedSort = (string) $request->query(
            'sort_by',
            'created_at'
        );

        $sortBy = in_array(
            $requestedSort,
            $allowedSorts,
            true
        )
            ? $requestedSort
            : 'created_at';

        $sortDirection = $request->query(
            'sort_direction'
        ) === 'asc'
            ? 'asc'
            : 'desc';

        $perPage = min(
            max($request->integer('per_page', 15), 1),
            100
        );

        $subjects = Subject::query()
            ->when(
                $request->filled('search'),
                function (Builder $query) use ($request): void {
                    $search = trim(
                        (string) $request->query('search')
                    );

                    $query->where(
                        function (Builder $query) use ($search): void {
                            $query
                                ->where(
                                    'code',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'description',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );
                }
            )
            ->orderBy($sortBy, $sortDirection)
            ->paginate($perPage)
            ->withQueryString();

        return SubjectResource::collection($subjects);
    }

    public function store(
        StoreSubjectRequest $request
    ): JsonResponse {
        Gate::authorize('create', Subject::class);

        $subject = Subject::create(
            $request->validated()
        );

        return (new SubjectResource($subject))
            ->additional([
                'message' => 'Mata pelajaran berhasil dibuat.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    public function show(
        Subject $subject
    ): SubjectResource {
        Gate::authorize('view', $subject);

        return new SubjectResource($subject);
    }

    public function update(
        UpdateSubjectRequest $request,
        Subject $subject
    ): SubjectResource {
        Gate::authorize('update', $subject);

        $subject->update(
            $request->validated()
        );

        return (new SubjectResource($subject))
            ->additional([
                'message' => 'Mata pelajaran berhasil diperbarui.',
            ]);
    }

    public function destroy(
        Subject $subject
    ): JsonResponse {
        Gate::authorize('delete', $subject);

        $subject->delete();

        return response()->json(
            data: null,
            status: 204
        );
    }
}