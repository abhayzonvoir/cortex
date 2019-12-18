{{-- Master Layout --}}
@extends('cortex/foundation::tenantarea.layouts.default')

{{-- Page Title --}}
@section('title')
    {{ extract_title(Breadcrumbs::render()) }}
@endsection

@push('inline-scripts')
    {!! JsValidator::formRequest(Cortex\Auth\Http\Requests\Tenantarea\AccountAttributesRequest::class)->selector('#tenantarea-account-attributes-form')->ignore('.skip-validation') !!}
@endpush

{{-- Main Content --}}
@section('content')

    <div class="container">
        <div class="row profile">
            <div class="col-md-3">
                @include('cortex/auth::tenantarea.partials.sidebar')
            </div>
            <div class="col-md-9">
                <div class="profile-content">

                    {{ Form::model($currentUser, ['url' => route('tenantarea.account.attributes.update'), 'id' => 'tenantarea-account-attributes-form']) }}

                        @attributes($currentUser)

                        @if($currentUser->getEntityAttributes()->isNotEmpty())
                            <div class="row">
                                <div class="col-md-12 text-center profile-buttons">
                                    {{ Form::button('<i class="fa fa-save"></i> '.trans('cortex/auth::common.update_attributes'), ['class' => 'btn btn-primary btn-flat', 'type' => 'submit']) }}
                                </div>
                            </div>
                        @endif

                    {{ Form::close() }}

                </div>
            </div>
        </div>
    </div>

@endsection
