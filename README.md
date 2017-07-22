# php-html-generator
Php class to generate html code
```php
    //if you have a php array like this:
    $img = [
        "url"=>"a girl ride on a horse",
        "type"=>1, 
        "jump_url"=>[
            "praise"=>"a url to post a praise",
            "comment"=>"a url to post a comment",
        ],
    ];
    //for a auto generator edit table like this
    $div = new Div("");
    $div->addElem("内容", new Input("url"));
    $div->addElem("类型", new Select("type", [0=>"只读", 1=>"可以评论"]));
    $divjump = new Div("jump_url");
    $divjump->addElem("点赞", new Input("praise"));
    $divjump->addElem("评论", new Input("commit"));
    $div->addElem("功能", $divjump, ["类型" => 1]);
    $div->value = $img;
```

```php
    //and if you want to generate a list like this
    $imgList = [
        [
        "url"=>"a girl ride on a horse",
        "type"=>0, 
        "jump_url"=>[
            "praise"=>"a url to post a praise",
            "comment"=>"a url to post a comment",
        ],
        ],
        [
        "url"=>"a boy ride on a horse",
        "type"=>1, 
        ]
    ];
    //you want to edit and add element or delete one
    $listElem = new ListElem("", $div, $imgList);
```


```php
    //if you have a tickek to geneartor a edit table and have a function to submit then content to clound server
    $ticket = [
        "movie_name"=>"穆赫兰道",
        "origin_price"=>500,
        "sale_price"=>200,
        "type"=>"4,5"
    ];
    $t = new EditTable(["width"=>"70%", "align"=>"center" , "cellspacing"=>"0", "cellpadding"=>"6"]);
    $t->setData($ticket);
    $t->row("电影名称", new Input("movie_name"));
    $t->row("票价", new Input("origin_price", "number", "$"));
    $t->row("售价", new Input("sale_price", "number", "$"));
    $t->row("类型", new CheckBox("type", [1=>"爱情", 2=>"恐怖", 3=>"童话", 4=>"推理", 5=>"悬疑"]));
    $t->submit("save", "index", [], "保存");
    $t->render();
```

```php
    //if you have a tickek to geneartor a edit table and have a function to submit then content to clound server
    $ticket =  [
        ["movie_name"=>"穆赫兰道",
        "origin_price"=>500,
        "sale_price"=>200,
        "type"=>"4,5"
        ],[
        "movie_name"=>"银河护卫队",
        "origin_price"=>300,
        "sale_price"=>100,
        "type"=>"4,5"
        ]
    ];
    $t = new Table();
    $t->column("movie_name", "商品ID");
    $t->column("origin_price", "票价($)");
    $t->column("sale_price", "原价($)");
    $t->column(function($row){return button("class", "method", [], "编辑");}, "编辑");
    $t->render();
```