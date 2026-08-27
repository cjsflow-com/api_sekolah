<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Fees\StoreFeeTypeRequest;
use App\Http\Requests\Fees\UpdateFeeTypeRequest;
use App\Http\Resources\FeeTypeResource;
use App\Models\FeeType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class FeeTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index(
        Request $request
    ): AnonymousResourceCollection {
        Gate::authorize('viewAny', FeeType::class);

        $allowedSorts = [
            'name',
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

        $feeTypes = FeeType::query()

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

        return FeeTypeResource::collection($feeTypes);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(
        StoreFeeTypeRequest $request
    ): JsonResponse {
        Gate::authorize('create', FeeType::class);

        $feeType = FeeType::create(
            $request->validated()
        );

        return (new FeeTypeResource($feeType))
            ->additional([
                'message' => 'Jenis biaya berhasil dibuat.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(FeeType $feeType): FeeTypeResource
    {
        //
        Gate::authorize('view',$feeType);
        return new FeeTypeResource($feeType);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFeeTypeRequest $request, FeeType $feeType): FeeTypeResource
    {
        //
        Gate::authorize('update',$feeType);
        $feeType->update(
            $request->validated()
        );

        return (new FeeTypeResource($feeType))->additional([
            'message' => 'Jenis biaya berhasil di update'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FeeType $feeType): JsonResponse
    {
        //
        Gate::authorize('delete',$feeType);

        $feeType->delete();

        return response()->json(
            data: null,
            status: 204
        );
    }
}
