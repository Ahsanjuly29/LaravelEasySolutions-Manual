$(document).ready(function () {


    editSuccess = function (data) {

    }

    formSuccess = function (data) {

    }

    deleteSuccess = function () {
        setTimeout(() => {
            window.location.reload();
        }, 400); // 100ms delay
    }

    // Dynamic Ajax Call
    ajaxCall = function (param) {

        // Pre defining values
        var method = param.type;
        var url = param.url;
        var dataType = param.dataType;
        var data = param.data;
        var tostrTimeOut = 3000;

        // Call Ajax Function
        $.ajax({
            headers: {
                'Authorization': 'Bearer ' + $('meta[name="bearer-token"]').attr('content'), // Laravel bearer token,
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Laravel CSRF token
            },
            type: method,
            url: url,
            dataType: dataType,
            data: data,
            success: function (response) {
                response.tostrTimeOut = tostrTimeOut; // adding time(sec) to response
                success(response); // Call success callback
            }
        }).done(function (data, textStatus, jqXHR) {
            // Process data, After received in data parameter
            if (param.crud == 'edit') {
                editSuccess(data);
            }
            else if (param.crud == 'formSubmit') {
                formSuccess(data);
            }
            else if (param.crud == 'delete') {
                deleteSuccess(data);
            }
            else { }
        }).fail(function ($xhr) {

            var errorData = $xhr.responseJSON; // Get Actual Json Response
            if (typeof errorData.message == "string") {
                toastr.error('', errorData.message, { timeOut: tostrTimeOut }); // toastr Error Messages
                // $('.error-message').append(`<span class="alert alert-danger mt-1 p-1">` + errorData.message + `</span>`); // Appending Error Messages
            }
            else {
                $.each(errorData.message, function (objKey, objValue) { // Finding Each Data
                    toastr.error('', objValue, { timeOut: tostrTimeOut }); // toastr Error Messages
                    // $('.' + objKey).append(`<p class="alert alert-danger mt-1 p-1">` + objValue + `</p>`); // Appending Valiadtion Messages For Each Input
                });
            }
            $('.alert').fadeOut(param.time); // Fading Away Error Message after time(Sec)
        });
    }

    // Close Modal
    closeModal = function () {
        // $('#form')[0].reset();
        $('.modal-title').html("Create Form"); // Replace Value
        $('#form').trigger('reset'); // Form Reset to empty
        $('#url').val(''); // url set empty
        $('input[name="_method"]').remove(); // remove input tag
        $("#form-modal").modal('hide'); // Hide Modal
    }

    // Close Modal on Click
    $(document).on('hidden.bs.modal', '#form-modal', function () {
        closeModal();
    });

    // Open Modal For Create or Edit data
    openModal = function (response) {

        // As, it is a single form using for both type file Submit Event.
        // so response.length defines if it has data object, then it's update form otherwise Create form
        if (typeof (response) !== "string" && Object.keys(response).length > 0) {
            $('.modal-title').html("Edit Form"); // Rename Modal Title
            let data = response.data;
            $.each(data, function (objKey, objValue) { // Finding/Assigning Each Data on Each Input
                $('#' + objKey).find('option[value="${objValue}"]').attr('selected', 'selected').change();
                $('#' + objKey).val(objValue); // Appending data for Each Input
            });

            $('#form').append('<input type="hidden" name="_method" value="PUT">'); // Needed Put method to Update Form
        }
        else {
            $('#url').val(response);
        }

        $("#form-modal").modal('show'); // Show Modal Form
    }

    // Show Modal On Click
    $(document).on('click', '.create-task', function () {
        url = $(this).attr('data-url');
        openModal(url);
    });

    // Edit JS
    $(document).ready(function () {
        // Edit/Show Data, Using Ajax
        $(document).on('click', '.edit-task', function () {
            var url = $(this).data('url'); // Get the delete URL
            var param = {
                type: 'GET',
                url: url,
                dataType: 'JSON',
            }

            ajaxCall(param); // Submit form Using Ajax
        });

        // Form Submit for Create/Update Using Ajax
        $(document).on('click', '#formSubmitBtn', function (event) {
            event.preventDefault();

            var url = $('#url').val(); // Update/Create URL
            var method = $('input[name="_method"]').val(); // "POST" Method Create Form
            if (!method) {
                method = $('#form').attr('method'); // "PUT" Method Update Form
            }
            var param = {
                type: method,
                url: url,
                dataType: 'JSON',
                data: $('#form').serialize(),
                crud: 'formSubmit'
            }

            ajaxCall(param); // Submit form Using Ajax
        });
    });

    /*
    // Single delete data from table
    // Form Submit for Delete Using Ajax
    */
    $(document).on('click', '.delete-btn', function () {
        var id = $(this).data('id'); // Get the task ID
        var url = $(this).data('url'); // Get the delete URL
        if (confirm('Are you sure you want to delete this task?')) {
            var param = {
                type: 'DELETE',
                url: url,
                dataType: 'JSON',
                data: {
                    ids: id
                }
            }
            ajaxCall(param); // Submit form Using Ajax
            $(this).closest('tr').remove();
        }
    });

    /*
    //  open multiple delete button
    */
    $(".checkitem").change(function () {
        if (this.checked) {
            $('#multiple_delete_btn').removeClass('d-none');
        }
        else if ($(".table input[name='id']:checked").length < 1) {
            $('#multiple_delete_btn').addClass('d-none');
            $('#check_all_box').prop('checked', false);
        }
    });

    // Check all boxes
    $('#check_all_box').click(function (event) {
        if (this.checked) {
            $('.checkitem').each(function () {
                this.checked = true;
                $('#multiple_delete_btn').removeClass('d-none');
            });
        } else {
            $('.checkitem').each(function () {
                this.checked = false;
                $('#multiple_delete_btn').addClass('d-none');
            });
        }
    });

    $('#multiple_delete_btn').on('click', function (e) {
        var url = $(this).data('url'); // Get the delete URL
        let selctedIds = [];
        $("input:checkbox[name=id]:checked").each(function () {
            selctedIds.push($(this).val());
        });

        if (confirm('Are you sure you want to delete this task?')) {
            var param = {
                type: 'DELETE',
                url: url,
                dataType: 'JSON',
                data: {
                    ids: selctedIds
                },
                crud: 'delete'
            }

            ajaxCall(param); // Submit form Using Ajax
            // deleteSwalAlert(selctedIds); // Calling Custom created Function
        }
    });
});
