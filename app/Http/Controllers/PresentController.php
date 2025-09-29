<?php

namespace App\Http\Controllers;

use App\Data\Invoice\StoreInvoiceData;
use App\Services\BlogService;
use App\Services\InvoiceService;
use App\Services\PresentService;
use App\Support\WebResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class PresentController extends Controller
{
    public function __construct(
        protected BlogService $blogService,
        protected InvoiceService $invoiceService,
        protected PresentService $presentService,
    ) {}

    public function show(Request $request)
    {
        $presentInfo = $this->getPresentInfo($request);
        $blogId = $presentInfo['blog_id'];

        return view('present.show', [
            'data' => $this->presentService->getCachedApiResponse($blogId, request(), false)->getData(),
            'storeInvoiceAction' => $this->presentService->routeByPresentInfo($presentInfo, 'invoices.store'),
        ]);
    }

    public function sitemap(Request $request)
    {
        $presentInfo = $this->getPresentInfo($request);
        $blogId = $presentInfo['blog_id'];

        $response = $this->presentService->getSitemapResponse($blogId, request())->abortUnSuccessful();

        return Response::make($response->getData('sitemap'), $response->getStatus())
            ->header('Content-Type', 'application/xml');
    }

    public function storeInvoice(Request $request)
    {
        $presentInfo = $this->getPresentInfo($request);
        $blogId = $presentInfo['blog_id'];

        $blogResponse = $this->blogService->getApiResource($blogId);
        if (! $blogResponse->isSuccessful()) {
            return WebResponse::new($blogResponse->getStatus());
        }

        $storeInvoiceData = new StoreInvoiceData(
            $blogId,
            $request->invoice,
            $request->invoice_delivery,
            $request->invoice_items,
        );

        return $this->invoiceService->storeInvoice($storeInvoiceData, $presentInfo);
    }

    public function showInvoice(Request $request)
    {
        $presentInfo = $this->getPresentInfo($request);
        $blogId = $presentInfo['blog_id'];

        $invoiceUuid = $request->route()->parameter('invoice_uuid');
        $latestInvoiceResponse = $this->invoiceService->getApiResourceByUuid($blogId, $invoiceUuid);

        return view('present.invoice', [
            'data' => $this->presentService->getCachedApiResponse($blogId, request(), false)->getData(),
            'invoice' => $latestInvoiceResponse->getData('invoice'),
        ]);
    }

    private function getPresentInfo(Request $request)
    {
        return $this->presentService->getRequestPresentInfo($request);
    }
}
