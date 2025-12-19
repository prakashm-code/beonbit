<div class="body-wrapper-inner">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">
                    <a class="card-title fw-semibold mb-4" href="{{ route('admin.referral_setting') }}">Referral Setting list</a> / Edit Commission
                </h5>
                <div class="card">
                    <div class="card-body">
                        <form id="edit_referral_form" method="POST" action="{{ route('admin.referral_setting_update',encrypt($level->id)) }}">
                            @csrf
                            <div class="mb-3">
                                <label for="level" class="form-label">Referral Level</label>
                                <input type="text" name="level" class="form-control only-number" id="level"
                                    placeholder="Enter referral level" value="{{ $level->level }}">
                            </div>

                            <div class="mb-3">
                                <label for="percentage" class="form-label">Commission Percentage</label>
                                <input type="text" name="percentage" class="form-control only-decimal" id="percentage"
                                    placeholder="Enter referral Commission percentage" value="{{ $level->percentage }}">
                            </div>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
