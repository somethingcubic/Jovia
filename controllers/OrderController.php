<?php
namespace app\controllers;

use app\common\helps\tools;
use app\models\ProductSpec;
use Yii;
use app\controllers\CommonController;
use app\models\Order;
use app\models\OrderDetail;
use app\models\Cart;
use app\models\Product;
use app\models\Address;
use app\models\User;
use app\models\Pay;
use PayPal\Api\Payment;

class OrderController extends CommonController
{
    public $enableCsrfValidation = false;

    public function actionIndex()
    {
        $this->layout = "layout2";
        if (Yii::$app->session['isLogin'] != 1) {
            return $this->redirect(['member/auth']);
        }
        $loginname = Yii::$app->session['loginname'];
        $userid = User::find()->where('username = :name or useremail = :email', [':name' => $loginname, ':email' => $loginname])->one()->userid;
        $orders = Order::getProducts($userid);
        return $this->render("index", ['orders' => $orders]);
    }

    public function actionCreate()
    {
        if (Yii::$app->session['isLogin'] != 1) {
            return $this->redirect(['member/login']);
        }
        $userid = User::find()->where('username = :name', [':name' => Yii::$app->session['loginname']])->one()->userid;
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (Yii::$app->request->isPost) {
                $post = Yii::$app->request->post();
//                var_dump($post);exit;
                $ordermodel = new Order;
                $ordermodel->scenario = 'create';
                $ordermodel->userid = $userid;
                $ordermodel->status = Order::CREATEORDER;
                $ordermodel->createtime = time();
                if (!$ordermodel->save()) {
                    throw new \Exception();
                }
                $orderid = $ordermodel->getPrimaryKey();
                foreach ($post['OrderDetail'] as $product) {
                    $model = new OrderDetail;
                    $product['orderid'] = $orderid;
                    $product['createtime'] = time();
                    $data['OrderDetail'] = $product;
                    if (!$model->add($data)) {
                        throw new \Exception();
                    }
                }
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            return $this->redirect(['cart/index']);
        }
        return $this->redirect(['order/check', 'orderid' => $orderid]);
    }

    public function actionAdd()
    {
        if (Yii::$app->session['isLogin'] != 1) {
            return tools::show(-1, '请先登录');
        }
        $userid = User::find()->where('username = :name', [':name' => Yii::$app->session['loginname']])->one()->userid;
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (Yii::$app->request->isPost) {
                $post = Yii::$app->request->post();
                $data['Cart']['productid'] = $post['productid'];
                $data['Cart']['productnum'] = $post['productnum'];
                $data['Cart']['sku'] = $post['productsku'];
                $data['Cart']['price'] = $post['price'];
                $data['Cart']['userid'] = $userid;
                if (!$cart = Cart::find()->where('productid = :pid and userid = :uid and sku = :sku', [':pid' => $data['Cart']['productid'], ':uid' => $data['Cart']['userid'], ':sku' => $data['Cart']['sku']])->one()) {
                    $cart = new Cart;
                } else {
                    $data['Cart']['productnum'] = $cart->productnum + $data['Cart']['productnum'];
                }
                $data['Cart']['createtime'] = time();
                if (!($cart->load($data) && $cart->save())) {
                    return tools::show(0, 'cart出错，请重试', [new \Exception()]);
                }
                $ordermodel = new Order;
                $ordermodel->scenario = 'create';
                $ordermodel->userid = $userid;
                $ordermodel->status = Order::CREATEORDER;
                $ordermodel->createtime = time();
            }
            if (!$ordermodel->save()) {
                return tools::show(0, 'order出问题了，请重试', [new \Exception()]);
            }
            $orderid = $ordermodel->getPrimaryKey();
            $model = new OrderDetail();
            $data['OrderDetail'] = $post;
            $data['OrderDetail']['orderid'] = $orderid;
            $data['OrderDetail']['createtime'] = time();
            if (!$model->add($data)) {
                return tools::show(0, 'orderdetail出问题了，请重试', $data);
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            return tools::show(0, '出问题了，请重试', [$e]);
        }
        return tools::show(1, '添加成功', ['orderid' => $orderid]);
    }

