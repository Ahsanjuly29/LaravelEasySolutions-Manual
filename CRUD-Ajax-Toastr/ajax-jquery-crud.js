// Custom DataTable operation start here  
// selecting table, get url and data values... 
// this options are completely replacedable and usable dynamically many times if needed on each page
const $table = $('#dataTable');
const $tbody = $table.find('tbody');
const $pagination = $('#pagination-wrapper');
const baseUrl = $table.data('url');
const columns = $table.data('columns'); // serially showable columns on table like, ["name", "mobile", "address"]... will come as modified name from data


let currentPage = 1;
let search = '';

// get data from api server 
function fetchData(page = 1, searchQuery = '', highlightId = null) {
    currentPage = page;

    $('#loader').show();
    $.get(baseUrl, {
        page,
        search: searchQuery,
    }, response => {
        $('#loader').hide();

        if (!response.success) {
            $tbody.html(`<tr><td colspan="${columns.length + 2}">Error fetching data</td></tr>`);
            $pagination.empty();
            return;
        }

        // console.log(response);
        // console.log(response.data.data);
        // console.log(response.data);

        renderRows(response.data.data, highlightId, response.data.per_page);
        renderPagination(response.data);

        if (highlightId) {
            setTimeout(() => {
                $tbody.find(`tr.highlight-row`).removeClass('highlight-row').addClass('fade-out');
            }, 3000); // fades after 3 seconds
        }

    }).fail(() => {
        $('#loader').hide();
        $tbody.html(`<tr><td colspan="${columns.length + 2}">Error fetching data</td></tr>`);
        $pagination.empty();
    });
}

// decorate inside table by api response
function renderRows(rows, highlightId = null, perPage = 10) {

    $tbody.empty();
    if (!rows || rows.length === 0) {
        $tbody.append(`<tr><td colspan="${columns.length + 2}">No data found.</td></tr>`);
        return;
    }

    rows.forEach((item, index) => {
        const serial = (currentPage - 1) * perPage + index + 1;

        const highlightClass = (item.id == highlightId) ? 'highlight-row' : '';
        // console.log(`Row ID: ${item.id}, Highlight: ${highlightClass ? 'YES' : 'NO'}`);

        let rowHtml = `<tr class="${highlightClass}">`;

        // Serial number
        rowHtml += `<td>${serial}</td>`;

        // Checkbox
        rowHtml += `
                    <td class="text-center">
                        <div class="form-check">
                            <input class="form-check-input checkitem" type="checkbox" value="${item.id}" />
                        </div>
                    </td>`;

        // Data columns
        columns.forEach(col => {

            const rawValue = item[col] ?? '';
            const value = String(rawValue).trim();
            const lowerCol = col.toLowerCase();

            let cellContent = '';

            // 1. Empty/null/undefined
            if (!value) {
                cellContent = `<span class="text-muted">-</span>`;
            }
            // 2. HTML content from editors like TinyMCE/Summernote
            else if (/<\/?[a-z][\s\S]*>/i.test(value)) {
                const div = document.createElement('div');
                div.innerHTML = value;
                const textContent = div.textContent || div.innerText || '';

                if (textContent.length > 100) {
                    const shortText = textContent.slice(0, 100).trim() + '...';
                    cellContent =
                        `<span title="${textContent.replace(/"/g, '&quot;')}">${shortText}</span>`;
                } else {
                    cellContent = value; // Render full HTML
                }
            }
            // 3. Detect and render typed fields
            else if (lowerCol.includes('email')) {
                cellContent = `<a href="mailto:${value}">${value}</a>`;
            } else if (lowerCol.includes('tel') || lowerCol.includes('phone') || lowerCol.includes(
                'mobile')) {
                cellContent = `<a href="tel:${value}">${value}</a>`;
            } else if (lowerCol.includes('url') || lowerCol.includes('website')) {
                const safeUrl = value.startsWith('http') ? value : `http://${value}`;
                cellContent = `<a href="${safeUrl}" target="_blank" rel="noopener">${value}</a>`;
            } else if (lowerCol.includes('address') || lowerCol.includes('description') || lowerCol
                .includes('bio')) {
                if (value.length > 100) {
                    const shortText = value.slice(0, 100).trim() + '...';
                    cellContent =
                        `<span title="${value.replace(/"/g, '&quot;')}">${shortText}</span>`;
                } else {
                    cellContent = `<address>${value}</address>`;
                }
            }
            // 4. Default fallback with truncation
            else {
                cellContent = value.length > 100 ?
                    `<span title="${value.replace(/"/g, '&quot;')}">${value.slice(0, 100).trim()}...</span>` :
                    value;
            }

            rowHtml += `<td>${cellContent}</td>`;
        });

        // Action buttons
        rowHtml += `
                    <td>
                        <div class="d-flex">
                            <button class="btn btn-sm btn-outline-primary mr-1 edit-btn" data-url="${baseUrl}/${item.id}/edit">Edit</button>
                            <button class="btn btn-sm btn-outline-danger ml-1 delete-btn" data-url="${baseUrl}/${item.id}" data-id="${item.id}">Delete</button>
                        </div>
                    </td>`;

        rowHtml += `</tr>`;
        $tbody.append(rowHtml);
    });
}

