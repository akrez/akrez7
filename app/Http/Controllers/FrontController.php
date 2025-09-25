<?php

namespace App\Http\Controllers;

use App\Data\Invoice\StoreInvoiceData;
use App\Services\BlogService;
use App\Services\InvoiceService;
use App\Services\SummaryService;
use App\Support\WebResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class FrontController extends Controller
{
    public function __construct(
        protected BlogService $blogService,
        protected InvoiceService $invoiceService,
    ) {}

    public function show(Request $request)
    {
        $blogId = $request->blog_id;

        $blog = $this->blogService->getApiResource($blogId)->abortUnSuccessful();

        return view('front.show', [
            'data' => SummaryService::new()->getCachedApiResponse($blogId, request(), false)->getData(),
            'storeInvoiceAction' => $this->getRoute($request, 'invoices.store'),
        ]);
    }

    public function sitemap(Request $request)
    {
        $blogId = $request->blog_id;

        $blog = $this->blogService->getApiResource($blogId)->abortUnSuccessful();

        $response = SummaryService::new()->getSitemapResponse($blogId, request())->abortUnSuccessful();

        return Response::make($response->getData('sitemap'), $response->getStatus())
            ->header('Content-Type', 'application/xml');
    }

    public function storeInvoice(Request $request, int $blog_id)
    {
        $blogResponse = $this->blogService->getApiResource($blog_id);
        if (! $blogResponse->isSuccessful()) {
            return WebResponse::new($blogResponse->getStatus());
        }

        $storeInvoiceData = new StoreInvoiceData(
            $blog_id,
            $request->invoice,
            $request->invoice_delivery,
            $request->invoice_items,
        );

        $r = $this->invoiceService->storeApiInvoice($storeInvoiceData);

        return $r;
    }

    private function getRoute(Request $request, $route, $parameters = [])
    {
        $domain = $request->route()->parameter('domain');
        if ($domain) {
            return route('domain.'.$route, $parameters);
        } else {
            $parameters['blog_id'] = $request->blog_id;

            return route('front.'.$route, $parameters);
        }
    }
}
