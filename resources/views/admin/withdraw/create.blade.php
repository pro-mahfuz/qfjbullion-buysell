@extends('layouts.app')
@section('content_header')
    <h1>Withdraw List</h1>
@stop


@section('content')

    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-end">

            <ul class="nav navbar-right panel_toolbox">
                <a class="btn btn-danger" href="{{ route('admin.withdrawlist') }}"><i class="fa fa-dollar"></i>
                    Withdraw List </a>

        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-sm-12 ">
            <div class="x_panel">
                <div class="x_content">
                    <form action="{{ route('admin.supplier.withdraw.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <label for="user_type">Supplier Type <span>*</span></label>
                                <select name="user_type" id="user_type" class="form-control" required>
                                    <option value="">None</option>
                                    <option value="1" {{ old('user_type') == '1' ? 'selected' : '' }}>Client
                                    </option>
                                    <option value="0" {{ old('user_type') == '0' ? 'selected' : '' }}>Supplier
                                    </option>
                                </select>
                            </div>

                            <!-- Supplier Dropdown -->
                            <div class="col-md-6">
                                <div class="form-group" id="supplier_container">
                                    <label for="supplier_id">Search By Name<span>*</span></label>
                                    <select name="supplier_id" id="supplier_id" class="form-control" required>
                                        <option value="">None </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">

                                <div class="form-group" id="supplier_details" style="display:none">

                                </div>
                            </div>
                        </div>

                </div>

                </form>

            </div>
        </div>
    </div>
    </div>

@stop


@push('js')
    <script>
        $(document).ready(function() {
            const suppliers = @json($suppliers);
            var selectedType = '{{ old('user_type') }}'; // Get the old selected type from the server
            var oldSupplierId = '{{ old('supplier_id') }}'; // Get the old selected supplier ID from the server
            // Initially populate the type dropdown based on the old value if present
            if (selectedType) {
                $('#user_type').val(selectedType);
            }

            // Trigger the change event on the supplier_type dropdown to repopulate the suppliers
            $('#user_type').trigger('change');

            // Handle type dropdown change to populate the supplier dropdown
            $('#user_type').on('change', function() {
                var selectedType = $(this).val();

                // Clear and repopulate the supplier dropdown
                const supplierDropdown = $('#supplier_id');
                supplierDropdown.empty();
                supplierDropdown.append('<option value="">Select by Name</option>');

                if (selectedType) {
                    if (suppliers[selectedType]) {
                        suppliers[selectedType].forEach(function(supplier) {
                            supplierDropdown.append(
                                `<option value="${supplier.id}" ${oldSupplierId == supplier.id ? 'selected' : ''}>${supplier.full_name} (${supplier.mobile_number})</option>`
                            );
                        });
                    }
                }
            });

            // Initially trigger the 'change' event to populate the suppliers dropdown if needed
            if (selectedType) {
                $('#user_type').trigger('change');
            }

            // Handle the supplier change event to fetch additional details via AJAX
            $('#supplier_id').on('change', function() {
                var selectedValue = $(this).val();
                if (selectedValue) {
                    $.ajax({
                        url: '{{ route('admin.withdrawlistbysupplier') }}',
                        method: 'POST',
                        data: {
                            'supplier_id': selectedValue,
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $('#supplier_details').html(response).show();
                        },
                        error: function(xhr, status, error) {
                            alert('An error occurred: ' + xhr.responseText);
                            console.error('Error details:', status, error, xhr.responseText);
                        }
                    });
                } else {
                    $('#supplier_details').hide();
                }
            });

            // If an old supplier ID exists, select the appropriate supplier and show details
            if (oldSupplierId) {
                $('#supplier_id').val(oldSupplierId).trigger('change');
            }
        });
    </script>
@endpush