// search data 
$('#searchInput').on('input', function () {
    search = $(this).val();
    fetchData(1, search);
});

function renderPagination(data) {

    $pagination.empty();
    const currentPage = data.current_page;
    const lastPage = data.last_page;

    // Helper to create a page button
    const pageButton = (page, label = null, disabled = false, active = false) => {
        if (label === '...') {
            return `<span class="mx-1">...</span>`;
        }

        return `
            <button 
                class="btn btn-sm btn-outline-secondary mx-1 page-btn ${active ? 'active' : ''}" 
                data-page="${page}" 
                ${disabled ? 'disabled' : ''}
                ${active ? 'disabled' : ''}>
                ${label ?? page}
            </button>
        `;
    };

    // Prev Button
    $pagination.append(pageButton(currentPage - 1, 'Prev', currentPage === 1));

    const pages = [];

    if (lastPage <= 10) {
        // Show all pages if total <= 10
        for (let i = 1; i <= lastPage; i++) {
            pages.push(i);
        }
    } else {
        // Always show first 5 pages
        for (let i = 1; i <= 4; i++) {
            pages.push(i);
        }

        // If currentPage > 6, show first ellipsis
        if (currentPage > 4) {
            pages.push('...');
        }

        // Show pages around current page (only if it's far from start and end)
        const middlePagesStart = Math.max(4, currentPage - 1);
        const middlePagesEnd = Math.min(lastPage - 4, currentPage + 1);

        for (let i = middlePagesStart; i <= middlePagesEnd; i++) {
            if (!pages.includes(i)) {
                pages.push(i);
            }
        }

        // If current page < lastPage - 4, show second ellipsis
        if (currentPage < lastPage - 4) {
            pages.push('...');
        }

        // Always show last 2 pages
        for (let i = lastPage - 1; i <= lastPage; i++) {
            if (i > 5 && !pages.includes(i)) {
                pages.push(i);
            }
        }
    }

    // Render pages
    pages.forEach(p => {
        if (p === '...') {
            $pagination.append(pageButton(null, '...'));
        } else {
            $pagination.append(pageButton(p, null, false, p === currentPage));
        }
    });

    // Next Button
    $pagination.append(pageButton(currentPage + 1, 'Next', currentPage === lastPage));

    // "Go to page" input
    $pagination.append(`
        <br><span class="mx-2">Go to page:</span>
        <input type="number" min="1" max="${lastPage}" id="gotoPageInput" class="form-control d-inline-block" style="width: 120px;" value="${currentPage}">
        <button class="btn btn-sm btn-outline-primary mx-1" id="gotoPageBtn">Go</button>
    `);
}

