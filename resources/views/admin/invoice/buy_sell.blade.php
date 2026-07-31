<!DOCTYPE html>
<html>

<head>
    <title>{{ $type }} Report</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        h2 {
            color: #343a40;
            margin-bottom: 20px; /* Add some space below the title */
        }

        table {
            width: 100%;
            border-collapse: collapse; /* Ensure borders collapse for cleaner look */
            margin-bottom: 20px; /* Space below the table */
        }

        th {
            background-color: #007bff;
            color: white;
            padding: 10px; /* Padding for table headers */
            text-align: left; /* Align text to the left for readability */
        }

        td {
            padding: 10px; /* Padding for table cells */
            border: 1px solid #dee2e6; /* Add a border for table cells */
            word-wrap: break-word; /* Ensure text wraps in cells */
            overflow-wrap: break-word; /* Handle overflow */
        }

        .no-transactions {
            text-align: center;
            margin-top: 20px;
            font-style: italic;
        }
    </style>
</head>

<body>
    <h2>{{ Str::ucfirst($type) }} Transaction Report</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Transaction ID</th>
            </tr>
        </thead>
        <tbody>
            @php
                $i = 1;
            @endphp
            @if (count($transactions) > 0)
                @foreach ($transactions as $transaction)
                    <tr>
                        <td>{{ $i++ }}</td>
                        <td>{{ number_format($transaction->transaction_amount, 3) }}</td>
                        <td>{{ date('Y-m-d', strtotime($transaction->transaction_date)) }}</td>
                        <td>{{ $transaction->tnx_id }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="4" class="no-transactions">No transactions found.</td>
                </tr>
            @endif
        </tbody>
    </table>
</body>

</html>
