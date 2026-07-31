@extends('client.layouts.app')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop


@section('content')

    @php
        $completed = session()->get('is_completed');
    @endphp
    @if ($completed == 0 || $completed == null)
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Please Complete your profile.</h2>
                <a href="{{ route('client.profile') }}" class="btn btn-sm btn-primary float-right">Go to profile</a>
            </div>
        </div>
    @endif


    <div class="row">
        <div class="col-md-12">
            <div class="card">

                <div class="card-body">
                    <div class="row">

                        <div class="col-md-4 d-flex align-items-stretch">
                            <div class="card text-center shadow-sm border-0 w-100">
                                <div class="card-body py-4">
                                    <i class="fas fa-dollar-sign fa-2x text-info mb-3"></i>
                                    <h5 class="card-title text-muted">Total Deposit</h5>
                                    <h3 class="card-text font-weight-bold">{{ $totalDepositInCompleted }}</h3>
                                </div>
                                <div class="card-footer d-flex justify-content-between">
                                    <p class="text-muted mb-0">Approved :
                                        {{ number_format($totalDepositApproved, 3) }}
                                    </p>
                                    <p class="text-muted mb-0">Amount : {{ number_format($totalDepositInCompletedAvg, 3) }}
                                    </p>
                                </div>
                            </div>

                        </div>

                        <div class="col-md-4 d-flex align-items-stretch">
                            <div class="card text-center shadow-sm border-0 w-100">
                                <div class="card-body py-4">
                                    <i class="fas fa-dollar-sign fa-2x text-info mb-3"></i>
                                    <h5 class="card-title text-muted">Total WithDraw</h5>
                                    <h3 class="card-text font-weight-bold">
                                        {{ number_format($totalWithDrawInCompleted, 3) }}
                                    </h3>
                                </div>
                                <div class="card-footer d-flex justify-content-between">
                                    <p class="text-muted mb-0">Approved :
                                        {{ number_format($totalWithDrawApproved, 3) }}
                                    </p>
                                    <p class="text-muted mb-0">Amount :
                                        {{ number_format($totalWithDrawInCompletedAvg, 3) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 d-flex align-items-stretch">
                            <div class="card text-center shadow-sm border-0 w-100">
                                <div class="card-body py-4">
                                    <i class="fas fa-dollar-sign fa-2x text-info mb-3"></i>
                                    <h5 class="card-title text-muted">Total Transactions</h5>
                                    <h3 class="card-text font-weight-bold">
                                        {{ number_format($totalTransactions, 3) }}
                                    </h3>
                                </div>
                                <div class="card-footer d-flex justify-content-between">
                                    <p class="text-muted mb-0">profit :
                                        {{ number_format($profit, 3) }}
                                    </p>
                                    <p class="text-muted mb-0">loss :
                                        {{ number_format($loss, 3) }}</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
