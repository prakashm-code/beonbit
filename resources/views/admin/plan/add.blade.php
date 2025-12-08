<div class="body-wrapper-inner">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">
                    <a class="card-title fw-semibold mb-4" href="{{ route('admin.user') }}">Plan list</a> / Add Plan
                </h5>

                <div class="card">
                    <div class="card-body">
                        <form id="add_plan_form" method="POST" action="{{ route('admin.plan_store') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Plan Name</label>
                                <input type="text" name="name" class="form-control" id="name"
                                    placeholder="Enter plan name ">
                            </div>

                            <div class="mb-3">
                                <label for="min_amount" class="form-label">Minimum Amount</label>
                                <input type="text" name="min_amount" class="form-control only-decimal" id="min_amount"
                                    placeholder="Enter minimum investment amount">
                            </div>

                            <div class="mb-3">
                                <label for="max_amount" class="form-label">Maximum Amount</label>
                                <input type="text" name="max_amount" class="form-control only-decimal" id="max_amount"
                                    placeholder="Enter maximum investment amount">
                            </div>

                            <div class="mb-3">
                                <label for="daily_roi" class="form-label">Rate Of Interest (%)</label>
                                <input type="text" name="daily_roi" class="form-control only-decimal" id="daily_roi"
                                    placeholder="Enter ROI (e.g., 12%)">
                            </div>

                            <div class="mb-3">
                                <label for="duration_days" class="form-label">Duration (Days)</label>
                                <input type="text" name="duration_days" class="form-control only-number" id="duration_days"
                                    placeholder="Enter duration in days">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Plan Type</label>
                                <select class="form-select" name="type" tabindex="1">
                                    <option value="" selected disabled>Select plan type</option>
                                    <option value="1">Basic</option>
                                    <option value="2">Advanced</option>
                                    <option value="3">Premium</option>
                                    <option value="4">Expert</option>
                                    <option value="5">Master</option>
                                    <option value="6">Professional</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">Add</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
