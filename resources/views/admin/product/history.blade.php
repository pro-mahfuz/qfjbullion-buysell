@extends('layouts.app')

@section('content')
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <!-- Reward History Card -->
            <div class="card mt-4">
                <div class="card-header bg-gold  d-flex justify-content-between">
                    <h4 style="margin: 0;">Reward History - {{ $rewards->first()->customer->name }}
                        ({{ $rewards->first()->customer->customer_code }})</h4>
                    <a href="{{ route('admin.referral.dashboard') }}" class="btn btn-secondary">Back</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Referral Title</th>
                                    <th>Referral Reward</th>
                                    <th>Referral Date</th>
                                    <th>Transaction ID</th>
                                    <th>Transaction Type</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rewards as $key => $reward)
                                    <tr>
                                        <td>{{ $rewards->firstItem() + $key }}</td>
                                        <td>{{ $reward->referrals->title }}</td>
                                        <td>{{ $reward->reward_amount }}</td>
                                        <td>{{ $reward->created_at->format('d-m-Y H:i:s') }}</td>
                                        <td>{{ $reward->transaction->tnx_id ?? 'N/A' }}</td>
                                        <td>{{ $reward->transaction->transaction_type ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end">
                        {{ $rewards->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
