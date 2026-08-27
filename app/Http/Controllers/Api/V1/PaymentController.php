<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payments\StorePaymentRequest;
use App\Http\Requests\Payments\UpdatePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class PaymentController extends Controller
{
    /**
     * Daftar pembayaran.
     */
    public function index(
        Request $request
    ): AnonymousResourceCollection {
        Gate::authorize('viewAny', Payment::class);

        $allowedSorts = [
            'reference_number',
            'payment_date',
            'amount',
            'payment_method',
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

        $payments = Payment::query()
            ->with([
                'invoice.student',
                'invoice.feeType',
                'receivedBy',
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
                                    'reference_number',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'reference_no',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'description',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhereHas(
                                    'invoice',
                                    function (Builder $query) use ($search): void {
                                        $query->where(
                                            'invoice_number',
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
                $request->filled('invoice_id'),
                function (Builder $query) use ($request): void {
                    $query->where(
                        'invoice_id',
                        $request->integer('invoice_id')
                    );
                }
            )

            ->when(
                $request->filled('payment_method'),
                function (Builder $query) use ($request): void {
                    $query->where(
                        'payment_method',
                        (string) $request->query('payment_method')
                    );
                }
            )

            ->orderBy($sortBy, $sortDirection)
            ->paginate($perPage)
            ->withQueryString();

        return PaymentResource::collection($payments);
    }

    /**
     * Membuat pembayaran.
     */
    public function store(
        StorePaymentRequest $request
    ): JsonResponse {
        Gate::authorize('create', Payment::class);

        $payment = new Payment(
            $request->validated()
        );

        $payment->receivedBy()->associate(
            $request->user()
        );

        $payment->save();

        $payment->load([
            'invoice.student',
            'invoice.feeType',
            'receivedBy',
        ]);

        return (new PaymentResource($payment))
            ->additional([
                'message' => 'Pembayaran berhasil dibuat.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Detail pembayaran.
     */
    public function show(
        Payment $payment
    ): PaymentResource {
        Gate::authorize('view', $payment);

        $payment->load([
            'invoice.student',
            'invoice.feeType',
            'receivedBy',
        ]);

        return new PaymentResource($payment);
    }

    /**
     * Memperbarui pembayaran.
     */
    public function update(
        UpdatePaymentRequest $request,
        Payment $payment
    ): PaymentResource {
        Gate::authorize('update', $payment);

        $payment->update(
            $request->validated()
        );

        $payment->load([
            'invoice.student',
            'invoice.feeType',
            'receivedBy',
        ]);

        return (new PaymentResource($payment))
            ->additional([
                'message' => 'Pembayaran berhasil diperbarui.',
            ]);
    }

    /**
     * Menghapus pembayaran.
     */
    public function destroy(
        Payment $payment
    ): JsonResponse {
        Gate::authorize('delete', $payment);

        $payment->delete();

        return response()->json(
            data: null,
            status: 204
        );
    }
}