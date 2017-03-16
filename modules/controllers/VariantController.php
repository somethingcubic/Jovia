<?php

namespace app\modules\controllers;

use yii\web\Controller;
use app\models\Variant;
use app\models\Category;
use Yii;

class VariantController extends Controller
{
    public function actionList()
    {
        $this->layout = 'layout1';
        $model = new Variant;
        $variants = $model->find()->orderBy('cateid')->all();
        $cates = Category::find()->all();
        $data = [];
        foreach($cates as $v)
        {
            $data[$v['cateid']] = $v['title'];
        }
        return $this->render('lists',['variants' => $variants, 'data' => $data]);
    }
    
    public function actionAdd()
    {
        $this->layout = 'layout1';
        $model = new Variant;
        $cate = new Category;
        $list = $cate->getOptions();
        if(\Yii::$app->request->isPost)
        {
            $post = \Yii::$app->request->post();
            if($model->add($post))
            {
                return $this->redirect(['variant/list']);
                \Yii::$app->end();
            }
        }
        return $this->render('add',['model' => $model, 'list' => $list]);
    }

}
