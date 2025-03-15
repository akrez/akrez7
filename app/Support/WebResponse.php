<?php

namespace App\Support;

class WebResponse extends ApiResponse
{
    private ?string $successfulRoute;

    public function reset(): self
    {
        parent::reset();
        $this->successfulRoute = null;

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

    public function abortUnSuccessful()
    {
        abort_unless($this->isSuccessful(), $this->getStatus(), $this->getMessage());
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
