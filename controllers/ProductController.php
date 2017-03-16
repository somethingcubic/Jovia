<?php

namespace app\controllers;


use yii\helpers\ArrayHelper;
use app\controllers\CommonController;
use app\models\Product;
use app\models\ProductVariant;
use app\models\ProductSpec;
use app\models\Variant;
use Yii;

class ProductController extends CommonController
{
    public $enableCsrfValidation = false;

    public function actionIndex(){
        $this->layout = "layout1";
        $products = Product::find()->all();
        return $this->render("index", ['products' => $products]);
    }

    public function actionDetail()
    {
        $this->layout = 'layout1';
        if (Yii::$app->request->isGet) {
            $get = Yii::$app->request->get();
        }
        $productid = $get['productid'];
        $product = Product::find()->where('productid = :pid', [':pid' => $productid])->one();
        $cateid = $product['cateid'];
        $variants = Variant::find()->where('cateid = :cid', [':cid' => $cateid])->all();
        return $this->render('detail', ['variants' => $variants, 'product' => $product]);
    }

    public function actionGetprice()
    {
        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            $productid = $post['productid'];
            $sku = $post['sku'];
            $spec = ProductSpec::find()->where('productid = :pid and sku = :sku',[':pid' => $productid, ':sku' => $sku])->one();
            $spec = ArrayHelper::toArray($spec);
            echo json_encode($spec);
        }
    }
}

