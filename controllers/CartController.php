<?php
namespace app\controllers;

use Yii;
use app\controllers\CommonController;
use app\models\Cart;
use app\models\Product;
use app\models\ProductSpec;
use app\common\helps\tools;
use app\models\User;

class CartController extends CommonController
{
    public $enableCsrfValidation = false;

    public function actionIndex(){
        $this->layout = 'layout2';
        if (Yii::$app->session['isLogin'] != 1) {
            return $this->redirect(['member/auth']);
        }
        $userid = User::find()->where('username = :name', [':name' => Yii::$app->session['loginname']])->one()->userid;
        $cart = Cart::find()->where('userid = :uid', [':uid' => $userid])->all();
//        var_dump($cart);exit;
        $data = [];
        foreach ($cart as $k => $pro) {
            $product = Product::find()->where('productid = :pid', [':pid' => $pro['productid']])->one();
            $spec = ProductSpec::find()->where('productid = :pid and sku = :sku', [':pid' => $pro['productid'], ':sku' => $pro['sku']])->one();
            $data[$k]['pic'] = $spec->pic;
            $data[$k]['sku'] = $spec->sku;
            $data[$k]['title'] = $product->title;
            $data[$k]['productnum'] = $pro['productnum'];
            $data[$k]['price'] = $pro['price'];
            $data[$k]['productid'] = $pro['productid'];
            $data[$k]['cartid'] = $pro['cartid'];
        }
        return $this->render('index',['data' => $data]);
    }

    public function actionAdd(){
        if (Yii::$app->session['isLogin'] != 1) {
            return tools::show(-1,'请先登录');
        }
        $userid = User::find()->where('username = :name', [':name' => Yii::$app->session['loginname']])->one()->userid;
        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            $data['Cart'] = $post;
            $data['Cart']['userid'] = $userid;
            if (!$model = Cart::find()->where('productid = :pid and userid = :uid and sku = :sku', [':pid' => $data['Cart']['productid'], ':uid' => $data['Cart']['userid'], ':sku' => $data['Cart']['sku']])->one()) {
                $model = new Cart;
            } else {
                $data['Cart']['productnum'] = $model->productnum + $data['Cart']['productnum'];
            }
            $data['Cart']['createtime'] = time();
            $model->load($data);
            if($model->save()){
                return tools::show(1,'Add Success!');
            }else{
                return tools::show(0,'发生未知错误!');
            }
        }
    }

    public function actionMod()
    {
        $cartid = Yii::$app->request->get("cartid");
        $productnum = Yii::$app->request->get("productnum");
        Cart::updateAll(['productnum' => $productnum], 'cartid = :cid', [':cid' => $cartid]);
    }

    public function actionDel()
    {
        $cartid = Yii::$app->request->get("cartid");
        Cart::deleteAll('cartid = :cid', [':cid' => $cartid]);
        return $this->redirect(['cart/index']);
    }

}