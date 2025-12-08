<?php

namespace App\DataTables;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Http\Request;

class PlanDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Plan> $query Results from query() method.
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
                            foreach ($keywords as $word) {
                                $q->orWhere(function ($subQuery) use ($word) {
                                    $subQuery->where('name', 'LIKE', "%{$word}%")
                                        ->orWhere('max_amount', 'LIKE', "%{$word}%");
                                });
                            }
                            $q->orWhere('min_amount', 'LIKE', "%{$keyword}%");
                        });
                    }
                }
            })
            ->addColumn('checkbox', function ($row) {
                return '<input type="checkbox" class="row-checkbox" value="' . $row->id . '">';
            })
            ->addColumn('name', function ($row) {
                return $row->name;
            })
            ->addColumn('max_amount', function ($row) {
                return $row->max_amount;
            })
            ->addColumn('min_amount', function ($row) {
                return $row->min_amount;
            })
            ->addColumn('roi', function ($row) {
                return $row->daily_roi;
            })
            ->addColumn('duration', function ($row) {
                return $row->duration_days;
            })
            ->addColumn('type', function ($row) {
                $types = [
                    1 => 'Basic',
                    2 => 'Advanced',
                    3 => 'Premium',
                    4 => 'Expert',
                    5 => 'Master',
                    6 => 'Professional'
                ];

                return $types[$row->type] ?? 'Unknown';
            })

            ->addColumn('status', function ($row) {
    $checked = $row->status == 1 ? 'checked' : '';
    return '  <div class="form-check form-switch">
        <input class="form-check-input status-toggle" type="checkbox" id="color-primary" data-id="'.$row->id.'"  '.$checked.'>
    </div>';
            })
            ->addColumn('actions', function ($row) {
                $cryptId = encrypt($row->id);
                $template_delete = decrypt($cryptId);
                $delete_url = route('admin.user_delete', $cryptId);
                $edit_url = route('admin.plan_edit', $cryptId);

                return '<div class="action-icon" style="gap: 20px;display: flex">
                             <a class="" href="' .  $edit_url . '" title="Edit"><i class="ti ti-edit"></i></a>
                            <form id="delete_plan_form' . $template_delete . '" action="' . $delete_url . '" method="POST">' .
                    csrf_field() .
                    '<button style="background:transparent;border:none;"     type="button" data-id="' . $template_delete . '" class="deleteButton-Icon delete_user"><i class="ti ti-trash"></i></button></form>
                            </div>';
            })

            ->rawColumns(['checkbox', 'name', 'max_amount', 'min_amount', 'roi', 'durations', 'type', 'status', 'actions']);
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Plan>
     */
    public function query(Plan $model, Request $request): QueryBuilder
    {
        $columns = [
            0 => 'id',
            1 => 'name',
            2 => 'min_amount',
            3 => 'max_amount',
            4 => 'daily_roi',
            5 => 'duration_days',
        ];

        $orderIndex = $request->input('order.0.column', 0);
        $column = $columns[$orderIndex] ?? 'id';


        $direction = 'desc';

        if (isset($request->order[0]['dir']) && $request->order[0]['dir'] == 'asc') {
            $direction = 'asc';
        }

        return Plan::query()->orderBy($column, $direction);
    }
    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('plan-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
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
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [

            Column::make('checkbox')
                ->title('<input type="checkbox" id="select-all">')
                ->orderable(false)
                ->searchable(false),
            Column::make('no')->title('No')->orderable(false),
            Column::make('name')->orderable(true),
            Column::make('min_amount')->title('Min Amount')->orderable(true),
            Column::make('max_amount')->title('Max Amount')->orderable(false),
            Column::make('roi')->title('ROI')->orderable(false),
            Column::make('duration')->title('Duration')->orderable(false),
            Column::make('type')->title('Type')->orderable(false),
            Column::make('status')->title('Status')->orderable(false),
            Column::make('actions')->title('Actions')->orderable(false),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Plan_' . date('YmdHis');
    }
}
