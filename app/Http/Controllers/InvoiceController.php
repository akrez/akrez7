<?php

namespace App\Http\Controllers;

use App\Data\Invoice\IndexInvoiceData;
use App\Data\Invoice\UpdateInvoiceData;
use App\Enums\PresenterEnum;
use App\Services\ColorService;
use App\Services\DomainService;
use App\Services\InvoiceDeliveryService;
use App\Services\InvoiceItemService;
use App\Services\InvoiceService;
use App\Services\PackageService;
use App\Services\PresentService;
use App\Services\ProductService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct(
        protected ProductService $productService,
        protected ColorService $colorService,
        protected PackageService $packageService,
        protected InvoiceService $invoiceService,
        protected InvoiceDeliveryService $invoiceDeliveryService,
        protected InvoiceItemService $invoiceItemService,
        protected DomainService $domainService,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $indexInvoiceData = new IndexInvoiceData(
            $this->blogId(),
            $request->invoice,
            $request->invoice_delivery,
        );
        $latestInvoicesResponse = $this->invoiceService->getLatestBlogInvoices(
            $indexInvoiceData,
            $request->get('page')
        );
        if (! $latestInvoicesResponse->isSuccessful()) {
            return $latestInvoicesResponse;
        }

        $presentInfos = [
            ['presenter' => PresenterEnum::PREVIEW->value, 'blog_id' => $this->blogId(), 'domain' => null],
            ['presenter' => PresenterEnum::FRONT->value, 'blog_id' => $this->blogId(), 'domain' => null],
        ];
        $domains = $this->domainService->blogIdToDomains($this->blogId())->getData('domains');
        foreach ($domains as $domain) {
            $presentInfos[] = ['presenter' => PresenterEnum::DOMAIN->value, 'blog_id' => $this->blogId(), 'domain' => $domain];
        }

        return view('invoice.index', [
            'invoice' => $request->query('invoice'),
            'invoice_delivery' => $request->query('invoice_delivery'),
            //
            'invoices' => $latestInvoicesResponse->getData('invoices'),
            'paginator' => $latestInvoicesResponse->getPaginator(route('invoices.index')),
            //
            'presentService' => PresentService::new(),
            'presentInfos' => $presentInfos,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $updateInvoiceData = new UpdateInvoiceData(
            $this->blogId(),
            $id,
            $request->invoice,
        );

        return $this->invoiceService->updateBlogInvoice($updateInvoiceData);
    }
}
