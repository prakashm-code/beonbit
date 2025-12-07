<div class="body-wrapper-inner">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4"><a class="card-title fw-semibold mb-4" href="javascript::void(0);">
                        {{ $privacy_policy != '' ? 'Edit Privacy & Policy' : 'Add Privacy & Policy' }}
                </h5>
                <div class="card">
                    <div class="card-body">
                        <form id="add_privacy_policy_form" method="POST"
                            action="{{ $privacy_policy != '' ? route('admin.privacy_policy_edit') : route('admin.privacy_policy_store') }}">
                            @csrf <div class="mb-3">
                                <input id="id" name="id"
                                    value="{{ isset($privacy_policy->id) ? $privacy_policy->id : '' }}" hidden />
                                <label for="title" class="form-label">Title</label>
                                <input type="text" name="title" class="form-control" id="title"
                                    value="{{ isset($privacy_policy->title) ? $privacy_policy->title : '' }}"
                                    aria-describedby="emailHelp" placeholder="Please enter title">
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control ckeditor" name="description" id="description"rows="3"
                                    placeholder="Please enter description">{{ isset($privacy_policy->content) ? $privacy_policy->content : '' }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Add</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    CKEDITOR.replace('description');
</script>
