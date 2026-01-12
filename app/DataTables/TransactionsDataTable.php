<?php

namespace App\DataTables;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Http\Request;

class TransactionsDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Transaction> $query Results from query() method.
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
                            //         $subQuery->where('name', 'LIKE', "%{$word}%");
                            //         // ->orWhere('max_amount', 'LIKE', "%{$word}%");
                            //     });
                            // }
                            $q->whereHas('user', function ($q) use ($keyword) {
                                $q->where('email', 'LIKE', "%{$keyword}%");
                            });
                            $q->orWhere('Amount', 'LIKE', "%{$keyword}%");
                            $q->orWhere('commission', 'LIKE', "%{$keyword}%");
                            $q->orWhere('description', 'LIKE', "%{$keyword}%");
                            $q->orWhere('balance_after', 'LIKE', "%{$keyword}%");
                            $q->orWhere('transaction_reference', 'LIKE', "%{$keyword}%");
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
            ->addColumn('type', function ($row) {
                return $row->description;
            })
            ->addColumn('amount', function ($row) {
                return '$'.$row->amount;
            })
            ->addColumn('commission', function ($row) {
                return '$'.$row->commission;
            })

            ->addColumn('balance_after', function ($row) {
                return '$'.$row->balance_after;
            })
            ->addColumn('transaction_reference', function ($row) {
                return $row->transaction_reference;
            })
            ->addColumn('transaction_date', function ($row) {
                return $row->created_at;
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

            ->rawColumns(['checkbox', 'email', 'type', 'amount','commission', 'balance_after', 'transaction_reference', 'transaction_date', 'actions']);
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Transaction>
     */
    public function query(Transaction $model, Request $request): QueryBuilder
    {
        $columns = [
            0 => 'id',
            1 => 'user_id',
            2 => 'type',
            3 => 'amount',
            4 => 'commission',
            5 => 'balance_after',
            6 => 'transaction_reference',
            7 => 'transaction_date'
        ];

        $orderIndex = $request->input('order.0.column', 0);
        $column = $columns[$orderIndex] ?? 'id';


        $direction = 'desc';

        if (isset($request->order[0]['dir']) && $request->order[0]['dir'] == 'asc') {
            $direction = 'asc';
        }

        return Transaction::query()->with('user')->orderBy($column, $direction);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('transactions-table')
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
            Column::make('type')->title('Type')->orderable(true),
            Column::make('amount')->title('Amount($)')->orderable(false),
            Column::make('commission')->title('Commission($)')->orderable(false),
            Column::make('balance_after')->title('Balance After($)')->orderable(false),
            Column::make('transaction_reference')->title('Transaction Reference')->orderable(false),
            Column::make('transaction_date')->title('Transaction Date')->orderable(false),
            // Column::make('actions')->title('Actions')->orderable(false)
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Transactions_' . date('YmdHis');
    }
}
