<div style="margin:0 auto;display: table; text-transform:capitalize;">
	<h2>Using comparison operator to the method:</h2>
	<p><u>Example</u> 1:</p>
	<blockquote>
	$users = DB::table('users')
			->whereColumn('updated_at', '>', 'created_at')
			->get();
	</blockquote>
	<h3>The whereColumn method can also be passed an array of multiple conditions.
	<br />These conditions will be joined using the AND operator:</h3>
	<p><u>Example</u> 2:</p>
	<blockquote>
	$users = DB::table('users')
			->whereColumn([
				['first_name', '=', 'last_name'],
				['updated_at', '>', 'created_at']
			])->get();
	</blockquote>
</div>