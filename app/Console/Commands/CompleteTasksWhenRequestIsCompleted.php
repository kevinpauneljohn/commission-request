<?php

namespace App\Console\Commands;

use App\Models\Request;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompleteTasksWhenRequestIsCompleted extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:tasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all tasks when request is completed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $requests = collect(Request::where('status','completed')->get())->pluck('id')->toArray();
        if(DB::table('tasks')->whereIn('request_id',$requests)->where('status','pending')->update(['status' => 'completed']))
            Log::info('all tasks completed');
    }
}
