{{-- Master Layout --}}
@extends('cortex/foundation::adminarea.layouts.default')

{{-- Page Title --}}
@section('title')
    {{ extract_title(Breadcrumbs::render()) }}
@endsection

{{-- Main Content --}}
@section('content')

    <div class="content-wrapper">
        <section class="content-header">
            <h1>{{ Breadcrumbs::render() }}</h1>
        </section>

        {{-- Main content --}}
        <section class="content">

            <div class="nav-tabs-custom">
                {!! Menu::render("{$tabs}", 'nav-tab') !!}

                <div class="tab-content">

                    <div class="tab-pane active" id="{{ $id }}-tab">
                        {{ Form::open(['url' => $url, 'class' => 'dropzone', 'id' => "{$id}-media-dropzone", 'data-dz-accepted-files' => 'application/vnd.ms-excel']) }}
                            <div class="dz-message" data-dz-message><span>{{ trans('cortex/foundation::common.drop_to_import') }}</span></div>
                        {{ Form::close() }}
                        {!! $dataTable->table(['class' => 'table table-striped table-hover responsive dataTableBuilder', 'id' => "{$id}"]) !!}
                    </div>

                </div>

            </div>

        </section>

    </div>

@endsection

@push('head-elements')
    <meta name="turbolinks-cache-control" content="no-cache">
@endpush

@push('styles')
    <link href="{{ mix('css/datatables.css') }}" rel="stylesheet">
@endpush

@push('vendor-scripts')
    <script src="{{ mix('js/datatables.js') }}" defer></script>
@endpush

@push('inline-scripts')
    {!! $dataTable->scripts() !!}
@endpush
