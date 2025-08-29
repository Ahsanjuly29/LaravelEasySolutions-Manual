const initSelect2WithAjaxCall = ($context = $(document)) => {
    $context.find('.select2-ajax').each(function () {
        const $select = $(this);
        const url = $select.data('url');
        const placeholder = $select.data('placeholder') || 'Select';
        const minimumInputLength = $select.data('minlength') || 1;
        const idField = $select.data('id-field') || 'id';
        const textField = $select.data('text-field') || 'name';

        const commonOptions = {
            theme: 'bootstrap4',
            placeholder: placeholder,
            allowClear: true
        };

        if (url) {
            $select.select2({
                ...commonOptions, // ✅ Use spread syntax here
                minimumInputLength: minimumInputLength,
                ajax: {
                    transport: function (params, success, failure) {
                        ajaxCall({
                            type: 'GET',
                            url: url,
                            dataType: 'JSON',
                            data: params.data,
                            successCallback: success,
                            errorCallback: failure
                        });
                    },
                    processResults: function (data) {
                        return {
                            results: (data.items || []).map(item => ({
                                id: item[idField],
                                text: item[textField]
                            }))
                        };
                    },
                    delay: 250,
                    cache: true
                }
            });
        } else {
            $select.select2({
                ...commonOptions // ✅ Spread static options
            });
        }
    });
};


$(document).ready(function () {

    initSelect2WithAjaxCall();

    // Configurable options (can be modified globally)
    const config = {
        formId: '#form',
        modalId: '#form-modal',
        titleSelector: '.modal-title',
        urlInputId: '#url',
        methodInputName: '_method',
        submitBtnId: '#formSubmitBtn',
        createBtnClass: '.create-btn',
        editBtnClass: '.edit-btn',
        deleteBtnClass: '.delete-btn',
        checkItemClass: '.checkitem',
        checkAllBoxId: '#check_all_box',
        multipleDeleteBtnId: '#multiple_delete_btn'
    };

    // Success callback handlers
    const handleCrudSuccess = {
        edit: (data) => {
            openModal(data);
        },
        formSubmit: (data) => {
            // Handle form submission success
            closeModal();
            toastr.success(data.message);
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
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
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

        $title.text("Add ");
        $form.trigger('reset');
        $(config.urlInputId).val('');
        $form.find(`input[name="${config.methodInputName}"]`).remove();

        $modal.modal('hide');
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
    // end ajax crud process

    // on key up process
    $('#name').on('keyup', function () {
        var name = $(this).val();
        var slug = name
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9\s-]/g, '') // remove invalid chars
            .replace(/\s+/g, '_') // collapse whitespace and replace with -
            .replace(/-+/g, '_'); // collapse multiple hyphens
        $('#slug').val(slug);
    });
});