<?php

namespace App\DataTables;

use App\Models\ReferralSetting;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Http\Request;

class ReferralSettingDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<ReferralSetting> $query Results from query() method.
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
                            $q->where('level', 'LIKE', "%{$keyword}%");
                            $q->orWhere('percentage', 'LIKE', "%{$keyword}%");
                        });
                    }
                }
            })
            ->addColumn('checkbox', function ($row) {
                return '<input type="checkbox" class="row-checkbox" value="' . $row->id . '">';
            })
            ->addColumn('level', function ($row) {
                return $row->level;
            })
            ->addColumn('percentage', function ($row) {
                return $row->percentage;
            })
            ->addColumn('status', function ($row) {
                $checked = $row->status == 1 ? 'checked' : '';
                return '  <div class="form-check form-switch">
        <input class="form-check-input status-toggle" type="checkbox" id="color-primary" data-id="' . $row->id . '"  ' . $checked . '>
    </div>';
            })
            ->addColumn('actions', function ($row) {
                $cryptId = encrypt($row->id);
                $template_delete = decrypt($cryptId);
                $delete_url = route('admin.referral_setting_destroy', $cryptId);
                $edit_url = route('admin.referral_setting_edit', $cryptId);

                return '<div class="action-icon" style="gap: 20px;display: flex">
                             <a class="" href="' .  $edit_url . '" title="Edit"><i class="ti ti-edit"></i></a>
                            <form id="delete_referral_setting_form' . $template_delete . '" action="' . $delete_url . '" method="POST">' .
                    csrf_field() .
                    '<button style="background:transparent;border:none;"     type="button" data-id="' . $template_delete . '" class="deleteButton-Icon delete_referral_setting"><i class="ti ti-trash"></i></button></form>
                            </div>';
            })

            ->rawColumns(['checkbox', 'level', 'percentage', 'status', 'actions']);
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<ReferralSetting>
     */
    public function query(ReferralSetting $model, Request $request): QueryBuilder
    {
        $columns = [
            0 => 'id',
            1 => 'level',
            2 => 'percentage',
            3 => 'status',
        ];

        $orderIndex = $request->input('order.0.column', 0);
        $column = $columns[$orderIndex] ?? 'id';


        $direction = 'desc';

        if (isset($request->order[0]['dir']) && $request->order[0]['dir'] == 'asc') {
            $direction = 'asc';
        }

        return ReferralSetting::query()->orderBy($column, $direction);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('referralsetting-table')
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
            Column::make('level')->title('Level')->orderable(true),
            Column::make('percentage')->title('Commission %')->orderable(true),
            Column::make('status')->title('Status')->orderable(false),
            Column::make('actions')->title('Actions')->orderable(false),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'ReferralSetting_' . date('YmdHis');
    }
}
