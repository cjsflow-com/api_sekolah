<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Semesters\StoreSemesterRequest;
use App\Http\Requests\Semesters\UpdateSemesterRequest;
use App\Http\Resources\SemesterResource;
use App\Models\Semester;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class SemesterController extends Controller
{
    public function index(
        Request $request
    ): AnonymousResourceCollection {
        Gate::authorize('viewAny', Semester::class);

        $allowedSorts = [
            'name',
            'start_date',
            'end_date',
            'is_active',
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

        $semesters = Semester::query()
            ->with('academicYear')

            ->when(
                $request->filled('search'),
                function (Builder $query) use ($request): void {
                    $search = trim(
                        (string) $request->query('search')
                    );

                    $query->where(
                        'name',
                        'like',
                        "%{$search}%"
                    );
                }
            )

            ->when(
                $request->filled('academic_year_id'),
                function (Builder $query) use ($request): void {
                    $query->where(
                        'academic_year_id',
                        $request->integer('academic_year_id')
                    );
                }
            )

            ->when(
                $request->has('is_active'),
                function (Builder $query) use ($request): void {
                    $query->where(
                        'is_active',
                        $request->boolean('is_active')
                    );
                }
            )

            ->orderBy($sortBy, $sortDirection)
            ->paginate($perPage)
            ->withQueryString();

        return SemesterResource::collection($semesters);
    }

    public function store(
        StoreSemesterRequest $request
    ): JsonResponse {
        Gate::authorize('create', Semester::class);

        $semester = DB::transaction(
            function () use ($request): Semester {
                $data = $request->validated();

                if ($data['is_active'] ?? false) {
                    Semester::query()
                        ->where('is_active', true)
                        ->update([
                            'is_active' => false,
                        ]);
                }

                return Semester::create($data);
            }
        );

        $semester->load('academicYear');

        return (new SemesterResource($semester))
            ->additional([
                'message' => 'Semester berhasil dibuat.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    public function show(
        Semester $semester
    ): SemesterResource {
        Gate::authorize('view', $semester);

        $semester->load('academicYear');

        return new SemesterResource($semester);
    }

    public function update(
        UpdateSemesterRequest $request,
        Semester $semester
    ): SemesterResource {
        Gate::authorize('update', $semester);

        DB::transaction(
            function () use ($request, $semester): void {
                $data = $request->validated();

                if ($data['is_active'] ?? false) {
                    Semester::query()
                        ->whereKeyNot($semester->id)
                        ->where('is_active', true)
                        ->update([
                            'is_active' => false,
                        ]);
                }

                $semester->update($data);
            }
        );

        $semester->load('academicYear');

        return (new SemesterResource($semester))
            ->additional([
                'message' => 'Semester berhasil diperbarui.',
            ]);
    }

    public function destroy(
        Semester $semester
    ): JsonResponse {
        Gate::authorize('delete', $semester);

        $semester->delete();

        return response()->json(
            data: null,
            status: 204
        );
    }
}
