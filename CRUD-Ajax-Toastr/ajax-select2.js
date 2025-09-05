// combination of select2 and ajax for dynamic result.
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
                ...commonOptions, // Use spread syntax here
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
            .replace(/\s+/g, '_') // collapse whitespace and replace with -
            .replace(/-+/g, '_'); // collapse multiple hyphens
        $('#slug').val(slug);
    });
});