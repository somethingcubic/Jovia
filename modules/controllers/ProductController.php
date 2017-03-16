<?php

namespace app\modules\controllers;

use app\models\Variant;
use app\modules\controllers\CommonController;
use Yii;
use app\models\Product;
use app\models\Category;
use app\models\ProductVariant;
use app\models\ProductSpec;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

class ProductController extends CommonController
{
    public $enableCsrfValidation = false;

    public function actionList()
    {
        $this->layout = 'layout1';
        $model = new Product;
        $products = $model->find()->all();
        return $this->render('products', ['products' => $products]);
    }

    public function actionAdd()
    {
        $this->layout = 'layout1';
        $product = new Product;
        $cates = new Category;
        $opts = $cates->getOptions();
//        unset($opts[0]);
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $post['Specs']['pics'] = UploadedFile::getInstancesByName('Specs[pic]');
            $img = UploadedFile::getInstance($product, 'cover');
            $post['Product']['cover'] = $img;
            $product->add($post);
            return $this->redirect(['product/list']);
        }
        return $this->render('add', ['product' => $product, 'opts' => $opts]);
    }

    public function actionDetails()
    {
        $this->layout = 'layout1';
        $model = new Product;
        if (Yii::$app->request->isGet) {
            $get = Yii::$app->request->get();
            $productid = $get['productid'];
            $cates = new Category;
            $opts = $cates->getOptions();
            $product = $model->find()->where('productid = :id', [':id' => $productid])->one();
            $provariant = ProductVariant::find()->where('productid = :id', [':id' => $productid])->all();
            $spec = ProductSpec::find()->where('productid = :id', [':id' => $productid])->all();
            $variant = Variant::find()->where('cateid = :cid', [':cid' => $product['cateid']])->all();
        }
        return $this->render('details', ['variant' => $variant, 'opts' => $opts, 'product' => $product, 'provariant' => $provariant, 'spec' => $spec]);
    }

    public function actionMod()
    {
        $this->layout = 'layout1';
        $productid = Yii::$app->request->get('productid');
        $cates = new Category;
        $opts = $cates->getOptions();
        $product = Product::find()->where('productid = :id', [':id' => $productid])->one();
        $provariant = ProductVariant::find()->where('productid = :id', [':id' => $productid])->all();
        $spec = ProductSpec::find()->where('productid = :id', [':id' => $productid])->all();
        $variant = Variant::find()->where('cateid = :cid', [':cid' => $product['cateid']])->all();
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if($_FILES['Product']['size']['cover'] != ''){
                $img = UploadedFile::getInstance($product, 'cover');
                $post['Product']['cover'] = $img;
            }else{
                $post['Product']['cover'] = $product['cover'];
            }
            if($product->updatePro($post,$productid)){
                return $this->redirect(['product/list']);
            }

        }
        return $this->render('mod', ['variant' => $variant, 'opts' => $opts, 'product' => $product, 'provariant' => $provariant, 'spec' => $spec]);
    }

    public function actionGetcates()
    {
        if (Yii::$app->request->isGet) {
            $get = Yii::$app->request->get();
            $data = Variant::find()->where('cateid = :id', [':id' => $get['cateid']])->all();
            $cates = ArrayHelper::toArray($data);
            if ($cates) {
                return json_encode($cates);
            }
        }
    }

    public function actionDel(){
        if(Yii::$app->request->isGet){
            $get = Yii::$app->request->get();
        }
        $productid = $get['productid'];
        $product = new Product();
        if($product->deletePro($productid)){
            return $this->redirect(['product/list']);
        }else{
            return $this->redirect(['product/list']);
        }
    }


}
