// combination of select2 and ajax for dynamic result.
const initSelect2WithAjaxCall = ($context = $(document)) => {
    $context.find('.select2-ajax').each(function () {
        const $select = $(this);
        const url = $select.data('url');
        const placeholder = $select.data('placeholder') || 'Select';
        const minimumInputLength = $select.data('minlength') || 0;

        const columns = $select.data('columns') || { id: 'id', text: 'name' };
        const columnsToFetch = Object.values(columns);

        const commonOptions = {
            theme: 'bootstrap4',
            placeholder: placeholder,
            allowClear: true,
            dropdownParent: $select.closest('.modal')[0] || document.body,
            minimumInputLength,
        };

        if (typeof url === "string" && url.trim().length > 0) {
            $select.select2({
                ...commonOptions,
                ajax: {
                    transport: function (params, success, failure) {
                        ajaxCall({
                            url,
                            type: 'GET',
                            dataType: 'JSON',
                            data: {
                                // ...params.data,
                                search: params.data.term || '',
                                page: params.data.page || 1,
                                columns: columnsToFetch
                            },
                            successCallback: success,
                            errorCallback: failure
                        });
                    },
                    processResults: data => ({
                        results: (data.data?.data || []).map(item => ({
                            id: item[columns.id],
                            text: item[columns.text],
                        })),
                        pagination: { more: data.data?.current_page < data.data?.last_page },
                    }),
                    delay: 250,
                    cache: true
                }
            });
        } else {
            $select.select2({
                ...commonOptions // Spread static options
            });
        }
    });
};


$(document).ready(function () {

    initSelect2WithAjaxCall();

    // on key up process
    $('#name').on('keyup', function () {
        var name = $(this).val();
        var slug = name
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9\s-]/g, '') // remove invalid chars
            .replace(/\s+/g, '_')         // replace spaces with underscore
            .replace(/_+/g, '_');         // collapse multiple underscores
        $('#slug').val(slug);
    });
});