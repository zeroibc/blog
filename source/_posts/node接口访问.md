---
title: nodejs中通过mongoose块对数据库查询到的内容展示到前台
---

> 2018年7月10日天气晴,乱鸡儿写写

#### 开始 

1.首先需要先引入 express

```javascript

//引入express之前需要先 npm install express
let express = require('express');
let app = express();

//跨域设置请求头.
app.all('*', function(req, res, next) {
    res.header("Access-Control-Allow-Origin", "*");
    res.header("Access-Control-Allow-Headers", "X-Requested-With");
    res.header("Access-Control-Allow-Methods","PUT,POST,GET,DELETE,OPTIONS");
    res.header("X-Powered-By",' 3.2.1');
    res.header('content-type','text/html;charset="utf-8"');
    next();
});

//需要先use下访问静态目录 默认指向 index.html
app.use(express.static('./nodeHtml'))
//city 是请求的接口名
app.get('/city',(req,res)=>{
   res.status(200);

```



2.引入mongoose 首先要看下数据库名字

![](C:\Users\VULCAN\Desktop\1531216400(1).jpg)

```javascript
 
    let mongoose =require('mongoose')
    mongoose.connect('mongodb://localhost/test');//test是数据库的名字
    let db = mongoose.connection
    db.on('error',()=>{
        console.log('连接失败')
    });
//连接成功
    db.once('open', ()=> {
        console.log('连接成功')
        let kittySchema = mongoose.Schema({
            name: String,
            age:Number,
        });

        let kitten = mongoose.model('kitten',kittySchema)
        kitten.find((err,docs)=>{
            if(err){
                return console.error(err);
            }
            console.log(docs);
            res.json(docs); //
            res.end()
        })
    });
})
app.listen(3000);
```

3.到前端页面发送请求

```javascript
 $.ajax({
             type:'get',
             url:'http://localhost:3000/city', //city 是请求的接口名
             success:function(data){
                 console.log(data);
             },
             error:function(){
                 console.log('error');
             }
         })
```

4.打开页面请求接口

![1531216706377](C:\Users\VULCAN\AppData\Local\Temp\1531216706377.png)