## 1.说明
这是一个有关账单管理的框架，具有自动路由，日志，mysql，redis等功能

模拟对于账单的增删改查和收藏等功能

## 2. 部署
根目录指向 public/（指令）
以下是一部分的指令
/addBillInfo
/updataBillInfo
/deleteBillInfo
/queryBillInfo
/likeBillInfo
/dislikeBillInfo
/queryLikeBillInfo

## 3.使用
**** 3.1 项目测试
使用浏览器访问http://localhost/laravel/test/public/

可以看到
{
    "error_code": 0,
    "message": "欢迎使用账单管理系统"
}
表示程序成功运行

## 4.路由
直接在对应的public后面加上指令，并附上参数便可以执行对应的命令

#### 4.1 新接口
若需要加入一个新的接口，需要在app\Http\Controllers\Bill\BillInfoController.php 中加入以下代码

public function HelloWorld()
{
    return array(
        'erron_code' => 0, 
        'msg' => 'HelloWorld'
    );
}

并在routes\web.php 中加入相对应的路由
Route::any('/hello', [BillInfoController::class, 'HelloWorld']);

之后访问 http://localhost/laravel/test/public/hello
就可以获得以下的返回值
{
    "erron_code": 0,
    "msg": "HelloWorld"
}
这样你就是自己开发了一个新的接口了

## 5.日志

write_log($Test);
$Test为一个字符串，运行了之后会自动在该目录下加入一行日志runtime\log.txt

## 6.功能介绍

## 增加新的订单
只需要在加上指令并放置上对应的参数
?type=收入&category=工资&amount=6522.15&content=基本工资
就可以记录账单


