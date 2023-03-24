<div style="margin:0 auto;display: table; text-transform:capitalize;">
	<h2>How to Filter inside of related table related to foreign key While calling From a base Table ??</h2>
	<br />
	<p><b>For example</b>: lets take a Table name <b><u>'Video'</u></b> Which has an User_id as foreign key. now We need to get data from Video and also have to filter that:</p>
	<p><b>Get All The Data of Video Where User Name is 'Ahsan'</b>. Then Using that query we can easily Filter This data.</p>
	<blockquote>
		use App\Video; // set this on the top after php starts.
		</br>
		$Videos = Video::with('user')-whereHas('user', function($q){</br>
					$q-where('name','=','Ahsan');</br>
				})-get();</br>
	</blockquote>
</div>