@extends('layouts.app')

@section('title', 'Referral')

@section('content_header')
    <h1>Referral Summary</h1>
@stop

@section('content')


    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row transactionContainer">
                        @foreach ($referralSummary as $key => $detail)
                            <div class="col-md-3 mt-2">
                                <div
                                    class="card
                                    {{ $loop->index % 6 == 0
                                        ? 'bg-primary'
                                        : ($loop->index % 6 == 1
                                            ? 'bg-success'
                                            : ($loop->index % 6 == 2
                                                ? 'bg-info'
                                                : ($loop->index % 6 == 3
                                                    ? 'bg-warning'
                                                    : ($loop->index % 6 == 4
                                                        ? 'bg-danger'
                                                        : 'bg-secondary')))) }}">
                                    <div class="card-header text-center text-white">
                                        {{ $key }}
                                    </div>
                                    <div class="card-body text-center text-white">
                                        <strong>{{ $detail }}</strong>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Reward List Card -->
            <div class="card mt-4">
                <div class="card-header bg-gold text-light">
                    <h4 style="text-align: center; margin: 0;">Reward List</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    {{-- <th>Referral Title</th> --}}
                                    <th>Referrer Name</th>
                                    <th>Total Referral Reward</th>
                                    <th>Referral Date</th>
                                    <th> Transaction ID </th>
                                    <th>Action</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($paginatedRewards as $key => $reward)
                                    {{-- @dd($reward->referrals) --}}
                                    <tr>
                                        <td>{{ $paginatedRewards->firstItem() + $key }}</td>
                                        {{-- <td>{{ $reward->referrals->title }}</td> --}}
                                        <td>{{ $reward->customer->name ?? 'N/A' }}</td>
                                        <td>{{ $reward->total_rewards }}</td>
                                        <td>{{ $reward->created_at->format('d-m-Y') }}</td>
                                        <td>{{ $reward->transaction_id }}</td>
                                        <td>
                                            <a href="{{ route('admin.referral.reward.show', $reward->customer_id) }}"
                                                class="btn btn-info btn-sm">History</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end">
                        {{ $paginatedRewards->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
