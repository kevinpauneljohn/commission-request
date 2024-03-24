<?php

namespace App\Services;

use App\Models\ActionTaken;

class ActionTakenService
{
    public function create($request): bool
    {
        if(ActionTaken::create([
            'action' => $request->description,
            'task_id' => $request->task_id,
            'user_id' => auth()->user()->id
        ]))
        {
            return true;
        }
        return false;
    }
}
