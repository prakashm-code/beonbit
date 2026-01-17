<div class="body-wrapper-inner">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4"><a class="card-title fw-semibold mb-4"
                        href="{{ route('admin.user') }}">User list</a> / Add User Plan</h5>
                <div class="card">
                    <div class="card-body">
                        <form id="add_plan_user_form" method="POST" action="{{ route('admin.user_plan_store') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">User Email</label>
                                <input type="text" name="email" class="form-control" id="email" value="{{ $user_email->email }}"
                                     readonly>
                            </div>
                            <div class="mb-3">
                                {{-- <label for="first_name" class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-control" id="first_name"
                                    placeholder="Please enter first name"> --}}
                                <input type="hidden" name="user_id" value="{{ $user_id }}"/>
                                {{-- <div class="form-group mb-4"> --}}
                                <label for="plan_id" class="form-label">Choose Plan</label>
                                <select class="form-select" id="plan_id" name="plan_id">
                                    @foreach ($getplans as $plan)
                                        <option value="{{ $plan->id }}"  data-min="{{ $plan->min_amount }}"
            data-max="{{ $plan->max_amount }}">{{ $plan->name }} (Min: ${{ $plan->min_amount }} | Max: ${{ $plan->max_amount }})</option>
                                    @endforeach

                                </select>
                                {{-- </div> --}}
                            </div>
                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount</label>
                                <input type="text" name="amount" class="form-control only-decimal" id="amount"
                                    placeholder="Enter investment amount">
                            </div>
                            <button type="submit" class="btn btn-primary">Add</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
