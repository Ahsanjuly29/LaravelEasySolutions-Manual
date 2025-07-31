# 🗑️ How to Delete Multiple Users/Rows from Table using GUI

*This guide walks you through the process of implementing a feature to delete multiple users or rows from a table using a graphical user interface (GUI). We’ll utilize Ajax for seamless data management without refreshing the page.*

## 🚀 Using Ajax to Delete Multiple Rows from Table

*Ajax allows you to send and receive data asynchronously, meaning you can perform actions like deleting data without the need to reload your page. Here’s how to set it up.*

### Step 1: 📋 Add Checkbox to Table Header

*Begin by inserting a checkbox in the table header. This checkbox will allow users to select or deselect all rows at once.*

```html
<th class="text-center">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" value="" id="check_all_box" />
    </div>
</th>
```

*We use `id="check_all_box"` so that when checked, all rows will be selected, and a delete button will appear!*

### Step 2: ✅ Implement Checkbox in Each Row

*Next, you’ll want to add a checkbox to each row in your table. This enables individual selection for deletion.*

```html
<td class="text-center">
    <div class="form-check">
        <input class="form-check-input checkitem" type="checkbox" value="{{ $item->id }}" name="id" />
    </div>
</td>
```

*This setup makes it simple to delete either a single row or multiple rows based on user selection.*

### Step 3: 🥳 Add Delete Button for Single Deletion

*Now, let’s create a delete button for each row that allows users to delete entries individually.*

```html
<td>
    <div class="d-flex">
        <button class="btn btn-sm btn-outline-danger ms-1 delete-btn" data-url="{{ route('url.to.destroy', $item->id) }}" data-id="{{ $item->id }}">
            <i class="material-icons">delete</i> Delete
        </button>
    </div>
</td>
```

*The `class="delete-btn"` triggers the deletion process via Ajax, enabling single or multiple data deletions effectively.*

### Step 4: 🏗️ Create Space for Multiple Delete Button

*Add a dedicated space in your table for a button that allows for the deletion of all selected entries at once. This enhances user experience by providing a clear action for bulk deletions.*

```html
<tr>
    <td colspan="13">
        <button id="multiple_delete_btn" class="btn btn-xs btn-outline-danger mr-2 d-none" type="submit" data-url="{{ route('api-item.destroy', 1) }}">
            <i class="material-icons">delete_sweep</i> Delete All
        </button>
    </td>
</tr>
```

*This button, `id="multiple_delete_btn"`, will initiate the process to delete multiple entries at once when clicked.*

---

## 📜 Delete.js

### Step 5: 🖥️ Integrate the JS File

*The following JavaScript will handle the deletion functionality. It manages both single and multiple deletions, ensuring a smooth user experience.*

**Single Row Deletion:**
>This script enables the deletion of a single row when the corresponding delete button is clicked, utilizing Ajax for a seamless experience.

```js
$(document).on('click', '.delete-btn', function () {
    var id = $(this).data('id'); // Get the task ID
    var url = $(this).data('url'); // Get the delete URL
    if (confirm('Are you sure you want to delete this task?')) { // Confirm deletion
        var param = {
            type: 'DELETE',
            url: url,
            dataType: 'JSON',
            data: {
                ids: id
            }
        }
        ajaxCall(param); // Submit form using Ajax
        $(this).closest('tr').remove(); // Remove the corresponding row from the table
    }
});
```

**Visibility of the Multiple Delete Button:**
>This section manages the appearance of the multiple delete button. It shows up when any checkbox is checked and hides when all checkboxes are unchecked:

```js
$(".checkitem").change(function () {
    if (this.checked) {
        $('#multiple_delete_btn').removeClass('d-none'); // Show the multiple delete button
    } else if ($(".table input[name='id']:checked").length < 1) {
        $('#multiple_delete_btn').addClass('d-none'); // Hide the button if no checkboxes are checked
        $('#check_all_box').prop('checked', false); // Uncheck the header checkbox
    }
});
```

### Step 6: 🔥 Handling Multiple Deletion

*When using the multiple delete button, this function collects all selected checkbox IDs and sends them via Ajax to delete those entries at once.*

```js
$('#multiple_delete_btn').on('click', function (e) {
    var url = $(this).data('url'); // Get the delete URL
    let selectedIds = []; // Initialize an empty array for selected IDs
    $("input:checkbox[name=id]:checked").each(function () {
        selectedIds.push($(this).val()); // Push the selected IDs into the array
    });

    if (confirm('Are you sure you want to delete these tasks?')) { // Confirm deletion
        var param = {
            type: 'DELETE',
            url: url,
            dataType: 'JSON',
            data: {
                ids: selectedIds // Include selected IDs in the request
            },
            crud: 'delete'
        }

        ajaxCall(param); // Send the request via Ajax
    }
});
```

### Step 7: 📞 Dynamic Ajax Call

*This function standardizes your Ajax calls, making it easier to manage notifications for both success and error messages.*

```css
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" />
```

```js
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
```

```js
ajaxCall = function (param) {
    var timeOut = 3000; // Set duration for notifications

    $.ajax({
        headers: {
            'Authorization': 'Bearer ' + $('meta[name="bearer-token"]').attr('content'), // Add Laravel bearer token
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Include Laravel CSRF token
        },
        type: param.type,
        url: param.url,
        dataType: param.dataType,
        data: param.data,

        success: function (response) {
            response.tostrTimeOut = timeOut; // Set notification duration
            success(response); // Call the success callback function
        }
    }).done(function (data, textStatus, jqXHR) {
    }).fail(function ($xhr) {
        var errorData = $xhr.responseJSON; // Retrieve the JSON response
        if (typeof errorData.message == "string") {
            toastr.error('', errorData.message, { timeOut: timeOut }); // Display error message
        } else {
            $.each(errorData.message, function (objKey, objValue) { // Handle multiple error messages
                toastr.error('', objValue, { timeOut: timeOut }); // Show each error message
            });
        }
    });
}
```

### Step 8: 🔒 Controller Delete Method

*Finally, implement the delete method in your controller. This method will handle the request to remove tasks from storage.*

```php
/**
 * Remove the Task from storage.
 */
public function destroy(Request $request)
{
    try {
        Task::whereIn('id', explode(',', $request->ids))->delete(); // Delete tasks from storage
        return response()->json([
            'success' => 1,
            'message' => "This Task has been successfully deleted." // Success message
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 0,
            'message' => $e->getMessage() . '. Line: ' . $e->getLine() . '. File: ' . $e->getFile(), // Detailed error message
            'data' => [],
        ], $e->getCode());
    }
}
```