<div class="body-wrapper-inner">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">
                    <a class="card-title fw-semibold mb-4" href="{{ route('admin.user') }}">Plan list</a> / Edit Plan
                </h5>

                <div class="card">
                    <div class="card-body">
                        <form id="edit_plan_form" method="POST" action="{{ route('admin.plan_update', encrypt($plan->id)) }}">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Plan Name</label>
                                <input type="text" name="name" class="form-control" id="name"
                                    value="{{ old('name', $plan->name) }}"
                                    placeholder="Enter plan name">
                            </div>

                            <div class="mb-3">
                                <label for="min_amount" class="form-label">Minimum Amount</label>
                                <input type="text" name="min_amount" class="form-control only-decimal" id="min_amount"
                                    value="{{ old('min_amount', $plan->min_amount) }}"
                                    placeholder="Enter minimum investment amount">
                            </div>

                            <div class="mb-3">
                                <label for="max_amount" class="form-label">Maximum Amount</label>
                                <input type="text" name="max_amount" class="form-control only-decimal" id="max_amount"
                                    value="{{ old('max_amount', $plan->max_amount) }}"
                                    placeholder="Enter maximum investment amount">
                            </div>

                            <div class="mb-3">
                                <label for="daily_roi" class="form-label">Rate Of Interest (%)</label>
                                <input type="text" name="daily_roi" class="form-control only-decimal" id="daily_roi"
                                    value="{{ old('daily_roi', $plan->daily_roi) }}"
                                    placeholder="Enter ROI (e.g., 12%)">
                            </div>

                            <div class="mb-3">
                                <label for="duration_days" class="form-label">Duration (Days)</label>
                                <input type="text" name="duration_days" class="form-control only-number" id="duration_days"
                                    value="{{ old('duration_days', $plan->duration_days) }}"
                                    placeholder="Enter duration in days">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Plan Type</label>
                                <select class="form-select" name="type" tabindex="1">
                                    <option value="" disabled>Select plan type</option>
                                    <option value="1" {{ $plan->type == 1 ? 'selected' : '' }}>Basic</option>
                                    <option value="2" {{ $plan->type == 2 ? 'selected' : '' }}>Advanced</option>
                                    <option value="3" {{ $plan->type == 3 ? 'selected' : '' }}>Premium</option>
                                    <option value="4" {{ $plan->type == 4 ? 'selected' : '' }}>Expert</option>
                                    <option value="5" {{ $plan->type == 5 ? 'selected' : '' }}>Master</option>
                                    <option value="6" {{ $plan->type == 6 ? 'selected' : '' }}>Professional</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-success">Update Plan</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
