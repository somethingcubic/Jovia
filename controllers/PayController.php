<?php
namespace app\controllers;

use Yii;
use app\controllers\CommonController;
use app\models\Pay;

class PayController extends CommonController
{
    public $enableCsrfValidation = false;
    public function actionReturn()
    {
        if (Yii::$app->request->isGet) {
            $get = Yii::$app->request->get();
            if(Pay::success($get))
            {
                return $this->redirect(['order/index']);
            }
        }

    }

    public function actionNotify()
    {
        if(Yii::$app->request->isPost)
        {
            $post = Yii::$app->request->post();
            if(Pay::verified($post))
            {
                if(Pay::notify($post))
                {
                    echo "success";
                    exit;
                }
                echo "fail";
                exit;
            }

        }
    }
}