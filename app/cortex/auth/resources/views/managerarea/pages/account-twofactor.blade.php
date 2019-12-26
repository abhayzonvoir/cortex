{{-- Master Layout --}}
@extends('cortex/foundation::managerarea.layouts.default')

{{-- Page Title --}}
@section('title')
    {{ extract_title(Breadcrumbs::render()) }}
@endsection

{{-- Main Content --}}
@section('content')

    <div class="content-wrapper">

        <section class="content">

            <div class="row profile">
                <div class="col-md-3">
                    @include('cortex/auth::managerarea.partials.sidebar')
                </div>

                <div class="col-md-9">

                    <div class="profile-content">

                        <div class="tab-content">

                            <div role="tabpanel" class="tab-pane active" id="security">

                                {{ Form::open(['url' => route('managerarea.account.twofactor.totp.update'), 'id' => 'managerarea-twofactor-totp-form']) }}

                                    <h3 class="centered">
                                        @if(array_get($twoFactor, 'totp.enabled') || array_get($twoFactor, 'phone.enabled'))
                                            {!! trans('cortex/auth::twofactor.active') !!}
                                        @else
                                            {!! trans('cortex/auth::twofactor.inactive') !!}
                                        @endif
                                    </h3>

                                    <p class="text-justify">{{ trans('cortex/auth::twofactor.notice') }}</p>

                                    <div class="panel panel-primary">
                                        <header class="panel-heading">
                                            @if(! empty($twoFactor['totp']['enabled']))
                                                <a class="btn btn-default btn-flat btn-xs pull-right" href="{{ route('managerarea.account.twofactor.totp.disable') }}" onclick="event.preventDefault(); var form = document.getElementById('managerarea-twofactor-totp-form'); form.action = '{{ route('managerarea.account.twofactor.totp.disable') }}'; form.submit();">{{ trans('cortex/auth::common.disable') }}</a>
                                                <a class="btn btn-default btn-flat btn-xs pull-right" style="margin-right: 10px" href="{{ route('managerarea.account.twofactor.totp.enable') }}">{{ trans('cortex/auth::common.settings') }}</a>
                                            @else
                                                <a class="btn btn-default btn-flat btn-xs pull-right" href="{{ route('managerarea.account.twofactor.totp.enable') }}">{{ trans('cortex/auth::common.enable') }}</a>
                                            @endif

                                            <h3 class="panel-title">
                                                {{ trans('cortex/auth::twofactor.totp_head') }}
                                            </h3>
                                        </header>
                                        <div class="panel-body">
                                            {!! trans('cortex/auth::twofactor.totp_body') !!}
                                        </div>
                                    </div>

                                    <div class="panel panel-primary">
                                        <header class="panel-heading">
                                            <a class="btn btn-default btn-flat btn-xs pull-right" href="{{ route('managerarea.account.twofactor.phone.'.(! empty($twoFactor['phone']['enabled']) ? 'disable' : 'enable')) }}" onclick="event.preventDefault(); var form = document.getElementById('managerarea-twofactor-totp-form'); form.action = '{{ route('managerarea.account.twofactor.phone.'.(! empty($twoFactor['phone']['enabled']) ? 'disable' : 'enable')) }}'; form.submit();">{{ trans('cortex/auth::common.'.(! empty($twoFactor['phone']['enabled']) ? 'disable' : 'enable')) }}</a>

                                            <h3 class="panel-title">
                                                {{ trans('cortex/auth::twofactor.phone_head') }}
                                            </h3>
                                        </header>
                                        <div class="panel-body">
                                            {{ trans('cortex/auth::twofactor.phone_body') }}
                                        </div>
                                    </div>

                                {{ Form::close() }}

                            </div>

                        </div>

                    </div>
                </div>
            </div>

        </section>

    </div>

@endsection
