<?php
namespace app\controllers;

use app\models\ProductSpec;
use Yii;
use yii\web\Controller;
use app\models\User;
use app\models\Cart;
use app\models\Product;

class CommonController extends Controller
{
    public function init()
    {
        $data = [];
        $data['products'] = [];
        $total = 0;
        if(Yii::$app->session['isLogin']){
            $usermodel = User::find()->where('username = :name', [":name" => Yii::$app->session['loginname']])->one();
            if (!empty($usermodel) && !empty($usermodel->userid)) {
                $userid = $usermodel->userid;
                $carts = Cart::find()->where('userid = :uid', [':uid' => $userid])->asArray()->all();
                foreach($carts as $k=>$pro) {
                    $product = Product::find()->where('productid = :pid', [':pid' => $pro['productid']])->one();
                    $spec = ProductSpec::find()->where('productid = :pid and sku = :sku', [':pid' => $pro['productid'], ':sku' => $pro['sku']])->one();
                    $data['products'][$k]['pic'] = $spec->pic;
                    $data['products'][$k]['title'] = $product->title;
                    $data['products'][$k]['productnum'] = $pro['productnum'];
                    $data['products'][$k]['price'] = $pro['price'];
                    $data['products'][$k]['productid'] = $pro['productid'];
                    $data['products'][$k]['cartid'] = $pro['cartid'];
                    $total += $data['products'][$k]['price'] * $data['products'][$k]['productnum'];
                }
            }
        }
        $data['total'] = $total;
        $this->view->params['cart'] = $data;
    }
}