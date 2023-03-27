
<div style="margin:0 auto;display: table; text-transform:capitalize;">
	<h2>How to Declare Foreign Key In Migration ??</h2>
	<p>For Example Lets take A migration Of Sub Sub menu means The Grand child menu of Main menu.</p>
	<blockquote>
		Schema::create('sub_sub_menu', function (Blueprint $table) {<br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$table->increments('id'); //Default Auto increments ID <br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$table->string('name'); <br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$table->string('slug'); <br />
			</br></br></br>
			//here it is The Menu ids. we goinG to declare this as Foreign key for Menu(Main) </br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$table->unsignedInteger('fk_menu_id'); <br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$table->foreign('fk_menu_id')->references('id')->on('menu'); //Declaration on(Primary keys Table)<br />
			</br></br></br>
			//here it is The SubMenu ids. we goinG to declare this as Foreign key for SubMenu </br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$table->unsignedInteger('fk_sub_menu_id'); <br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$table->foreign('fk_sub_menu_id')->references('id')->on('sub_menu'); <br />
			</br></br></br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$table->tinyInteger('status'); <br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$table->timestamps(); <br />
		});
	<br />
	</blockquote>
</div>