    public function actionCheck()
    {
        $this->layout = 'layout2';
        if (Yii::$app->session['isLogin'] != 1) {
            return $this->redirect(['member/auth']);
        }
        $userid = User::find()->where('username = :name', [':name' => Yii::$app->session['loginname']])->one()->userid;
        $orderid = Yii::$app->request->get('orderid');
        $status = Order::find()->where('orderid = :oid', [':oid' => $orderid])->one()->status;
        if ($status != Order::CREATEORDER && $status != Order::CHECKORDER) {
            $this->redirect(['order/index']);
        }
        $addresses = Address::find()->where('userid = :uid', [':uid' => $userid])->asArray()->all();
        $details = OrderDetail::find()->where('orderid = :oid', [':oid' => $orderid])->asArray()->all();
        $data = [];
        foreach ($details as $detail) {
            $model = Product::find()->where('productid = :pid', [':pid' => $detail['productid']])->one();
            $spec = ProductSpec::find()->where('productid = :pid and sku = :sku', [':pid' => $detail['productid'], ':sku' => $detail['productsku']])->one();
            $detail['title'] = $model->title;
            $detail['pic'] = $spec->pic;
            $data[] = $detail;
        }
        return $this->render('check', ['products' => $data, 'addresses' => $addresses]);
    }

    public function actionConfirm()
    {
        //需要更新的字段：amount,status,addressid
        try {
            if (!Yii::$app->request->isPost) {
                throw new \Exception();
            }
            $post = Yii::$app->request->post();
            if (Yii::$app->session['isLogin'] != 1) {
                return $this->redirect(['member/auth']);
            }
            $userid = User::find()->where('username = :name', [':name' => Yii::$app->session['loginname']])->one()->userid;
            $model = Order::find()->where('orderid = :oid and userid = :uid', [':oid' => $post['orderid'], ':uid' => $userid])->one();
            if (empty($model)) {
                throw new \Exception();
            }
            $model->scenario = "update";
            $post['status'] = Order::CHECKORDER;
            $details = OrderDetail::find()->where('orderid = :oid', [':oid' => $post['orderid']])->all();
            $amount = 0;
            foreach ($details as $detail) {
                $amount += $detail->productnum * $detail->price;
            }
            if ($amount <= 0) {
                throw new \Exception();
            }
            $post['amount'] = $amount;
            $data['Order'] = $post;
//            var_dump($data);exit;
            if (empty($post['addressid'])) {
                return $this->redirect(['order/pay', 'orderid' => $post['orderid'], 'paymethod' => $post['paymethod']]);
            }
            if ($model->load($data) && $model->save()) {
                foreach ($details as $detail) {
                    Cart::deleteAll('productid = :pid and sku = :sku', [':pid' => $detail['productid'], ':sku' => $detail['productsku']]);
                }
                return $this->redirect(['order/pay', 'orderid' => $post['orderid'], 'paymethod' => $post['paymethod']]);
            }
        } catch (\Exception $e) {
            return $this->redirect(['index/index']);
        }
    }

    public function actionBackedit()
    {

        $orderid = Yii::$app->request->get('orderid');
        try {
            OrderDetail::deleteAll('orderid = :oid', [':oid' => $orderid]);
            $model = Order::find()->where('orderid = :oid', [':oid' => $orderid])->one();
            $userid = $model->userid;
            $model->delete();
            return $this->redirect(['cart/index', 'userid' => $userid]);
        } catch (\Exception $e) {
            return $this->redirect(['order/check', 'orderid' => $orderid]);
        }
    }

    public function actionPay()
    {
        $this->layout = 'layout1';
        try {
            if (Yii::$app->session['isLogin'] != 1) {
                throw new \Exception();
            }
            $orderid = Yii::$app->request->get('orderid');
            $paymethod = Yii::$app->request->get('paymethod');
            if (empty($orderid) || empty($paymethod)) {
                throw new \Exception();
            }
            if ($paymethod == 'paypal') {
                $url = Pay::paypal($orderid);
                return $this->redirect($url);
            }
        } catch (\Exception $e) {
            throw $e;
        }
        return $this->redirect(['order/index']);
    }


}