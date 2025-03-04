<?php

namespace App\Http\Controllers\Bill;


use App\Cache\BillInfoCache as CacheBillInfoCache;
use Illuminate\Routing\Controller as BaseController;
use App\Models\BillInfoModel;
use Illuminate\Http\Request;

class BillInfoController extends BaseController
{
    private static $noInfo = 'noInfo';
    // 添加新订单
    public function HelloWorld()
    {
        return array(
            'erron_code' => 0, 
            'msg' => 'HelloWorld'
        );
    }
    
    public function add(Request $request)
    {
        // 传参
        $type = $request->input('type', '');
        $category = $request->input('category', '');
        $amount = $request->input('amount', '');
        $content = $request->input('content', '');
        if (!$type) {
            return array('erron_code' => 1001, 'msg' => '没有收入或者支出类型');
        }
        // 获取数据库中的订单最大编号，为新的订单找好单独的ID
        $maxId = BillInfoModel::getMaxId();
        $id = $maxId + 1;
        $result = BillInfoModel::addInfo($id, $type, $category, $content, $amount);
        // 测试结果
        if ($result) {
            write_log(date('Y-m-d H:i:s') . '添加新订单 --> 程序步骤通过');
            return array('erron_code' => 101, 'msg' => '添加成功');
        } else {
            return array('erron_code' => 1002, 'msg' => '添加失败');
        }
    }
    public function updata(Request $request)
    {
        $id = $request->input('id', '');
        if (!$id) {
            return array('erron_code' => 1002.1, 'msg' => '没有注明修改id');
        }
        $result = BillInfoModel::updataInfo($id, $request);
        if ($result) {
            write_log(date('Y-m-d H:i:s') . '修改订单 --> 程序步骤通过');
            return array('erron_code' => 102, 'msg' => '修改成功');
        } else {
            return array('erron_code' => 1002, 'msg' => '修改失败');
        }
    }
    public function delete(Request $request)
    {
        $id = $request->input('id', '');
        if (!$id) {
            return array('erron_code' => 1003.1, 'msg' => '没有注明删除id');
        }
        $result = BillInfoModel::deleteInfo($id);
        if ($result) {
            write_log(date('Y-m-d H:i:s') . '删除订单 --> 程序步骤通过');
            return array('erron_code' => 103, 'msg' => '删除成功');
        } else {
            return array('erron_code' => 1003, 'msg' => '删除失败');
        }
    }
    public function query(Request $request)
    {
        //为查询的条件赋值，若是没有加入该条件则会加上缺省的Unix时间的头和尾
        $first_date = $request->input('first_date', '1970-01-01 00:00:00');
        $second_date = $request->input('second_date', '2100-12-31 23:59:59');
        $category = $request->input('category', null);
        if (!$first_date) $first_date = '1970-01-01 00:00:00';
        if (!$second_date) $second_date = '2100-12-31 23:59:59';

        // 使用json输出
        // $result = json_encode(BillInfoModel::queryInfo($first_date, $second_date, $category)); 
        $result = BillInfoModel::queryInfo($first_date, $second_date, $category, null);
        if ($result) {
            write_log(date('Y-m-d H:i:s') . '按条件查询订单 --> 程序步骤通过');
            return array($result, 'erron_code' => 104, 'msg' => '查询成功',);
        } else {
            return array('erron_code' => 1004, 'msg' => '查询失败');
        }
    }


    public function setFavorite(Request $request)
    {
        $id = $request->input('id', '');
        if (!$id) {
            return array('erron_code' => 1005.1, 'msg' => '没有注明收藏id');
        }
        $result = BillInfoModel::changeBillFavorite($id, 'setFa');
        if ($result) {
            write_log(date('Y-m-d H:i:s') . '设置收藏订单 --> 程序步骤通过');
            return array($result, array('erron_code' => 105, 'msg' => '收藏成功'));
        } else {
            return array('erron_code' => 1005, 'msg' => '收藏失败');
        }
    }
    public function unSetFavorite(Request $request)
    {
        $id = $request->input('id', '');
        if (!$id) {
            return array('erron_code' => 1006.1, 'msg' => '没有注明取消收藏id');
        }
        $result = BillInfoModel::changeBillFavorite($id, 'unsetFa');
        if ($result) {
            write_log(date('Y-m-d H:i:s') . '取消收藏订单 --> 程序步骤通过');
            return array($result, array('erron_code' => 106, 'msg' => '取消收藏成功'));
        } else {
            return array('erron_code' => 1006, 'msg' => '取消收藏失败');
        }
    }
    public function queryFavorite(Request $request)
    {
        //和上面普通的查询同理
        $first_date = $request->input('first_date', '1970-01-01 00:00:00');
        $second_date = $request->input('second_date', '2100-12-31 23:59:59');
        $category = $request->input('category', null);
        if (!$first_date) $first_date = '1970-01-01 00:00:00';
        if (!$second_date) $second_date = '2100-12-31 23:59:59';
        $result = CacheBillInfoCache::queryFavoriteBill($first_date, $second_date, $category);
        if ($result) {
            write_log(date('Y-m-d H:i:s') . '查询收藏订单 --> 程序步骤通过');
            return array($result, 'erron_code' => 107, 'msg' => '查询收藏账单成功');
        } else {
            return array('erron_code' => 1007, 'msg' => '查询收藏账单失败');
        }
    }
}
