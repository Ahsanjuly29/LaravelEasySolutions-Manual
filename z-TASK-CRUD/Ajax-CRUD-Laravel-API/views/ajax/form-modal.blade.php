    <!-- The Modal -->
    <div class="modal" id="form-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Create Form</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <div class="card-body">
                        <form id="form" method="POST">
                            <div class="row">
                                <div class="col-sm-4 mt-1 ">
                                    <input type="text" class="form-control" placeholder="Enter name" id="name"
                                        name="name">
                                </div>
                                <div class="col-sm-4 mt-1 ">
                                    <input type="text" class="form-control" id="description"
                                        placeholder="Enter description" name="description">
                                </div>
                                <div class="col-sm-4 mt-1 ">
                                    <input type="date" class="form-control" placeholder="Enter due_date"
                                        id="due_date" name="due_date">
                                </div>
                                <div class="col-sm-4 mt-1 ">
                                    <select class="form-select" name="status" id="status">
                                        <option value="PENDING">PENDING</option>
                                        <option value="IN_PROGRESS">IN PROGRESS</option>
                                        <option value="COMPLETED">COMPLETED</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Add hidden url input for create/Update form -->
                            <input type="hidden" id="url" value="">
                        </form>
                    </div>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button class="btn btn-outline-primary" id="formSubmitBtn">Submit</button>
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal"
                        id="formReset">Close</button>
                </div>
            </div>
        </div>
    </div>
