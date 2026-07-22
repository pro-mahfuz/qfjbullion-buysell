@extends('layouts.app')


@section('title', 'Shop Product List')

@section('content_header')
    <h1>Shop Product List</h1>
@stop

@section('content')

    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-end">
            <a href="{{ route('admin.product.shop.create') }}" class="btn btn-success">
                <i class="fa fa-plus"></i> Create Product
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-responsive">
                    <table id="role-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Weight</th>
                                <th>Qty</th>
                                <th>Image</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($result) > 0)
                                <?php $sl = 0; ?>
                                @foreach ($result as $row)
                                    <?php $sl++; ?>
                                    <tr data-id="{{ $row->id }}">
                                        <td>{{ $sl }}</td>
                                        <td>{{ $row->title }}</td>
                                        <td>{{ $row->weight }}</td>
                                        {{-- <td>{{ $row->qty }}</td> --}}
                                        <td class="editable-qty">
                                            <span class="qty-text">{{ $row->qty }}</span>
                                            <input type="number" class="form-control qty-input" value="{{ $row->qty }}"
                                                style="display: none;" min="1">
                                        </td>


                                        <td class="text-center"><img src="{{ asset('images/shop/' . $row->image) }}"
                                                alt="Product Image" style="width: 50px; height: auto;"></td>
                                        <td>
                                            {{-- @dd( $row->id) --}}
                                            <form action="{{ route('admin.product.shop.delete', $row->id) }}" method="POST"
                                                style="display: inline;"
                                                onsubmit="return confirm('Are you sure you want to delete this product?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                            </form>


                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop


@section('js')
    <script>
        $(document).ready(function() {
            $('#product-table').DataTable();

            $(document).on('click', '.editable-qty', function() {
                const $cell = $(this);
                $cell.find('.qty-text').hide();
                $cell.find('.qty-input').show().focus();
            });

            $(document).on('blur', '.qty-input', function() {
                const $input = $(this);
                const $cell = $input.closest('.editable-qty');
                const newQty = $input.val();
                const productId = $cell.closest('tr').data('id');

                if (!newQty || isNaN(newQty) || parseInt(newQty) <= 0) {
                    alert('Invalid quantity');
                    $input.hide();
                    $cell.find('.qty-text').show();
                    return;
                }

                $.ajax({
                    url: `{{ route('admin.product.shop.updateQty') }}`,
                    method: 'POST',
                    data: {
                        id: productId,
                        qty: newQty,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            $cell.find('.qty-text').text(newQty).show();
                            $input.hide();
                            fire('Quantity updated successfully', 'success');
                        } else {
                            fire('Failed to update quantity', 'error');
                        }
                    },
                    error: function(xhr) {
                        fire('An error occurred while updating the quantity', 'error');
                        $input.hide();
                        $cell.find('.qty-text').show();
                    }
                });
            });

            $(document).on('keypress', '.qty-input', function(e) {
                if (e.key === 'Enter') {
                    $(this).blur();
                }
            });
        });

        function fire(msg, type) {
            new Swal({
                title: 'Good job!',
                text: msg,
                icon: type,
                showConfirmButton: false,
                timer: 1500
            });
        }
    </script>
@stop
