<?php

namespace App\Support;

use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;

class ApiResponse implements Responsable
{
    const DEFAULT_STATUS = 200;

    protected int $status;

    protected ?string $message;

    protected mixed $data;

    protected mixed $input;

    protected ?MessageBag $errors;

    protected ?string $successfulRoute;

    protected null|Paginator|LengthAwarePaginator $paginator;

    public function __construct()
    {
        $this->reset();
    }

    public function reset(): self
    {
        $this->status = static::DEFAULT_STATUS;
        $this->message = null;
        $this->data = null;
        $this->input = null;
        $this->errors = null;
        $this->successfulRoute = null;
        $this->paginator = null;

        return $this;
    }

    public function status(int $status, bool $setMessage = true): self
    {
        $this->status = $status;

        if ($setMessage) {
            $this->message(__('http-statuses.'.$status));
        }

        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function message(?string $message): self
    {
        $this->message = (($message and Lang::has($message)) ? __($message) : $message);

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function data(mixed $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getData($key = null): mixed
    {
        return Arr::get($this->data, $key);
    }

    public function input(mixed $input): self
    {
        $this->input = $input;

        return $this;
    }

    public function getInput(): mixed
    {
        return $this->input;
    }

    public function errors(?MessageBag $errors): self
    {
        $this->errors = $errors;

        return $this;
    }

    public function getErrors(): ?MessageBag
    {
        return $this->errors;
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

    public function isSuccessful(): bool
    {
        return $this->getStatus() >= 200 and $this->getStatus() < 300;
    }

    public function abortUnSuccessful()
    {
        abort_unless($this->isSuccessful(), $this->getStatus(), $this->getMessage());

        return $this;
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

    public static function new($status = self::DEFAULT_STATUS): static
    {
        return (new static)->status($status);
    }
}
