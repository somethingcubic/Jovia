<?php

namespace app\modules\controllers;

use app\modules\controllers\CommonController;
use app\models\Category;
use Yii;

class CategoryController extends CommonController
{

    public function actionList()
    {
        $this->layout = "layout1";
        $model = new Category;
        $data = [];
        $cates = $model->getTree();
        foreach($cates as $v){
            $data[$v['cateid']] = $v['title'];
        }
        return $this->render("cates", ["cates" => $cates, 'data' => $data]);
    }

    public function actionAdd()
    {
        $this->layout = "layout1";
        $model = new Category;
        $list = $model->getOptions();
        if (Yii::$app->request->isPost)
        {
            $post = Yii::$app->request->post();
            if ($model->add($post))
            {
                \Yii::$app->session->setFlash('info', '添加成功');
                return $this->redirect(['category/list']);
                \Yii::$app->end();
            }
        }
        return $this->render('add', ['list' => $list, 'model' => $model]);
    }

    public function actionMod()
    {
        $this->layout = "layout1";
        $cateid = \Yii::$app->request->get('cateid');
        $model = Category::find()->where('cateid = :id', [':id' => $cateid])->one();
        if (Yii::$app->request->isPost)
        {
            $post = Yii::$app->request->post();
            if ($model->load($post) && $model->save())
            {
                \Yii::$app->session->setFlash('info', '修改成功');
                return $this->redirect(['category/list']);
            }
        }
        $list = $model->getOptions();
        return $this->render('add', ['model' => $model, 'list' => $list]);
    }

    public function actionDel()
    {
        try
        {
            $cateid = Yii::$app->request->get('cateid');
            if (empty($cateid))
            {
                throw new \Exception('参数错误');
            }
            $data = Category::find()->where('pid = :pid', [':pid' => $cateid])->one();
            if ($data)
            {
                throw new \Exception('该分类下有子类,不允许删除');
            }
            if (!Category::deleteAll('cateid = :id', [':id' => $cateid]))
            {
                throw new \Exception('删除失败');
            }
        } catch (\Exception $e)
        {
            Yii::$app->session->setFlash('info', $e->getMessage());
        }
        return $this->redirect(['category/list']);
    }

}
