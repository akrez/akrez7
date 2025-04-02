<?php

namespace App\Support;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class WebResponse extends ApiResponse
{
    protected ?string $successfulRoute;

    protected null|Paginator|LengthAwarePaginator $paginator;

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

    public function paginator(null|Paginator|LengthAwarePaginator $paginator): self
    {
        $this->paginator = ($paginator ? app($paginator::class, [
            'items' => [],
            'perPage' => $paginator->perPage(),
            'currentPage' => $paginator->currentPage(),
            'options' => [],
            'total' => ($paginator instanceof LengthAwarePaginator ? $paginator->total() : null),
        ]) : null);

        return $this;
    }

    public function getPaginator($path, $pageName = 'page'): null|Paginator|LengthAwarePaginator
    {
        return $this->paginator ? $this->paginator->setPath($path)->setPageName($pageName) : null;
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
