<?php

namespace App\Services;

use App\Data\Invoice\IndexInvoiceData;
use App\Data\Invoice\StoreInvoiceData;
use App\Data\Invoice\UpdateInvoiceData;
use App\Enums\InvoiceStatusEnum;
use App\Http\Resources\Invoice\InvoiceCollection;
use App\Http\Resources\Invoice\InvoiceResource;
use App\Models\Invoice;
use App\Models\InvoiceDelivery;
use App\Models\InvoiceItem;
use App\Support\ApiResponse;
use App\Support\Arr;
use App\Support\WebResponse;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceService extends Service
{
    public static function new()
    {
        return app(self::class);
    }

    public function __construct(
        protected ProductService $productService,
        protected ColorService $colorService,
        protected PackageService $packageService,
    ) {}

    public function getApiResource(int $blogId, int $id): ApiResponse
    {
        $model = $this->getLatestApiQuery($blogId)
            ->where('id', $id)
            ->first();

        return ApiResponse::new(200)->data([
            'invoice' => (new InvoiceResource($model))->toArr(),
        ]);
    }

    public function getApiCollection(int $blogId): ApiResponse
    {
        $models = $this->getLatestApiQuery($blogId)
            ->get();

        return ApiResponse::new(200)->data([
            'invoices' => (new InvoiceCollection($models))->toArr(),
        ]);
    }

    protected function getLatestBaseQuery($blogId): \Illuminate\Database\Eloquent\Builder
    {
        return Invoice::query()
            ->where('blog_id', $blogId)
            ->with(['invoiceDelivery', 'invoiceItems'])
            ->defaultOrder();
    }

    public function getApiResourceByUuid(int $blogId, string $uuid): ApiResponse
    {
        $invoice = $this->getLatestApiQuery($blogId)
            ->where('invoice_uuid', $uuid)
            ->first();

        $invoiceApiResource = $this->attachColorAndProduct(
            $blogId,
            (new InvoiceCollection([$invoice]))->toArray(request())
        );

        return WebResponse::new()->data([
            'invoice' => reset($invoiceApiResource),
        ]);
    }

    public function updateBlogInvoice(UpdateInvoiceData $updateInvoiceData)
    {
        $webResponse = WebResponse::new()->input($updateInvoiceData);

        $validation = $updateInvoiceData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $webResponse->status(422)->errors($validation->errors());
        }

        $invoice = $this->getLatestBlogQuery($updateInvoiceData->blog_id)->where('id', $updateInvoiceData->invoice_id)->first();
        if (! $invoice) {
            return $webResponse->status(404);
        }

        $invoice->update([
            'invoice_status' => $updateInvoiceData->invoice['invoice_status'],
        ]);
        if (! $invoice->save()) {
            return $webResponse->status(500);
        }

        return $webResponse
            ->status(200)
            ->data(['invoice' => (new InvoiceResource($invoice))->toArr()])
            ->message(__(':name is updated successfully', [
                'name' => __('Invoice'),
            ]));
    }

    public function getLatestBlogInvoices(
        IndexInvoiceData $indexInvoiceData,
        ?int $page = null,
        ?int $perPage = 2
    ) {
        $webResponse = WebResponse::new()->input($indexInvoiceData);

        $validation = $indexInvoiceData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $webResponse->status(422)->errors($validation->errors());
        }

        $invoicesQuery = $this->getLatestBlogQuery($indexInvoiceData->blog_id)
            ->latest();

        $filterInvoiceStatus = Arr::get($indexInvoiceData->invoice, 'invoice_status');
        if ($filterInvoiceStatus) {
            $invoicesQuery->where('invoice_status', $filterInvoiceStatus);
        }

        $filterInvoiceDeliveryName = Arr::get($indexInvoiceData->invoice_delivery, 'name');
        if ($filterInvoiceDeliveryName) {
            $invoicesQuery->whereHas('invoiceDelivery', function ($invoiceDeliveryQuery) use ($filterInvoiceDeliveryName) {
                $invoiceDeliveryQuery->where('name', 'LIKE', "%$filterInvoiceDeliveryName%");
            });
        }

        $filterInvoiceDeliveryMobile = Arr::get($indexInvoiceData->invoice_delivery, 'mobile');
        if ($filterInvoiceDeliveryMobile) {
            $invoicesQuery->whereHas('invoiceDelivery', function ($invoiceDeliveryQuery) use ($filterInvoiceDeliveryMobile) {
                $invoiceDeliveryQuery->where('mobile', 'LIKE', "%$filterInvoiceDeliveryMobile%");
            });
        }

        $invoices = $invoicesQuery->page($page, $perPage);

        return WebResponse::new()->data([
            'invoices' => $this->attachColorAndProduct($indexInvoiceData->blog_id, (new InvoiceCollection($invoices))->toArray(request())),
        ])->paginator($invoices);
    }

    public function attachColorAndProduct(int $blogId, array $invoices)
    {
        $invoices = collect($invoices)->keyBy('id')->toArray();

        $packageIds = collect($invoices)
            ->pluck('invoiceItems')->flatten(1)->pluck('package_id')->unique()->values()->toArray();
        $packages = $this->packageService->getLatestPackagesWithTrashedByIds($blogId, $packageIds)->getData('packages');
        $packages = collect($packages)->keyBy('id')->toArray();

        $productIds = collect($packages)
            ->pluck('product_id')->unique()->values()->toArray();
        $products = $this->productService->getLatestProductsByIds($blogId, $productIds)->getData('products');
        $products = collect($products)->keyBy('id')->toArray();

        $colorIds = collect($packages)
            ->pluck('color_id')->unique()->values()->toArray();
        $colors = $this->colorService->getLatestColorsByIds($blogId, $colorIds)->getData('colors');
        $colors = collect($colors)->keyBy('id');

        foreach ($invoices as $invoiceId => $invoice) {
            foreach ($invoice['invoiceItems'] as $invoiceItemId => $invoiceItem) {
                $package = Arr::get($packages, $invoiceItem['package_id']);
                $package['product'] = Arr::get($products, $package['product_id']);
                $package['color'] = Arr::get($colors, $package['color_id']);
                $invoices[$invoiceId]['invoiceItems'][$invoiceItemId]['package'] = $package;
            }
        }

        return $invoices;
    }

    public function storeInvoice(StoreInvoiceData $storeInvoiceData, array $presentInfo)
    {
        $storeInvoiceData->invoice['invoice_status'] = InvoiceStatusEnum::PENDING->value;

        $apiResponse = ApiResponse::new()->input($storeInvoiceData);

        try {

            DB::beginTransaction();

            $validation = $storeInvoiceData->validate();
            if ($validation->errors()->isNotEmpty()) {
                return $apiResponse->status(422)->errors($validation->errors());
            }

            $invoice = Invoice::create([
                'invoice_uuid' => Str::uuid7(),
                'blog_id' => $storeInvoiceData->blog_id,
                'invoice_status' => $storeInvoiceData->invoice['invoice_status'],
                'invoice_params' => [
                    'invoice_description' => $storeInvoiceData->invoice['invoice_description'] ?? null,
                    'present_info' => $presentInfo,

                ],
            ]);
            if (! $invoice) {
                throw new Exception;
            }

            $invoiceDelivery = InvoiceDelivery::create([
                'blog_id' => $storeInvoiceData->blog_id,
                'invoice_id' => $invoice->id,
                'name' => $storeInvoiceData->invoice_delivery['name'],
                'mobile' => $storeInvoiceData->invoice_delivery['mobile'],
                'phone' => $storeInvoiceData->invoice_delivery['phone'] ?? null,
                'invoice_delivery_params' => [
                    'city' => $storeInvoiceData->invoice_delivery['city'],
                    'address' => $storeInvoiceData->invoice_delivery['address'],
                    'postal_code' => $storeInvoiceData->invoice_delivery['postal_code'] ?? null,
                    'lat' => $storeInvoiceData->invoice_delivery['lat'] ?? null,
                    'lng' => $storeInvoiceData->invoice_delivery['lng'] ?? null,
                    'invoice_delivery_description' => $storeInvoiceData->invoice_delivery['invoice_delivery_description'] ?? null,
                ],
            ]);
            if (! $invoiceDelivery) {
                throw new Exception;
            }

            $invoiceItems = [];
            foreach ($storeInvoiceData->invoice_items as $storeInvoiceDataInvoiceItemKey => $storeInvoiceDataInvoiceItem) {
                $invoiceItems[$storeInvoiceDataInvoiceItemKey] = InvoiceItem::create([
                    'blog_id' => $storeInvoiceData->blog_id,
                    'invoice_id' => $invoice->id,
                    'cnt' => $storeInvoiceDataInvoiceItem['cnt'],
                    'package_id' => $storeInvoiceDataInvoiceItem['package_id'],
                    'price' => $storeInvoiceData->packageApiResources[$storeInvoiceDataInvoiceItem['package_id']]['price'],
                ]);

                if (! $invoiceItems[$storeInvoiceDataInvoiceItemKey]) {
                    throw new Exception;
                }
            }

            DB::commit();

            return $apiResponse->status(201)->data([
                'invoice' => $invoice,
                'invoice_delivery' => $invoiceDelivery,
                'invoice_items' => $invoiceItems,
            ])->message(__(':name is created successfully', [
                'name' => __('Invoice'),
            ]));

        } catch (Exception $e) {
            DB::rollBack();

            return $apiResponse->status(501);
        }
    }
}
