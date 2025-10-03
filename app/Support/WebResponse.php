<?php

namespace App\Support;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class WebResponse extends ApiResponse
{
    protected ?string $successfulRoute;

    protected ?array $paginator;

    public function reset(): self
    {
        parent::reset();

        $this->successfulRoute = null;
        $this->paginator = null;

        return $this;
    }

    public function successfulRoute(?string $successfulRoute): self
    {
        $this->successfulRoute = $successfulRoute;

        return $this;
    }

    public function getSuccessfulRoute(): ?string
    {
        return $this->successfulRoute;
    }

    public function paginator(null|Paginator|LengthAwarePaginator $paginator, $itemsDataKey = null): self
    {
        $this->paginator = ($paginator ? [
            'class' => $paginator::class,
            'parameters' => [
                'items' => [],
                'perPage' => $paginator->perPage(),
                'currentPage' => $paginator->currentPage(),
                'options' => $paginator->getOptions(),
                'total' => ($paginator instanceof LengthAwarePaginator ? $paginator->total() : null),
            ],
        ] : null);

        return $this;
    }

    public function getPaginator($path, $itemsDataKey = null, $pageName = 'page'): null|Paginator|LengthAwarePaginator
    {
        if (! $this->paginator) {
            return null;
        }

        if ($itemsDataKey) {
            $this->paginator['parameters']['items'] = (Arr::get($this->data, $itemsDataKey) ?: []);
        }

        return app($this->paginator['class'], $this->paginator['parameters'])
            ->setPath($path)
            ->setPageName($pageName);
    }

    public function toResponse($request)
    {
        if (! $this->isSuccessful()) {
            return back()
                ->with('swal-error', $this->getMessage())
                ->withInput($request->input())
                ->withErrors($this->getErrors());
        }

        if ($this->getSuccessfulRoute() === null) {
            return back()
                ->with('swal-success', $this->getMessage());
        }

        return redirect()
            ->to($this->getSuccessfulRoute())
            ->with('swal-success', $this->getMessage());
    }
}
