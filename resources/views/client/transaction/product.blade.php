<div class="modal fade" id="dynamicModal12" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Select Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table id="product-table" class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Image</th>
                            <th>Weight</th>
                            <th>Select</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $index => $product)
                            <tr data-product="{{ json_encode($product) }}">
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $product['title'] }}</td>
                                <td>{{ $product['description'] }}</td>

                                <td>
                                    <img src="{{ $product['image'] }}" alt="{{ $product['title'] }}"
                                        style="height: 50px; transition: transform 0.3s ease-in-out;"
                                        onmouseover="this.style.transform='scale(4)'; this.style.zIndex='9999'; this.style.position='absolute'; this.style.background='white'; this.style.boxShadow='0px 4px 8px rgba(0,0,0,0.2)';"
                                        onmouseout="this.style.transform='scale(1)'; this.style.zIndex='1'; this.style.position='relative';">
                                </td>


                                <td>{{ $product['weight'] }} {{ $product['weight_type'] }}</td>
                                <td><input type="radio" class="product-select" name="product" /></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Initialize DataTable if not already initialized
        if (!$.fn.DataTable.isDataTable('#product-table')) {
            $('#product-table').DataTable({
                "pageLength": 5
            });
        }

        // When the modal is opened
        $('body').on('show.bs.modal', '#dynamicModal12', function() {
            $('#selected_product').val(''); // Clear product name
            $('#qty').prop('disabled', true).val(''); // Disable and clear qty input
            $('#gold_value').val('');
        });

        // When a product is selected
        $('body').on('change', '.product-select', function() {
            let selectedRow = $(this).closest('tr');
            let product = JSON.parse(selectedRow.attr('data-product'));

            // Update form fields
            $('#selected_product').val(product['title']).data('product', product);
            $('#selected_product_id').val(product['id']);
            $('#qty').prop('disabled', false).val('');
            $('#gold_value').val(product['description']);
        });
    });
</script>
