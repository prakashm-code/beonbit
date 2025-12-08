<script src="{{ asset('assets/admin/libs/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('assets/admin/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/sidebarmenu.js') }}"></script>
<script src="{{ asset('assets/admin/js/app.min.js') }}"></script>
<script src="{{ asset('assets/admin/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/admin/libs/simplebar/dist/simplebar.js') }}"></script>
<script src="{{ asset('assets/admin/js/dashboard.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

<script src="{{ asset('assets/admin/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/additional-methods.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/toastr.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js"></script>

<script src="{{ asset('assets/admin/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/dataTables.responsive.min.js') }}"></script>
<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
@if (isset($js))
    @foreach ($js as $value)
        <script src="{{ asset('assets/admin') }}/js/{{ $value }}.js"></script>
    @endforeach
@endif

<script>
    var base_url = $("#base_url").val();

    @if (session('msg_error'))
        toastr.error("{{ session('msg_error') }}");
    @endif

    @if (session('msg_success'))
        toastr.success("{{ session('msg_success') }}");
    @endif

    @if (isset($errors) && $errors->any())
        @foreach ($errors->all() as $error)
            toastr.error("{{ $error }}");
        @endforeach
    @endif
</script>
