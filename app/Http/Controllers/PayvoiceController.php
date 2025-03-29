<?php

namespace App\Http\Controllers;

use App\Services\PayvoiceService;
use Illuminate\Http\Request;

class PayvoiceController extends Controller
{
    public function __construct(
        protected PayvoiceService $payvoiceService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $response = $this->payvoiceService->getLatestPayvoices(
            $this->blogId(),
            $request->get('page')
        );

        return view('payvoice.index', [
            'payvoices' => $response->getData('payvoices'),
            'paginator' => $response->getPaginator(route('payvoices.index')),
        ]);
    }
}
