{{-- Master Layout --}}
@extends('cortex/foundation::managerarea.layouts.auth')

{{-- Page Title --}}
@section('title')
    {{ extract_title(Breadcrumbs::render()) }}
@endsection

{{-- Scripts --}}
@push('inline-scripts')
    {!! JsValidator::formRequest(Cortex\Auth\Http\Requests\Managerarea\PhoneVerificationProcessRequest::class)->selector('#managerarea-verification-phone-token-form')->ignore('.skip-validation') !!}
@endpush

{{-- Main Content --}}
@section('content')

    <div class="login-box">
        <div class="login-logo">
            <a href="{{ route('frontarea.home') }}"><b>{{ $currentTenant->name }}</b></a>
        </div>

        <div class="login-box-body">
            <p class="login-box-msg">{{ trans('cortex/auth::common.account_verification_phone') }}</p>

            {{ Form::open(['url' => route('managerarea.verification.phone.process'), 'id' => 'managerarea-verification-phone-token-form', 'role' => 'auth']) }}

                <div class="form-group has-feedback{{ $errors->has('token') ? ' has-error' : '' }}">
                    {{ Form::hidden('phone', old('phone', request('phone')), ['class' => 'skip-validation']) }}
                    {{ Form::text('token', null, ['class' => 'form-control input-lg', 'placeholder' => trans('cortex/auth::common.authentication_code'), 'required' => 'required', 'autofocus' => 'autofocus']) }}

                    @if ($errors->has('token'))
                        <span class="help-block">{{ $errors->first('token') }}</span>
                    @endif

                    @if (session()->get('cortex.auth.twofactor.phone'))
                        {!! trans('cortex/auth::twofactor.backup_phone', ['href' => route('tenantarea.verification.phone.request')]) !!}
                    @elseif(session()->get('cortex.auth.twofactor.totp'))
                        {!! trans('cortex/auth::twofactor.backup_totp') !!}
                    @endif
                </div>

                {{ Form::button('<i class="fa fa-check"></i> '.trans('cortex/auth::common.verify_phone'), ['class' => 'btn btn-lg btn-primary btn-block', 'type' => 'submit']) }}

            {{ Form::close() }}

            {{ Html::link(route('managerarea.login'), trans('cortex/auth::common.account_login')) }}

        </div>

    </div>

@endsection
