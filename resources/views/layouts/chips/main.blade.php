@include('layouts.chips.header')

<body id="page-top">
<div id="wrapper">
@include('layouts.chips.sidebar')
    <div class="d-flex flex-column" id="content-wrapper">
        <div id="content">
            @include('layouts.chips.navbar')
            <div class="container-fluid">
{{--                @include('layouts.chips.body')--}}
                @yield('content')
            </div>
        </div>
        @include('layouts.chips.footer')
</div>
@include('layouts.chips.scripts')
