$(document).ready(function () {

    // Configurable options (can be modified globally)
    const config = {
        formId: '#form',
        modalId: '#form-modal',
        titleSelector: '.modal-title',
        urlInputId: '#url',
        methodInputName: '_method',
        submitBtnId: '#formSubmitBtn',
        createBtnClass: '.create-task',
        editBtnClass: '.edit-task',
        deleteBtnClass: '.delete-btn',
        checkItemClass: '.checkitem',
        checkAllBoxId: '#check_all_box',
        multipleDeleteBtnId: '#multiple_delete_btn'
    };

    // Success callback handlers
    const handleCrudSuccess = {
        edit: (data) => {
            // Handle edit success if needed
        },
        formSubmit: (data) => {
            // Handle form submission success
        },
        delete: () => {
            setTimeout(() => window.location.reload(), 400);
        }
    };

    // Process AJAX success based on CRUD type
    const processSuccessResponse = (data, crudType) => {
        if (crudType in handleCrudSuccess) {
            handleCrudSuccess[crudType](data);
        }
    };

    // Handle AJAX error response
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
                response.tostrTimeOut = tostrTimeOut;
                processSuccessResponse(response, param.crud);
            }
        }).fail(($xhr) => handleErrorResponse($xhr, tostrTimeOut));
    };

    // Modal open handler (Create/Edit)
    const openModal = (response) => {
        const $modal = $(config.modalId);
        const $form = $(config.formId);
        const $title = $modal.find(config.titleSelector);

        if (typeof response !== "string" && Object.keys(response).length > 0) {
            $title.text("Edit Form");
            $.each(response.data, (key, value) => {
                const $field = $(`#${key}`);
                if ($field.is('select')) {
                    $field.val(value).change();
                } else {
                    $field.val(value);
                }
            });
            if (!$form.find(`input[name="${config.methodInputName}"]`).length) {
                $form.append(`<input type="hidden" name="${config.methodInputName}" value="PUT">`);
            }
        } else {
            $(config.urlInputId).val(response);
            $title.text("Create Form");
        }

        $modal.modal('show');
    };

    // Modal close handler (reset form)
    const closeModal = () => {
        const $form = $(config.formId);
        const $modal = $(config.modalId);
        const $title = $modal.find(config.titleSelector);

        $title.text("Create Form");
        $form.trigger('reset');
        $(config.urlInputId).val('');
        $form.find(`input[name="${config.methodInputName}"]`).remove();
    };

    // Event bindings
    $(document).on('click', config.createBtnClass, function () {
        const url = $(this).attr('data-url');
        openModal(url);
    });

    $(document).on('click', config.editBtnClass, function () {
        ajaxCall({
            type: 'GET',
            url: $(this).data('url'),
            dataType: 'JSON',
            crud: 'edit'
        });
    });

    $(document).on('click', config.submitBtnId, function (e) {
        e.preventDefault();
        ajaxCall({
            type: $(`${config.formId} input[name="${config.methodInputName}"]`).val() || $(config.formId).attr('method'),
            url: $(config.urlInputId).val(),
            dataType: 'JSON',
            data: $(config.formId).serialize(),
            crud: 'formSubmit'
        });
    });

    $(document).on('click', config.deleteBtnClass, function () {
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

    $(config.checkItemClass).change(function () {
        const checked = $(config.checkItemClass + ":checked").length > 0;
        $(config.multipleDeleteBtnId).toggleClass('d-none', !checked);
        $(config.checkAllBoxId).prop('checked', checked);
    });

    $(config.checkAllBoxId).click(function () {
        const isChecked = $(this).is(':checked');
        $(config.checkItemClass).prop('checked', isChecked);
        $(config.multipleDeleteBtnId).toggleClass('d-none', !isChecked);
    });

    $(config.multipleDeleteBtnId).on('click', function () {
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

    $(document).on('hidden.bs.modal', config.modalId, closeModal);
});
