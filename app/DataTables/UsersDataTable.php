<?php

namespace App\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class UsersDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $users = User::whereHas("roles", function($q){ $q->where("name","!=","super admin"); })->get();
        return (new EloquentDataTable($query))
            ->addColumn('date', function($user){
                return $user->created_at->format('M d, Y');
            })
            ->addColumn('full_name',function($user){
                return ucwords($user->firstname.' '.$user->lastname);
            })
            ->addColumn('roles',function($user){
                $roles = '';
                foreach($user->getRoleNames() as $role)
                {
                    $roles .= '<span class="badge badge-success mr-1">'.$role.'</span>';
                }
                return $roles;
            })
            ->addColumn('action', function($user){
                $action = '';

                if(auth()->user()->can('edit user'))
                {
                    $action .= '<button class="btn btn-primary btn-xs mr-1 edit-backend-user" id="'.$user->id.'">Edit</button>';
                }
                if(auth()->user()->can('delete user'))
                {
                    $action .= '<button class="btn btn-danger btn-xs mr-1 delete-backend-user" id="'.$user->id.'">Delete</button>';
                }
                return $action;
            })
            ->setRowId('id')
            ->rawColumns(['roles','action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(User $model): QueryBuilder
    {
        return $model::whereHas("roles", function($q){ $q->where("name","!=","super admin"); })->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('new-backend-users-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    //->dom('Bfrtip')
                    ->orderBy(1)
                    ->selectStyleSingle()
                    ->buttons([
                        Button::make('excel'),
                        Button::make('csv'),
                        Button::make('pdf'),
                        Button::make('print'),
                        Button::make('reset'),
                        Button::make('reload')
                    ]);
//                    ->parameters([
//                'dom'          => 'Bfrtip',
//                'buttons'      => ['export', 'print', 'reset', 'reload'],
//            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('date')
                  ->exportable(false)
                  ->printable(false)
                  ->width(100)
                  ->addClass('text-center'),
            Column::make('full_name'),
            Column::make('date_of_birth'),
            Column::make('mobile_number'),
            Column::make('email'),
            Column::make('username'),
            Column::make('roles'),
            Column::make('action'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Users_' . date('YmdHis');
    }
}
