<div class="body-wrapper-inner">
    <div class="container-fluid">
        <div class="row">
            <div class="d-sm-flex d-block align-items-center justify-content-between mb-9">
                <div class="mb-3 mb-sm-0">
                    <h5 class="card-title fw-semibold">Withdraw Request List</h5>
                </div>
                <div>
\                </div>
            </div>
            <div class="col-lg-12 d-flex align-items-stretch">

                <div class="card w-100">
                    <div class="card-body">
                        <div class="table-responsive">
                            <button id="delete-selected" class="btn btn-danger mb-3" style="display:none;">
                                Delete Selected
                            </button>
                            {{ $dataTable->table() }}
                        </div>
                        {{ $dataTable->scripts(attributes: ['type' => 'module']) }}

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
