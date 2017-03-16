<?php

namespace app\models;

use yii\db\ActiveRecord;
use app\models\OrderDetail;
use app\models\Product;
use app\models\Category;
use yii\helpers\ArrayHelper;

class Order extends ActiveRecord
{
    const CREATEORDER = 0;
    const CHECKORDER = 100;
    const PAYFAILED = 201;
    const PAYSUCCESS = 202;
    const SENDED = 220;
    const RECEIVED = 260;

    public static $status = [
        self::CREATEORDER => '订单初始化',
        self::CHECKORDER => '待支付',
        self::PAYFAILED => '支付失败',
        self::PAYSUCCESS => '等待发货',
        self::SENDED => '已发货',
        self::RECEIVED => '订单完成',
    ];

    public $products;
    public $zhstatus;
    public $username;
    public $address;

    public function rules()
    {
        return [
            [['userid', 'status'], 'required', 'on' => ['create']],
            [['addressid', 'amount', 'status'], 'required', 'on' => ['update']],
            ['createtime', 'safe', 'on' => ['create']],
        ];
    }

    public static function tableName()
    {
        return "{{%order}}";
    }

    public static function getProducts($userid)
    {
        $orders = self::find()->where('status > 0 and userid = :uid', [':uid' => $userid])->orderBy('createtime desc')->all();
        foreach($orders as $order) {
            $details = OrderDetail::find()->where('orderid = :oid', [':oid' => $order->orderid])->all();
            $products = [];
            foreach($details as $detail) {
                $product = Product::find()->where('productid = :pid', [':pid' => $detail->productid])->one();
                $spec = ProductSpec::find()->where('productid = :pid and sku = :sku', [':pid' => $detail->productid, ':sku' => $detail->productsku])->one();
                if (empty($product)) {
                    continue;
                }
                $product = ArrayHelper::toArray($product);
                $product['num'] = $detail->productnum;
                $product['price'] = $detail->price;
                $product['spec'] = $spec;
                $product['cate'] = Category::find()->where('cateid = :cid', [':cid' => $product['cateid']])->one()->title;
                $products[] = $product;
            }
            $order->zhstatus = self::$status[$order->status];
            $order->products = $products;
        }
        return $orders;
    }

    public static function getDetail($orders)
    {
        foreach($orders as $order){
            $order = self::getData($order);
        }
        return $orders;
    }

    public static function getData($order)
    {
        $details = OrderDetail::find()->where('orderid = :oid', [':oid' => $order->orderid])->all();
        $products = [];
        foreach($details as $detail) {
            $product = Product::find()->where('productid = :pid', [':pid' => $detail->productid])->one();
            $spec = ProductSpec::find()->where('productid = :pid and sku = :sku', [':pid' => $detail->productid, ':sku' => $detail->productsku])->one();
            if (empty($product)) {
                continue;
            }
            $product = ArrayHelper::toArray($product);
            $product['num'] = $detail->productnum;
            $product['spec'] = $spec;
            $products[] = $product;
        }
        $order->products = $products;;
        $user = User::find()->where('userid = :uid', [':uid' => $order->userid])->one();
        if (!empty($user)) {
            $order->username = $user->username;
        }
        $order->address = Address::find()->where('addressid = :aid', [':aid' => $order->addressid])->one();
        if (empty($order->address)) {
            $order->address = "";
        } else {
            $order->address = $order->address->address;
        }
        $order->zhstatus = self::$status[$order->status];
        return $order;
    }

}