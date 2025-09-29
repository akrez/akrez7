<?php

namespace App\Services;

use App\Data\Invoice\StoreInvoiceData;
use App\Data\InvoiceDelivery\StoreInvoiceDeliveryData;
use App\Http\Resources\InvoiceDelivery\InvoiceDeliveryCollection;
use App\Http\Resources\InvoiceDelivery\InvoiceDeliveryResource;
use App\Models\Invoice;
use App\Models\InvoiceDelivery;
use App\Support\ApiResponse;
use App\Support\WebResponse;

class InvoiceDeliveryService extends Service
{
    public static function new()
    {
        return app(self::class);
    }

    public function getApiResource(int $blogId, int $id): ApiResponse
    {
        $model = $this->getLatestApiQuery($blogId)
            ->where('id', $id)
            ->first();

        return ApiResponse::new(200)->data([
            'invoice_delivery' => (new InvoiceDeliveryResource($model))->toArr(),
        ]);
    }

    public function getApiCollection(int $blogId): ApiResponse
    {
        $models = $this->getLatestApiQuery($blogId)
            ->get();

        return ApiResponse::new(200)->data([
            'invoice_deliveries' => (new InvoiceDeliveryCollection($models))->toArr(),
        ]);
    }

    protected function getLatestBaseQuery($blogId): \Illuminate\Database\Eloquent\Builder
    {
        return InvoiceDelivery::query()
            ->where('blog_id', $blogId)
            ->defaultOrder();
    }

    public function getLatestInvoiceDeliveries(int $blogId, ?int $page = null, ?int $perPage = 30)
    {
        $invoiceDeliveries = $this->getLatestBlogQuery($blogId)->latest()->page($page);

        return WebResponse::new()->data([
            'invoice_deliveries' => (new InvoiceDeliveryCollection($invoiceDeliveries))->toArray(request()),
        ])->paginator($invoiceDeliveries);
    }

    public function storeApiInvoiceDelivery(int $blogId, StoreInvoiceData $storeInvoiceData)
    {
        $apiResponse = ApiResponse::new()->input($storeInvoiceData);

        $validation = $storeInvoiceData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $apiResponse->status(422)->errors($validation->errors());
        }

        $invoice = Invoice::create([
            'blog_id' => $blogId,
            'invoice_status' => $storeInvoiceData->invoice['invoice_status'],
            'invoice_params' => [
                'invoice_description' => $storeInvoiceData->invoice['invoice_description'] ?? null,
            ],
        ]);
        if (! $invoice) {
            return $apiResponse->status(500);
        }

        return $apiResponse->status(201)->data($invoice)->message(__(':name is created successfully', [
            'name' => __('Invoice'),
        ]));
    }

    public function storeInvoiceDelivery(int $blogId, int $invoiceId, StoreInvoiceDeliveryData $storeInvoiceDeliveryData)
    {
        $webResponse = WebResponse::new()->input($storeInvoiceDeliveryData);

        $validation = $storeInvoiceDeliveryData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $webResponse->status(422)->errors($validation->errors());
        }

        $invoiceDelivery = InvoiceDelivery::create([
            'blog_id' => $storeInvoiceDeliveryData->blog_id,
            'invoice_id' => $invoiceId,
            'name' => $storeInvoiceDeliveryData->name,
            'mobile' => $storeInvoiceDeliveryData->mobile,
            'phone' => $storeInvoiceDeliveryData->phone,
            'invoice_delivery_params' => [
                'city' => $storeInvoiceDeliveryData->city,
                'address' => $storeInvoiceDeliveryData->address,
                'postal_code' => $storeInvoiceDeliveryData->postal_code,
                'lat' => $storeInvoiceDeliveryData->lat,
                'lng' => $storeInvoiceDeliveryData->lng,
                'invoice_delivery_description' => $storeInvoiceDeliveryData->invoice_delivery_description,
            ],
        ]);
        if (! $invoiceDelivery) {
            return $webResponse->status(500);
        }

        return $webResponse->status(201)->data($invoiceDelivery)->message(__(':name is created successfully', [
            'name' => __('InvoiceDelivery'),
        ]));
    }
}
