<div class="body-wrapper-inner">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4"><a class="card-title fw-semibold mb-4"
                        href="{{ route('admin.dashboard') }}">Dashboard</a> / Change Password</h5>
                <div class="card">
                    <div class="card-body">
                        <form id="change_pwd_form" method="POST" action="{{ route('admin.change_pwd_store') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" id="password"
                                    placeholder="Please enter password">
                            </div>
                            <div class="mb-3">
                                <label for="c_password" class="form-label">Confirm Password</label>
                                <input type="password" name="c_password" class="form-control" id="c_password"
                                    placeholder="Please confirm your password">
                            </div>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
