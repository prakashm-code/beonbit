<div class="body-wrapper-inner">
    <div class="container-fluid">
        <div class="row">
            <div class="d-sm-flex d-block align-items-center justify-content-between mb-9">
                <div class="mb-3 mb-sm-0">
                    <h5 class="card-title fw-semibold">Referral Position of {{ $get_username->first_name.' '.$get_username->last_name }}</h5>
                    <p class="text-muted">
                        Shows your level under other users in the referral network
                    </p>
                </div>
            </div>
            <div class="col-lg-12 d-flex align-items-stretch">

                <div class="card w-100">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table dataTable no-footer">
                                <thead>
                                    <tr>
                                        <th>Level</th>
                                        <th>User Name</th>
                                        <th>User Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($result as $level => $users)
                                        @foreach ($users as $user)
                                            <tr>
                                                <td>{{ ucfirst(str_replace('_', ' ', $level)) }}</td>
                                                <td>{{ $user['name'] }}</td>
                                                <td>{{ $user['email'] }}</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
