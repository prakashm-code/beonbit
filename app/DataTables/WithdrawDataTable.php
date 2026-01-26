<?php

namespace App\DataTables;

use App\Models\WithdrawRequest;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class WithdrawDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<WithdrawRequest> $query Results from query() method.
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
                            // foreach ($keywords as $word) {
                            //     $q->orWhere(function ($subQuery) use ($word) {
                            //         $subQuery->where('level', 'LIKE', "%{$word}%");
                            //     });
                            // }
                            $q->whereHas('user', function ($q) use ($keyword) {
                                $q->where('email', 'LIKE', "%{$keyword}%");
                            });
                            $q->orWhere('amount', 'LIKE', "%{$keyword}%");
                            $q->orWhere('status', 'LIKE', "%{$keyword}%");
                            $q->orWhere('method', 'LIKE', "%{$keyword}%");
                            $q->orWhere('created_at', 'LIKE', "%{$keyword}%");
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
            ->addColumn('amount', function ($row) {
                return '$'.$row->amount;
            })
            ->addColumn('status', function ($row) {

                $statuses = ['pending', 'approved', 'reject'];

                $html = '<select class="form-select withdrawal-status"
                    data-id="' . $row->id . '">';

                foreach ($statuses as $status) {
                    $selected = $row->status === $status ? 'selected' : '';
                    $html .= '<option value="' . $status . '" ' . $selected . '>'
                        . ucfirst($status) .
                        '</option>';
                }

                $html .= '</select>';

                // return 'Success';
                return $html;
            })

            ->addColumn('method', function ($row) {
                return $row->method;
            })
            ->addColumn('created_at', function ($row) {
                return $row->created_at;
            })
            ->addColumn('actions', function ($row) {
                $cryptId = encrypt($row->id);
                $template_delete = decrypt($cryptId);
                $delete_url = route('admin.referral_setting_destroy', $cryptId);
                $edit_url = route('admin.referral_setting_edit', $cryptId);

                return '<div class="action-icon" style="gap: 20px;display: flex">
                            <form id="delete_referral_setting_form' . $template_delete . '" action="' . $delete_url . '" method="POST">' .
                    csrf_field() .
                    '<button style="background:transparent;border:none;"     type="button" data-id="' . $template_delete . '" class="deleteButton-Icon delete_referral_setting"><i class="ti ti-trash"></i></button></form>
                            </div>';
            })

            ->rawColumns(['checkbox', 'email', 'amount', 'status', 'method', 'created_at', 'actions']);
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<WithdrawRequest>
     */
    public function query(WithdrawRequest $model, Request $request): QueryBuilder
    {
        $columns = [
            0 => 'id',
            1 => 'user_id',
            2 => 'amount',
            3 => 'status',
            4 => 'method',
            5 => 'created_at',
        ];

        $orderIndex = $request->input('order.0.column', 0);
        $column = $columns[$orderIndex] ?? 'id';


        $direction = 'desc';

        if (isset($request->order[0]['dir']) && $request->order[0]['dir'] == 'asc') {
            $direction = 'asc';
        }

        return WithdrawRequest::query()->with('user')->orderBy($column, $direction);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('withdraw-table')
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
            Column::make('amount')->title('Withdarwal Amount($)')->orderable(true),
            Column::make('status')->title('Status')->orderable(true),
            Column::make('method')->title('Method')->orderable(true),
            Column::make('created_at')->title(value: 'Request Date')->orderable(true),
            // Column::make('actions')->title('Actions')->orderable(false),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Withdraw_' . date('YmdHis');
    }
}
