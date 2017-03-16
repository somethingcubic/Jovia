<?php
namespace app\modules\controllers;

use app\models\Order;
use app\models\OrderDetail;
use app\models\Product;
use app\models\User;
use app\models\Address;
use yii\web\Controller;
use yii\data\Pagination;
use Yii;
use app\modules\controllers\CommonController;
use app\common\helps\tools;

class OrderController extends CommonController
{
    public function actionList()
    {
        $this->layout = "layout1";
        $model = Order::find();
        $count = $model->count();
        $pageSize = Yii::$app->params['pageSize']['order'];
        $pager = new Pagination(['totalCount' => $count, 'pageSize' => $pageSize]);
        $data = $model->offset($pager->offset)->limit($pager->limit)->all();
        $data = Order::getDetail($data);
        return $this->render('list', ['pager' => $pager, 'orders' => $data]);
    }

    public function actionDetail()
    {
        $this->layout = "layout1";
        $orderid = (int)Yii::$app->request->get('orderid');
        $order = Order::find()->where('orderid = :oid', [':oid' => $orderid])->one();
        $data = Order::getData($order);
        return $this->render('detail', ['order' => $data]);
    }

    public function actionSend()
    {
        $this->layout = "layout1";
        $orderid = (int)Yii::$app->request->get('orderid');
        $model = Order::find()->where('orderid = :oid', [':oid' => $orderid])->one();
        $model->status = Order::SENDED;
        if ($model->save()) {
            return tools::show(1, '发货成功');
        }
    }

}