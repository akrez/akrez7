<?php

namespace App\Services;

use App\Http\Resources\Payvoice\PayvoiceCollection;
use App\Models\Payvoice;
use App\Support\WebResponse;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class PayvoiceService
{
    public static function new()
    {
        return app(self::class);
    }

    protected function getPayvoicesQuery(int $blogId)
    {
        return Payvoice::query()->where('blog_id', $blogId);
    }

    public function getLatestPayvoices(int $blogId, ?int $page = null, ?int $perPage = 30)
    {
        $payvoices = $this->getPayvoicesQuery($blogId)->page($page);

        return WebResponse::new()->data([
            'payvoices' => (new PayvoiceCollection($payvoices))->toArray(request()),
        ])->paginator($payvoices);
    }

    public function storePayvoice(int $blogId, Request $request)
    {
        $agent = new Agent([], request()->userAgent());

        Payvoice::create([
            'ip' => $request->ip() ?: null,
            'method' => $request->method() ?: null,
            'controller' => ($request->route() && $request->route()->action['controller'] ? $request->route()->action['controller'] : null),
            'useragent_device' => $agent->deviceType() ?: null,
            'useragent_browser' => $agent->browser() ?: null,
            'useragent_platform' => $agent->platform() ?: null,
            'useragent_robot' => $agent->robot() ?: null,
            'useragent' => $request->userAgent() ?: null,
            'blog_id' => $blogId,
        ]);
    }
}
