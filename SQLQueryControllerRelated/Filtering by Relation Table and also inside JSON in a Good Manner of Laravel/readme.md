
    <h1>How to Create Relationship on tables By Model in Laravel?</h1>

    <p>Solution:</p>
    <p>Creating Relationship by model in difffrent ways in laravel and caling Then on controller Process</p>

    <p>If you have Models named Post and Comment. Post and Comments are related by some primary key and Foreign Key. Then you have to write tht code to both models or The one where just you need most</p>

    <ul>
        <li>
            This Relation can be made with hasMany, hasOne, belongsTo... etc
            <b>
                <blockquote>
                    public function Comments(){ <br/>
                        &nbsp;&nbsp;&nbsp;&nbsp;return $this->hasMany('App\Model\Comment');<br/>
                    }<br/>
                </blockquote>
            </b>
        </li>
    </ul>

    <ol>
        <li>    
            // this is the model path, called upperside of controller<br>
            <b><blockquote> use App\Models\Post;</blockquote></b>
            // inside Method Retrieve all posts that have at least one comment...<br>
            <b><blockquote>$posts = Post::has('comments')->get();"</blockquote> </b>
            // Here we have 'Post model(table)' and has a connection with 'Comments Model(table)' may be using with id as foreign and primary keys.<br><br>
        </li>
        <li>
            // this is the model path, called upperside of controller<br/>
            <b><blockquote> use App\Models\Post;</blockquote></b>
            // Retrieve all posts that have at least one comment...<br/>
            <b><blockquote>$posts = Post::with('comments')->get();</blockquote></b>

            <p>For Example: //if Subcategory Datas has a relation with category. And if you want to get category data while using Subcategory Filtering. <br/></p>
            <b>
                <blockquote>
                        $data = Subcategory::with(["category"]) <br/>
                        ->where('name','like', '%'.$this->searchData.'%')<br/>
                        ->orWhereHas('category', function($query){<br/>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $query->where('name', 'like', '%'.$this->searchData.'%');<br/>
                        })<br/>
                        ->orderBy($this->orderByData, $this->orderByAsc ? 'asc' : 'desc')<br/>
                        ->paginate($this->showData);
                </blockquote>
            </b>                
        </li>
    </ol>

References:
<ul>
    <li>
        <a href="https://laravel.com/docs/8.x/eloquent-relationships#querying-relationship-existence">
            link: https://laravel.com/docs/8.x/eloquent-relationships#querying-relationship-existence
        </a> 
    </li>
</ul>
