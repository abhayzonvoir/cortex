{{-- Master Layout --}}
@extends('cortex/foundation::tenantarea.layouts.default')

{{-- Page Title --}}
@section('title')
    {{ extract_title(Breadcrumbs::render()) }}
@endsection

{{-- Scripts --}}
@push('inline-scripts')
    {!! JsValidator::formRequest(Cortex\Auth\Http\Requests\Tenantarea\PhoneVerificationProcessRequest::class)->selector('#tenantarea-verification-phone-token-form')->ignore('.skip-validation') !!}
@endpush

@section('body-attributes')class="auth-page"@endsection

{{-- Main Content --}}
@section('content')

    <div class="container">

        <div class="row">

            <div class="col-md-4 col-md-offset-4">

                <section class="auth-form">

                    {{ Form::open(['url' => route('tenantarea.verification.phone.process'), 'id' => 'tenantarea-verification-phone-token-form', 'role' => 'auth']) }}

                        <div class="centered"><strong>{{ trans('cortex/auth::common.account_verification_phone') }}</strong></div>

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

                        <div>
                            {{ Html::link(route('tenantarea.login'), trans('cortex/auth::common.account_login')) }}
                            {{ trans('cortex/foundation::common.or') }}
                            {{ Html::link(route('tenantarea.register'), trans('cortex/auth::common.account_register')) }}
                        </div>

                    {{ Form::close() }}

                </section>

            </div>

        </div>

    </div>

@endsection
