
<div style="margin:0 auto;display: table; text-transform:capitalize;">
	<h2>Simply How to Find out Current URL or its Segments( All The parts after slash('/') ) ??</h2>
	<blockquote>example 1:
		<b>
			Request::url()->current();
		</b>
	</blockquote>
	<blockquote>example 2:
		<b>
			request()->segment(count(request()->segments(*)));
		</b>
	</blockquote>
	<br />
<p>To Know The Whole url You Can just Use <b>Example 1</b>.</p>
<p>
	To Know A Specific part of the Whole Url, You Can Use <b>Example 2</b>.
</p>
<p>
	<span>Just replace the <u>'*'</u> with the part number you want to get</span>
	<br /><br />
	<small><b><u>note</u>:</b></small>
	<big>all the Segments are Devided By <u>'/'</u></big>
</p>
</div>

