<?php

namespace App\Console\Commands;

use App\Models\Request;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateRequestDeliveredToCompleted extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:request';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the delivered request to completed';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        foreach (DB::table('requests')->get() as $request)
        {
            if($request->status == "delivered")
            {
                if(now()->diffInDays(Carbon::parse($request->updated_at),false) <= -1)
                {
                    $this->info(now()->diffInDays(Carbon::parse($request->updated_at),false).' -'.$request->id);
                    DB::table('requests')->where('status','=','delivered')->update(['status' => 'completed']);
                }
            }
        }
        Log::info('update request cron working once a day working, date: '.now());
    }
}
