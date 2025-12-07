<div class="body-wrapper-inner">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4"><a class="card-title fw-semibold mb-4" href="javascript::void(0);">
                        {{ $term_condition != '' ? 'Edit Terms & Condition' : 'Add Terms & Condition' }}
                </h5>
                <div class="card">
                    <div class="card-body">
                        <form id="add_term_condition_form" method="POST"
                            action="{{ $term_condition != '' ? route('admin.term_conditions_edit') : route('admin.term_conditions_store') }}">
                            @csrf <div class="mb-3">
                                <input id="id" name="id"
                                    value="{{ isset($term_condition->id) ? $term_condition->id : '' }}" hidden />
                                <label for="title" class="form-label">Title</label>
                                <input type="text" name="title" class="form-control" id="title"
                                    value="{{ isset($term_condition->title) ? $term_condition->title : '' }}"
                                    aria-describedby="emailHelp" placeholder="Please enter title">
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control ckeditor" name="description" id="description"rows="3"
                                    placeholder="Please enter description">{{ isset($term_condition->content) ? $term_condition->content : '' }}</textarea>
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
