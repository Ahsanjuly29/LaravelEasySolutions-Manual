$(document).ready(function () {

    // Success callback functions for CRUD operations
    const handleCrudSuccess = {
        edit: (data) => {
            // Actions after successful edit
        },
        formSubmit: (data) => {
            // Actions after successful form submission
        },
        delete: () => {
            setTimeout(() => window.location.reload(), 400); // Refresh after deletion
        }
    };

    // Function to handle AJAX response success based on CRUD type
    const processSuccessResponse = (data, crudType) => {
        if (crudType in handleCrudSuccess) {
            handleCrudSuccess[crudType](data); // Call respective success handler
        }
    };

    // Function to handle AJAX response errors
    const handleErrorResponse = ($xhr, timeout) => {
        const errorData = $xhr.responseJSON;
        if (typeof errorData.message === "string") {
            toastr.error('', errorData.message, { timeOut: timeout });
        } else {
            $.each(errorData.message, (key, message) => {
                toastr.error('', message, { timeOut: timeout });
            });
        }
    };

    // Centralized AJAX function
    const ajaxCall = (param) => {
        const tostrTimeOut = 3000;

        $.ajax({
            headers: {
                'Authorization': `Bearer ${$('meta[name="bearer-token"]').attr('content')}`,
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: param.type,
            url: param.url,
            dataType: param.dataType,
            data: param.data,
            success: (response) => {
                response.tostrTimeOut = tostrTimeOut; // Set timeout
                processSuccessResponse(response, param.crud);
            }
        })
            .fail(($xhr) => handleErrorResponse($xhr, tostrTimeOut));
    };

    // Modal Handling Functions
    const closeModal = () => {
        $('.modal-title').html("Create Form");
        $('#form').trigger('reset');
        $('#url').val('');
        $('input[name="_method"]').remove();
        $("#form-modal").modal('hide');
    };

    const openModal = (response) => {
        if (typeof response !== "string" && Object.keys(response).length > 0) {
            $('.modal-title').html("Edit Form");
            $.each(response.data, (key, value) => {
                $(`#${key}`).val(value).change();
            });
            $('#form').append('<input type="hidden" name="_method" value="PUT">');
        } else {
            $('#url').val(response);
        }
        $("#form-modal").modal('show');
    };

    // Event Bindings
    $(document).on('click', '.create-task', function () {
        openModal($(this).attr('data-url'));
    });

    $(document).on('click', '.edit-task', function () {
        ajaxCall({
            type: 'GET',
            url: $(this).data('url'),
            dataType: 'JSON',
            crud: 'edit'
        });
    });

    $(document).on('click', '#formSubmitBtn', function (event) {
        event.preventDefault();
        ajaxCall({
            type: $('input[name="_method"]').val() || $('#form').attr('method'),
            url: $('#url').val(),
            dataType: 'JSON',
            data: $('#form').serialize(),
            crud: 'formSubmit'
        });
    });

    $(document).on('click', '.delete-btn', function () {
        if (confirm('Are you sure you want to delete this task?')) {
            ajaxCall({
                type: 'DELETE',
                url: $(this).data('url'),
                dataType: 'JSON',
                data: { ids: $(this).data('id') },
                crud: 'delete'
            });
            $(this).closest('tr').remove();
        }
    });

    $(".checkitem").change(function () {
        $('#multiple_delete_btn').toggleClass('d-none', $(".checkitem:checked").length < 1);
        $('#check_all_box').prop('checked', $(".checkitem:checked").length > 0);
    });

    $('#check_all_box').click(function () {
        const isChecked = $(this).is(':checked');
        $('.checkitem').prop('checked', isChecked);
        $('#multiple_delete_btn').toggleClass('d-none', !isChecked);
    });

    $('#multiple_delete_btn').on('click', function () {
        const selectedIds = $("input:checkbox[name=id]:checked").map(function () {
            return $(this).val();
        }).get();

        if (confirm('Are you sure you want to delete these tasks?')) {
            ajaxCall({
                type: 'DELETE',
                url: $(this).data('url'),
                dataType: 'JSON',
                data: { ids: selectedIds },
                crud: 'delete'
            });
        }
    });

    $(document).on('hidden.bs.modal', '#form-modal', closeModal);

});