// pagination buttons
$(document).on('click', '.page-btn', function (e) {
    e.preventDefault();
    const selectedPage = parseInt($(this).data('page'));
    if (!isNaN(selectedPage)) {
        fetchData(selectedPage, search);
    }
});

// direct to page
$(document).on('click', '#gotoPageBtn', function () {
    const inputPage = parseInt($('#gotoPageInput').val());
    if (!isNaN(inputPage) && inputPage >= 1) {
        fetchData(inputPage, search);
    }
});

$(document).on('input', '#gotoPageInput', function () {
    $(this).val($(this).val().replace(/[^\d]/g, ''));
});

// Doanlodable options
// Export options like (PDF, EXCEL, WORD)
function exportToPDF() {
    const {
        jsPDF
    } = window.jspdf;
    const doc = new jsPDF();

    doc.text("Company Data", 14, 16);

    const rows = [];
    $('#dataTable tbody tr').each(function () {
        const row = [];
        $(this).find('td').each(function () {
            row.push($(this).text().trim());
        });
        rows.push(row);
    });

    doc.autoTable({
        head: [
            ['', 'Name', 'Mobile', 'Address', 'Action']
        ],
        body: rows,
        startY: 20
    });

    doc.save('company-data.pdf');
}

function exportToExcel() {
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.table_to_sheet(document.getElementById('dataTable'));
    XLSX.utils.book_append_sheet(wb, ws, "Company Data");
    XLSX.writeFile(wb, "company-data.xlsx");
}

function exportToWord() {
    const table = document.getElementById('dataTable').outerHTML;
    const blob = new Blob(['<html><body>' + table + '</body></html>'], {
        type: 'application/msword'
    });

    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'company-data.doc';
    a.click();
}

// Initial load DAta on table
$(document).ready(function () {
    fetchData();
});
// Custom DataTable(Read) operation ends here  


// Custom CRUD operation using Modal (Create, Edit, Update, Delete) operation starts here 
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
        fetchData(currentPage, search, data.data.id); // reload current page with highlight
        closeModal();
        toastr.success(data.message);
    },
    delete: () => {
        fetchData();
        // setTimeout(() => window.location.reload(), 400);
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
            if (param.successCallback) {
                param.successCallback(response);
            } else if (param.crud) {
                processSuccessResponse(response, param.crud);
            }
        }
    }).fail(($xhr) => {
        if (param.errorCallback) {
            param.errorCallback($xhr);
        } else {
            handleErrorResponse($xhr, tostrTimeOut);
        }
    });
};

// Modal open handler (Create/Edit)
const openModal = (response) => {
    const $modal = $(config.modalId);
    const $form = $(config.formId);
    const $title = $modal.find(config.titleSelector);

    initSelect2WithAjaxCall($modal);

    if (typeof response !== "string" && Object.keys(response).length > 0) {
        $title.text("Edit Form");

        //  $('#url').val(response.data.update_url || response.data.url || `${baseUrl}/${response.data.id}`);
        $(config.urlInputId).val(`${baseUrl}/${response.data.id}`);

        $.each(response.data, (key, value) => {
            const $field = $(`[name="${key}"]`);

            if (!$field.length) return;

            if ($field.is('select')) {
                if ($field.find(`option[value="${value}"]`).length) {
                    $field.val(value).trigger('change');
                } else {
                    const newOption = new Option(value, value, true, true);
                    $field.append(newOption).trigger('change');
                }
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


$(document).ready(function () {

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

    // single item boxes, each row on table
    $(document).on('change', config.checkItemClass, function () {
        if (!$(config.checkItemClass).length) return;

        const checked = $(config.checkItemClass + ":checked").length > 0;
        $(config.multipleDeleteBtnId).toggleClass('d-none', !checked);
        $(config.checkAllBoxId).prop('checked', checked);
    });

    // total table rows select bx 
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
});