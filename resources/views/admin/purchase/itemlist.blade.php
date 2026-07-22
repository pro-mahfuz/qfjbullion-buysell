 @if (count($PurchaseItems) > 0)
     <?php $sl = 0;
     $total = 0; ?>
     @foreach ($PurchaseItems as $row)
         <?php $sl++; ?>
         <tr>
             <td>
                 <button class="btn btn-danger btn-sm removeItem" data-id="{{ $row->id }}"><i
                         class="fa fa-trash"></i></button>

             </td>
             <td>{{ $row->product->title }}
                 <input type="hidden" name="items[{{ $sl }}][product_id]" value="{{ $row->product_id }}">
                 <input type="hidden" name="items[{{ $sl }}][product_name]" value="{{ $row->product->title }}">
             </td>
             <td>
                 <div class="form-group">
                     <input type="text" name="items[{{ $sl }}][quantity]"
                         class="form-control quantity_calculation" data-id="{{ $row->id }}"
                         data-purity="{{ $row->product->purity }}">
                 </div>
             </td>
             <td>
                 <div class="form-group">
                     <input type="text" name="items[{{ $sl }}][pure_quantity]" class="form-control"
                         id="pure_quantity_{{ $row->id }}" readonly>
                 </div>
             </td>
             <td>
                 <div class="form-group">
                     <input type="text" name="items[{{ $sl }}][unfix_rate_oz]"
                         class="form-control unfix_calculation" id="unfix_calculation_{{ $row->id }}"
                         data-id="{{ $row->id }}">
                 </div>
             </td>
             <td>
                 <div class="form-group">
                     <input type="text" name="items[{{ $sl }}][discount_usd]"
                         id="discount_usd_{{ $row->id }}" class="form-control discount_usd"
                         data-id="{{ $row->id }}">
                 </div>
             </td>
             <td>
                 <div class="form-group">
                     <input type="text" name="items[{{ $sl }}][discount_aed]"
                         id="discount_aed_{{ $row->id }}" class="form-control discount_aed"
                         data-id="{{ $row->id }}" readonly>
                 </div>
             </td>
             <td>
                 <div class="form-group">
                     <input type="text" name="items[{{ $sl }}][unfix_rate_gram]" class="form-control"
                         id="unfix_oz_{{ $row->id }}" readonly>
                 </div>
             </td>
             <td>
                 <div class="form-group">
                     <input type="text" name="items[{{ $sl }}][unfix_subtotal]"
                         class="form-control unfix_subtotal" id="unfix_subtotal_{{ $row->id }}" readonly>
                 </div>
             </td>
         </tr>
     @endforeach
 @endif

 <script>
     function calculateSum() {
         let sum = 0;

         const elements = document.querySelectorAll('.unfix_subtotal');

         elements.forEach(function(element) {
             let value = parseFloat(element.textContent.trim() || element.value.trim());
             if (!isNaN(value)) {
                 sum += value;
             }
         });

         $('#unfix_total').val(sum.toFixed(2));
     }


     function calculateUnfix(id) {
         var unfixValue = parseFloat($("#unfix_calculation_" + id).val()) || 0;
         var pureQuantity = parseFloat($("#pure_quantity_" + id).val()) || 0;
         var discount_usd = parseFloat($("#discount_usd_" + id).val()) || 0;

         var ozValue = (((unfixValue - discount_usd) * 3.674) / 31.1035).toFixed(2);
         var subtotalValue = (ozValue * pureQuantity).toFixed(2);

         $("#discount_aed_" + id).val(((pureQuantity / 31.1035) * discount_usd * 3.674).toFixed(2));
         $("#unfix_oz_" + id).val(ozValue);
         $("#unfix_subtotal_" + id).val(subtotalValue);

         calculateSum();
     }


     $(document).ready(function() {

         $('.removeItem').click(function(e) {
             e.preventDefault();


             var id = $(this).attr("data-id");

             //  alert(id);

             $.ajax({
                 type: 'POST',
                 url: '{{ route('admin.purchase.removeitem') }}',
                 data: {
                     id: id,
                     _token: '{{ csrf_token() }}' // Including the CSRF token
                 },
                 dataType: 'text',
                 success: function(data) {
                     try {
                         // Append the received data to the tbody
                         $('#tbody').html(data);
                         $("#products").val('');

                         // Re-enable the dropdown after successful submission
                         $('#products').prop('disabled', false);
                     } catch (e) {
                         console.error('Error parsing JSON data: ', e);

                         // Re-enable the dropdown if there was an error
                         $('#products').prop('disabled', false);
                     }
                 },
                 error: function(xhr, status, error) {
                     console.error('AJAX Error: ', status, error);

                     // Re-enable the dropdown if there was an error
                     $('#products').prop('disabled', false);
                 }
             });
         });

         $('#diposit_quantity').on('input', function() {
             $("#unfix_quantity").val($('#diposit_quantity').val());

         });

         $('.quantity_calculation').on('input', function() {
             $("#pure_quantity_" + $(this).attr("data-id")).val($(this).val() * $(this).attr(
                 "data-purity"));
         });


         $('.unfix_calculation').on('input', function() {
             var id = $(this).attr("data-id");
             calculateUnfix(id);
         });

         $('.discount_usd').on('input', function() {
             var id = $(this).attr("data-id");
             calculateUnfix(id);
         });



     });
 </script>
