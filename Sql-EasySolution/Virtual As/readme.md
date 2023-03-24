<div style="margin:0 auto;display: table; text-transform:capitalize;">
	<h2>Calculate a field according to other fields? OR Calculate something on accouts on Mysql accurectly ?</h2>
	<p><b><u>Solution</u></b>: lets Think Of An <u><b>'Account Table'</u></b> where <b>'total_product_price'</b> and <b>'received_amount'</b> already Exists.</p>
	<p>Now we Need another field <b><u>Due</u></b>.so most of us will first will calculate by code and then set that value to database</p>
	<p><b>But</b> now we can skip that work and let <u>mysql</u> do that job.By using <u><b>virtualAs</u></b>.
		We just need to add that column and leave the rest work to <u>mysql</u> </p>
	<p><u><b>Example</u></b> 1:</p>
	<blockquote>
		Schema::table('Account', function (Blueprint $table) {</br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			$table->float('due', 20, 2)->virtualAs('total_product_price-received_amount');</br>
		});</br>
	</blockquote>
</div>