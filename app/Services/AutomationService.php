<?php

namespace App\Services;

use App\Models\Automation;
use Yajra\DataTables\DataTables;

class AutomationService
{
    public function create($request, $author): bool
    {
        return (bool)Automation::create([
            'title' => $request->title,
            'user_id' => $author,
            'is_active' => true
        ]);
    }



    public function lists($query)
    {
        return DataTables::of($query)
            ->editColumn('created_at',function($automation){
                return $automation->created_at->format('M d, Y g:i:s a');
            })
            ->editColumn('user_id',function($automation){
                return $automation->user->full_name;
            })
            ->editColumn('is_active',function($automation){
                return $automation->is_active ? '<span class="text-success">yes</span>' : '<span class="text-warning">no</span>';
            })
            ->addColumn('action',function($automation){
                $action = "";

                if(auth()->user()->can('view automation'))
                {
                    $action .= '<a href="'.route('automation.show',['automation' => $automation->id]).'" class="btn btn-xs btn-success view-automation-btn mr-1" id="'.$automation->id.'">View</a>';

                }
                if(auth()->user()->can('edit automation'))
                {
                    $action .= '<button class="btn btn-xs btn-primary edit-automation-btn mr-1" id="'.$automation->id.'">Edit</button>';

                }
                if(auth()->user()->can('delete automation'))
                {
                    $action .= '<button class="btn btn-xs btn-danger delete-automation-btn" id="'.$automation->id.'">Delete</button>';

                }
                return $action;
            })
            ->rawColumns(['action','is_active'])
            ->make(true);
    }
}
