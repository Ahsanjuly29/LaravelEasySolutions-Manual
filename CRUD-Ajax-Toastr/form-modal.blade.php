<div class="modal fade" id="form-modal">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="card-body">
                    <form id="form" method="POST">
                        <div class="row">
                            <div class="col-sm-6 mt-1 ">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" placeholder="Enter name" id="name"
                                    name="name">
                            </div>
                            <div class="col-sm-6 mt-1 ">
                                <label for="mobile">Mobile</label>
                                <input type="text" class="form-control" placeholder="Enter mobile" id="mobile"
                                    name="mobile">
                            </div>
                            <div class="col-sm-12 mt-1 ">
                                <label class="d-block" for="address">Address</label>
                                <textarea name="address" id="address" class="w-100" style="resize: auto;"></textarea>
                            </div>

                            <div class="col-sm-12 my-4">
                                <!-- Add hidden url input for create/Update form -->
                                <input type="hidden" id="url" value="">

                                <div class="text-right">
                                    <button type="button" class="btn btn-outline-danger"
                                        data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-outline-primary" id="formSubmitBtn">Save
                                        changes</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="modal-footer justify-content-between">
            </div>

        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

{{-- example of select with dynamic ajax and also static options --}}
{{-- <div class="col-sm-6 mt-1 ">
    <label>Type</label>
    <select class="form-control select2-ajax" name="type" data-url="" data-placeholder="Select Type">
        <option value="">Select Type</option>
        <option value="individual">individual</option>
        <option value="company">company</option>
        <option value="stock">stock</option>
        <option value="crypto">crypto</option>
        <option value="real_estate">real_estate</option>
        <option value="deal">deal</option>
    </select>
</div>
<div class="col-sm-6 mt-1 ">
    <label>Company</label>
    <select class="form-control select2-ajax" name="company" data-url="{{ route('api-company-data.index') }}"
        data-columns='{"id":"id", "text":"name"}' data-placeholder="Select a Company">
        <option value="">Select company</option>
    </select>
</div> --}}
