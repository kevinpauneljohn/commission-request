<?php

namespace App\View\Components\Request;

use App\Models\Request;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class InfoBox extends Component
{
    public $declined;
    public $pending;
    public $on_going;
    public $completed;
    public $delivered;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $user = auth()->user();
        if($user->hasRole('sales director'))
        {
            $this->declined = Request::where('status','declined')->where('user_id',$user->id)->count();
            $this->pending = Request::where('status','pending')->where('user_id',$user->id)->count();
            $this->on_going = Request::where('status','on-going')->where('user_id',$user->id)->count();
            $this->completed = Request::where('status','completed')->where('user_id',$user->id)->count();
            $this->delivered = Request::where('status','delivered')->where('user_id',$user->id)->count();
        }else{
            $this->declined = Request::where('status','declined')->count();
            $this->pending = Request::where('status','pending')->count();
            $this->on_going = Request::where('status','on-going')->count();
            $this->completed = Request::where('status','completed')->count();
            $this->delivered = Request::where('status','delivered')->count();
        }

    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.request.info-box');
    }
}
