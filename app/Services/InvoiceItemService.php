<?php

namespace App\Services;

use App\Http\Resources\InvoiceItem\InvoiceItemCollection;
use App\Http\Resources\InvoiceItem\InvoiceItemResource;
use App\Models\InvoiceItem;
use App\Support\ApiResponse;
use App\Support\WebResponse;

class InvoiceItemService extends Service
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
            'invoice_item' => (new InvoiceItemResource($model))->toArr(),
        ]);
    }

    public function getApiCollection(int $blogId): ApiResponse
    {
        $models = $this->getLatestApiQuery($blogId)
            ->get();

        return ApiResponse::new(200)->data([
            'invoice_items' => (new InvoiceItemCollection($models))->toArr(),
        ]);
    }

    protected function getLatestBaseQuery($blogId): \Illuminate\Database\Eloquent\Builder
    {
        return InvoiceItem::query()
            ->where('blog_id', $blogId)
            ->defaultOrder();
    }

    public function getLatestInvoiceItems(int $blogId, ?int $page = null, ?int $perPage = 30)
    {
        $invoiceItems = $this->getLatestBlogQuery($blogId)->latest()->page($page);

        return WebResponse::new()->data([
            'invoice_items' => (new InvoiceItemCollection($invoiceItems))->toArray(request()),
        ])->paginator($invoiceItems);
    }
}
