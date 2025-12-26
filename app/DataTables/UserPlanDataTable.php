<?php

namespace App\DataTables;

use App\Models\UserPlan;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Http\Request;

class UserPlanDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<UserPlan> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $counter = 1;

        return datatables()
            ->eloquent($query)
            ->addColumn('no', function () use (&$counter) {
                return $counter++;
            })
            ->filter(function ($query) {
                if ($this->request->has('search')) {
                    $keyword = trim($this->request->get('search')['value']);
                    if ($keyword !== '') {
                        $keywords = explode(' ', $keyword);

                        $query->where(function ($q) use ($keywords, $keyword) {
                            //     foreach ($keywords as $word) {
                            //         $q->orWhere(function ($subQuery) use ($word) {
                            //             $subQuery->where('name', 'LIKE', "%{$word}%");
                            //             // ->orWhere('max_amount', 'LIKE', "%{$word}%");
                            //         });
                            //     }
                            $q->whereHas('user', function ($q) use ($keyword) {
                                $q->where('email', 'LIKE', "%{$keyword}%");
                            });
                            $q->orWhereHas('plan', function ($q) use ($keyword) {
                                $q->where('name', 'LIKE', "%{$keyword}%");
                            });
                            $q->orWhere('start_date', 'LIKE', "%{$keyword}%");
                            $q->orWhere('end_date', 'LIKE', "%{$keyword}%");
                            $q->orWhere('amount', 'LIKE', "%{$keyword}%");
                            $q->orWhere('daily_return_percent', 'LIKE', "%{$keyword}%");
                            $q->orWhere('status', 'LIKE', "%{$keyword}%");
                        });
                    }
                }
            })
            ->addColumn('checkbox', function ($row) {
                return '<input type="checkbox" class="row-checkbox" value="' . $row->id . '">';
            })
            ->addColumn('email', function ($row) {
                return $row->user->email;
            })
            ->addColumn('plan', function ($row) {
                return $row->plan->name;
            })
            ->addColumn('start_date', function ($row) {
                return $row->start_date;
            })
            ->addColumn('end_date', function ($row) {
                return $row->end_date;
            })
            ->addColumn('amount', function ($row) {
                return $row->amount;
            })
            ->addColumn('status', function ($row) {
                return $row->status;
            })


            ->addColumn('actions', function ($row) {
                $cryptId = encrypt($row->id);
                $template_delete = decrypt($cryptId);
                // $delete_url = route('admin.plan_destroy', $cryptId);
                $delete_url = "";

                return '<div class="action-icon" style="gap: 20px;display: flex">
                            <form id="delete_plan_form' . $template_delete . '" action="' . $delete_url . '" method="POST">' .
                    csrf_field() .
                    '<button style="background:transparent;border:none;"     type="button" data-id="' . $template_delete . '" class="deleteButton-Icon delete_plan"><i class="ti ti-trash"></i></button></form>
                            </div>';
            })

            ->rawColumns(['checkbox', 'email', 'plan', 'start_date', 'end_date', 'amount', 'status', 'actions']);
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<UserPlan>
     */
    public function query(UserPlan $model, Request $request): QueryBuilder
    {
        $columns = [
            0 => 'id',
            1 => 'user_id',
            2 => 'plan_id',
            3 => 'start_date',
            4 => 'end_date',
            5 => 'daily_return_percent',
            6 => 'status'
        ];

        $orderIndex = $request->input('order.0.column', 0);
        $column = $columns[$orderIndex] ?? 'id';


        $direction = 'desc';

        if (isset($request->order[0]['dir']) && $request->order[0]['dir'] == 'asc') {
            $direction = 'asc';
        }

        return UserPlan::query()->with('user', 'plan')->orderBy($column, $direction);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('userplan-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0)
            ->selectStyleSingle()
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload')
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [

            // Column::make('checkbox')
            //     ->title('<input type="checkbox" id="select-all">')
            //     ->orderable(false)
            //     ->searchable(false),
            Column::make('no')->title('No')->orderable(false),
            Column::make('email')->title('Email')->orderable(true),
            Column::make('plan')->title('Plan')->orderable(true),
            Column::make('start_date')->title('Start Date')->orderable(false),
            Column::make('end_date')->title('End Date')->orderable(false),
            Column::make('amount')->title('Amount')->orderable(false),
            Column::make('status')->title('Status')->orderable(false),
            Column::make('actions')->title('Actions')->orderable(false)
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'UserPlan_' . date('YmdHis');
    }
}
