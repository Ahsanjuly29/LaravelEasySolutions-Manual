
### **Database Transactions in Laravel: `DB::beginTransaction()`**

#### **Purpose**

`DB::beginTransaction()` is used to start a database transaction. It ensures that a set of database operations either all succeed or all fail — preserving data integrity.

---

### **Basic Usage**

```php
use Illuminate\Support\Facades\DB;

DB::beginTransaction();

try {
    // Perform multiple database operations
    DB::table('users')->update([...]);
    DB::table('orders')->insert([...]);

    DB::commit(); // Commit if all operations succeed
} catch (\Exception $e) {
    DB::rollBack(); // Rollback if any operation fails

    // Handle the exception
    Log::error($e->getMessage());
}
```

---

### **Key Methods**

* `DB::beginTransaction()`: Starts the transaction.
* `DB::commit()`: Commits the changes to the database.
* `DB::rollBack()`: Reverts all operations if something goes wrong.

---

### **Best Practices**

* Always wrap `beginTransaction()` in a `try-catch` block.
* Use `commit()` only if **all** queries succeed.
* Use `rollBack()` inside the `catch` block to maintain data consistency.
* Log or report errors inside the `catch`.

---

### **When to Use**

* Multiple dependent write operations.
* Operations involving financial records, inventory, etc.
* Any situation where partial success is unacceptable.

---

### **Alternative**

Laravel also supports **automatic transactions** using closures:

```php
DB::transaction(function () {
    // All operations inside here are automatically committed or rolled back
});
```

---
