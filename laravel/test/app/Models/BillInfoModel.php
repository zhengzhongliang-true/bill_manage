<?php

namespace App\Models;

use App\Cache\BillInfoCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class BillInfoModel extends Model
{
    protected $connection = 'mysql';
    protected $table = 'mysql.Bill_Info';
    protected $primekey = 'id';
    public $timestamps = false;
    public static function getAll()
    {
        return self::get()->toArray();
    }
    public static function getMaxId()
    {
        return self::where('id', '>=', 1)->max('id');
    }
    public static function getAllFaBill()
    {
        return self::where('favorite', 1)->get()->toArray();
    }
    public static function addInfo($id, $type, $category, $content, $amount)
    {
        $model = new self();
        $model->id = $id;
        $model->type = $type;
        $model->category = $category;
        $model->content = $content;
        $model->amount = $amount;
        $model->paid_time = date('Y-m-d H:i:s');
        $model->last_updata = date('Y-m-d H:i:s');
        $model->favorite = 0;
        return $model->save();
    }
    // 将输入的时间转换成Unix时间戳
    public static function unixTime($index)
    {
        $str_array = explode(' ', $index);
        $YMD_date = explode('-', $str_array[0]);
        $HMS_date = explode(':', $str_array[1]);
        $valuedate = mktime(
            (int)$HMS_date[0],
            (int)$HMS_date[1],
            (int)$HMS_date[2],
            (int)$YMD_date[1],
            (int)$YMD_date[2],
            (int)$YMD_date[0]
        );
        return $valuedate;
    }
    public static function queryInfo($first_date, $second_date, $category, $All_Bill)
    {
        $answer = array();
        $Total_income = 0.0;
        $Total_speed = 0.0;
        // 若是没有一个已有的订单表来查询，则会默认查全表
        if (!$All_Bill)
            $All_Bill = self::get()->toArray();
        $Ftime = BillInfoModel::unixTime($first_date);
        $Stime = BillInfoModel::unixTime($second_date);

        foreach ($All_Bill as $value) {
            $tatol_time = $value['paid_time'];
            $time1 = BillInfoModel::unixTime($tatol_time);

            //有账单类型时的查询    
            if ($category == $value['category'] && $time1 <= $Stime && $time1 >= $Ftime) {
                array_push($answer, $value);
                if ($value['type'] == '支出')
                    $Total_speed += $value['amount'];
                else if ($value['type'] == '收入')
                    $Total_income += $value['amount'];
            }
            //直接查一个时间段，或者单个时间
            else if ($time1 <= $Stime && $time1 >= $Ftime && !$category) {
                array_push($answer, $value);
                if ($value['type'] == '支出')
                    $Total_speed += $value['amount'];
                else if ($value['type'] == '收入')
                    $Total_income += $value['amount'];
            }
        }
        return array(
            '查询账单的总收入为' => $Total_income,
            '查询账单的总支出为' => $Total_speed,
            $answer
        );
    }
    public static function deleteInfo($idNum)
    {
        return self::where('id', $idNum)->delete();
    }

    /*
    * 这里的函数根据输入的CMD的不同，分别执行取消收藏或者设置收藏状态
    * 因为取消和设置收藏的代码有大段重合，为了减小代码重复，将其放置在同一个函数内
    */

    public static function changeBillFavorite($id, $cmd)
    {
        $list1 = BillInfoModel::getAll();
        $return = BillInfoCache::setBillFavorite($id, $list1, $cmd);
        if ($return) {
            self::where('id', $id)->delete();
            self::insert($return[0]);
            return $return[1];
        } else
            return null;
    }

    /*
    * 该更新函数为了防止出现只想修改单个数据，从而导致其他数据消失的情况
    * 把原数据全部作为缺省值，可以防止出现上述情况
    * 最后再更新一下修改的时间
    */

    public static function updataInfo($idNum, Request $request)
    {

        $thisobj = self::where('id', $idNum)->first();
        return self::where('id', $idNum)->update(
            array(
                'category' => $request->input('category', $thisobj['category']),
                'content' => $request->input('content', $thisobj['content']),
                'type' => $request->input('type', $thisobj['type']),
                'amount' => $request->input('amount', $thisobj['amount']),
                'last_updata' => date('Y-m-d H:i:s')
            )
        );
    }
}
