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
use Illuminate\Http\Request;

class UsersDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<User> $query Results from query() method.
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
                                    $subQuery->where('first_name', 'LIKE', "%{$word}%")
                                        ->orWhere('last_name', 'LIKE', "%{$word}%");
                                });
                            }
                            $q->orWhere('email', 'LIKE', "%{$keyword}%");
                            $q->orWhere('phone', 'LIKE', "%{$keyword}%");
                            $q->orWhere('country', 'LIKE', "%{$keyword}%");
                        });
                    }
                }
            })
            ->addColumn('checkbox', function ($row) {
                return '<input type="checkbox" class="row-checkbox" value="' . $row->id . '">';
            })
            ->addColumn('name', function ($row) {
                return $row->first_name . ' ' . $row->last_name;
            })
            ->addColumn('email', function ($row) {
                return $row->email;
            })
            ->addColumn('phone', function ($row) {
                return $row->phone;
            })
            ->addColumn('country', function ($row) {
                return $row->country;
            })
            ->addColumn('actions', function ($row) {
                $cryptId = encrypt($row->id);
                $template_delete = decrypt($cryptId);
                // $edit_url = route('admin.user_edit', $cryptId);
                $delete_url = route('admin.user_delete', $cryptId);
                $edit_url = "";

                // return '<div class="action-icon" style="gap: 20px;display: flex">
                //             <a class="" href="' .  $edit_url . '" title="Edit"><i class="ti ti-edit"></i></a>
                //             <form id="delete_user_form' . $template_delete . '" action="' . $delete_url . '" method="POST">' .
                //     csrf_field() . // Changed from @csrf to csrf_field()
                //     '<button style="background:transparent;border:none;"     type="button" data-id="' . $template_delete . '" class="deleteButton-Icon delete_user"><i class="ti ti-trash"></i></button></form>
                //             </div>';

                return '<div class="action-icon" style="gap: 20px;display: flex">
                            <form id="delete_user_form' . $template_delete . '" action="' . $delete_url . '" method="POST">' .
                    csrf_field() .
                    '<button style="background:transparent;border:none;"     type="button" data-id="' . $template_delete . '" class="deleteButton-Icon delete_user"><i class="ti ti-trash"></i></button></form>
                            </div>';
            })

            ->rawColumns(['checkbox', 'name', 'email', 'phone', 'country', 'actions']);
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<User>
     */
    public function query(User $model, Request $request): QueryBuilder
    {
        $columns = [
            0 => 'id',
            1 => 'first_name',
            2 => 'email',
            3 => 'phone',
            4 => 'country',
        ];

        $orderIndex = $request->input('order.0.column', 0);
        $column = $columns[$orderIndex] ?? 'id';


        $direction = 'desc';

        if (isset($request->order[0]['dir']) && $request->order[0]['dir'] == 'asc') {
            $direction = 'asc';
        }

        return User::query()->where('role', '0')->orderBy($column, $direction);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('users-table')
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
            // Column::make('checkbox')
            //     ->title('<input type="checkbox" id="select-all">')
            //     ->orderable(false)
            //     ->searchable(false),
            Column::make('no')->title('No')->orderable(false),
            Column::make('name')->title('Name')->orderable(true),
            Column::make('email')->title('Email')->orderable(true),
            Column::make('phone')->title('Phone no')->orderable(true),
            Column::make('country')->title('Country')->orderable(true),
            Column::make('actions')->title('Actions')->orderable(false),
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
