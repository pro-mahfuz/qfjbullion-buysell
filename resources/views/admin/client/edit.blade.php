<form action="{{ route('supplier.edit') }}" method="POST">
    @csrf
    <input type="hidden" name="id" class="form-control"  value="{{ $supplier->id }}">
    <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Supplier Edit  </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">Full Name <span>*</span></label>
                    <input type="text" name="full_name" class="form-control" required=""  value="{{ $supplier->full_name }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">Mobile Number </label>
                    <input type="text" name="mobile_number" class="form-control" value="{{ $supplier->mobile_number }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">Address</label>
                    <input type="text" name="address" class="form-control" value="{{ $supplier->address }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ $supplier->email }}">
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">TRN No</label>
                    <input type="text" name="trn_no"  class="form-control" value="{{ $supplier->trn_no }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">Narration</label>
                    <textarea name="narration"class="form-control"> {{ $supplier->narration }} </textarea>
                </div>
            </div>
            
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success">Submit </button>
    </div>
</form>