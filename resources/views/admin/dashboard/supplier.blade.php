@extends('layouts.app')
@section('title', 'Supplier Dashboard')

@section('content_header')
    <h1>Supplier Dashboard</h1>
@stop
@section('styles')
    <!-- Include Daterangepicker CSS -->
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Overview</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Supplier Info Card -->
                        <div class="col-md-6 d-flex align-items-stretch">
                            <div class="card text-center shadow-sm border-0 w-100">
                                <div class="card-body py-4">
                                    <i class="fas fa-truck fa-3x text-primary mb-3"></i>
                                    <h5 class="card-title text-muted">Total Suppliers</h5>
                                    <h3 class="card-text font-weight-bold">{{ $totalSuppliers }}</h3>
                                </div>
                                <div class="card-footer">
                                    <p class="text-muted mb-0">Total Supplier Balance:
                                        <strong>{{ number_format($suppliersBalance, 2) }}</strong>
                                    </p>
                                    <p class="text-muted mb-0">Fixed Purchases:
                                        <strong>{{ $totalSupplierFixedPurchase }}</strong>
                                        ({{ number_format($totalSupplierFixedPurchaseAmount, 2) }})
                                    </p>
                                    <p class="text-muted mb-0">Unfixed Purchases:
                                        <strong>{{ $totalSupplierUnfixedPurchase }}</strong>
                                        ({{ number_format($totalSupplierUnfixedPurchaseAmount, 2) }})
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Client Info Card -->
                        <div class="col-md-6 d-flex align-items-stretch">
                            <div class="card text-center shadow-sm border-0 w-100">
                                <div class="card-body py-4">
                                    <i class="fas fa-users fa-3x text-success mb-3"></i>
                                    <h5 class="card-title text-muted">Total Clients</h5>
                                    <h3 class="card-text font-weight-bold">{{ $totalClients }}</h3>
                                </div>
                                <div class="card-footer">
                                    <p class="text-muted mb-0">Total Client Balance:
                                        <strong>{{ number_format($clientsBalance, 2) }}</strong>
                                    </p>
                                    <p class="text-muted mb-0">Fixed Purchases:
                                        <strong>{{ $totalClientFixedPurchase }}</strong>
                                        ({{ number_format($totalClientFixedPurchaseAmount, 2) }})
                                    </p>
                                    <p class="text-muted mb-0">Unfixed Purchases:
                                        <strong>{{ $totalClientUnfixedPurchase }}</strong>
                                        ({{ number_format($totalClientUnfixedPurchaseAmount, 2) }})
                                    </p>
                                </div>
                            </div>
                        </div>


                        <!-- Additional Cards if Needed -->
                        <!-- Additional Cards if Needed -->
                        <div class="col-md-6 d-flex align-items-stretch">
                            <div class="card text-center shadow-sm border-0 w-100">
                                <div class="card-body py-4">
                                    <i class="fas fa-money-bill-wave fa-3x text-info mb-3"></i>
                                    <h5 class="card-title text-muted">Supplier Deposit/Withdraw</h5>
                                    <div class="row">
                                        <!-- Deposits Column -->
                                        <div class="col-6 border-end">
                                            <h6 class="text-muted">Total Deposits</h6>
                                            <h3 class="card-text font-weight-bold">{{ number_format($supplierDeposit, 2) }}
                                            </h3>
                                            <p class="text-muted mb-0">Count: <strong>{{ $supplierDepositCount }}</strong>
                                            </p>
                                        </div>

                                        <!-- Withdrawals Column -->
                                        <div class="col-6">
                                            <h6 class="text-muted">Total Withdrawals</h6>
                                            <h3 class="card-text font-weight-bold">
                                                {{ number_format($supplierWithdraw, 2) }}
                                            </h3>
                                            <p class="text-muted mb-0">Count: <strong>{{ $supplierWithdrawCount }}</strong>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-6 d-flex align-items-stretch">
                            <div class="card text-center shadow-sm border-0 w-100">
                                <div class="card-body py-4">
                                    <i class="fas fa-money-bill-wave fa-3x text-info mb-3"></i>
                                    <h5 class="card-title text-muted">Client Deposit/Withdraw</h5>
                                    <div class="row">
                                        <!-- Deposits Column -->
                                        <div class="col-6 border-end">
                                            <h6 class="text-muted">Total Deposits</h6>
                                            <h3 class="card-text font-weight-bold">{{ number_format($totalDeposit, 2) }}
                                            </h3>
                                            <p class="text-muted mb-0">Count: <strong>{{ $depositCount }}</strong></p>
                                        </div>

                                        <!-- Withdrawals Column -->
                                        <div class="col-6">
                                            <h6 class="text-muted">Total Withdrawals</h6>
                                            <h3 class="card-text font-weight-bold">{{ number_format($totalWithdraw, 2) }}
                                            </h3>
                                            <p class="text-muted mb-0">Count: <strong>{{ $withdrawCount }}</strong></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
