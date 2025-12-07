<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

    @isset($css)
        <link rel="stylesheet" href="{{ asset('assets') }}/front/css/{{ $css }}" />
    @endisset


    <x-admin.header title="{{ $title }}" />
</head>

<body>

    {{-- <x-admin.auth_header title={{ $title }} /> --}}

    @include($page)

    {{-- {{dd($js);}} --}}

    <x-admin.footer :js="$js ?? []" page="{{ $page }}" />


</body>

</html>
