<?php

namespace App\Observers;

use App\Mail\RequestCreated;
use App\Models\Request;
use App\Models\User;
use App\Services\RequestService;
use Illuminate\Support\Facades\Mail;

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

        $cc_users = collect(User::whereHas("roles", function($q){
            $q->where("name","=","sales administrator")
                ->orWhere("name","=","business_admin_01")
                ->orWhere("name","=","super admin");
        })->get())->pluck('email');

        Mail::to($request->user->email)
            ->cc($cc_users)->send(new RequestCreated($request));

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
