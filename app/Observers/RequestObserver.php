<?php

namespace App\Observers;

use App\Models\Request;
use App\Services\RequestService;

class RequestObserver
{
    public RequestService $requestService;
    public function __construct(RequestService $requestService)
    {
        $this->requestService = $requestService;
    }
    /**
     * Handle the Request "created" event.
     */
    public function created(Request $request): void
    {
        $this->requestService->generate_automated_task($request->id);
    }

    /**
     * Handle the Request "updated" event.
     */
    public function updated(Request $request): void
    {
        //
    }

    /**
     * Handle the Request "deleted" event.
     */
    public function deleted(Request $request): void
    {
        //
    }

    /**
     * Handle the Request "restored" event.
     */
    public function restored(Request $request): void
    {
        //
    }

    /**
     * Handle the Request "force deleted" event.
     */
    public function forceDeleted(Request $request): void
    {
        //
    }
}
