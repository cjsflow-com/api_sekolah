<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Invoices\StoreInvoiceRequest;
use App\Http\Requests\Invoices\UpdateInvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class InvoiceController extends Controller
{
    /**
     * Daftar invoice.
     */
    public function index(
        Request $request
    ): AnonymousResourceCollection {
        Gate::authorize('viewAny', Invoice::class);

        $allowedSorts = [
            'invoice_number',
            'month',
            'year',
            'total_amount',
            'amount',
            'status',
            'due_date',
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

        $invoices = Invoice::query()
            ->with([
                'student',
                'feeType',
                'semester',
            ])

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
                                    'invoice_number',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'description',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhereHas(
                                    'student',
                                    function (Builder $query) use ($search): void {
                                        $query->where(
                                            'name',
                                            'like',
                                            "%{$search}%"
                                        );
                                    }
                                );
                        }
                    );
                }
            )

            ->when(
                $request->filled('student_id'),
                function (Builder $query) use ($request): void {
                    $query->where(
                        'student_id',
                        $request->integer('student_id')
                    );
                }
            )

            ->when(
                $request->filled('fee_type_id'),
                function (Builder $query) use ($request): void {
                    $query->where(
                        'fee_type_id',
                        $request->integer('fee_type_id')
                    );
                }
            )

            ->when(
                $request->filled('semester_id'),
                function (Builder $query) use ($request): void {
                    $query->where(
                        'semester_id',
                        $request->integer('semester_id')
                    );
                }
            )

            ->when(
                $request->filled('status'),
                function (Builder $query) use ($request): void {
                    $query->where(
                        'status',
                        (string) $request->query('status')
                    );
                }
            )

            ->when(
                $request->filled('month'),
                function (Builder $query) use ($request): void {
                    $query->where(
                        'month',
                        $request->integer('month')
                    );
                }
            )

            ->when(
                $request->filled('year'),
                function (Builder $query) use ($request): void {
                    $query->where(
                        'year',
                        $request->integer('year')
                    );
                }
            )

            ->orderBy($sortBy, $sortDirection)
            ->paginate($perPage)
            ->withQueryString();

        return InvoiceResource::collection($invoices);
    }

    /**
     * Membuat invoice baru.
     */
    public function store(
        StoreInvoiceRequest $request
    ): JsonResponse {
        Gate::authorize('create', Invoice::class);

        $invoice = Invoice::create(
            $request->validated()
        );

        $invoice->load([
            'student',
            'feeType',
            'semester',
        ]);

        return (new InvoiceResource($invoice))
            ->additional([
                'message' => 'Invoice berhasil dibuat.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Detail invoice.
     */
    public function show(
        Invoice $invoice
    ): InvoiceResource {
        Gate::authorize('view', $invoice);

        $invoice->load([
            'student',
            'feeType',
            'semester',
        ]);

        return new InvoiceResource($invoice);
    }

    /**
     * Memperbarui invoice.
     */
    public function update(
        UpdateInvoiceRequest $request,
        Invoice $invoice
    ): InvoiceResource {
        Gate::authorize('update', $invoice);

        $invoice->update(
            $request->validated()
        );

        $invoice->load([
            'student',
            'feeType',
            'semester',
        ]);

        return (new InvoiceResource($invoice))
            ->additional([
                'message' => 'Invoice berhasil diperbarui.',
            ]);
    }

    /**
     * Menghapus invoice.
     */
    public function destroy(
        Invoice $invoice
    ): JsonResponse {
        Gate::authorize('delete', $invoice);

        $invoice->delete();

        return response()->json(
            data: null,
            status: 204
        );
    }
}