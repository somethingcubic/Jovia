<?php
namespace app\controllers;

use app\common\helps\tools;
use app\controllers\CommonController;
use Yii;
use app\models\User;

class MemberController extends CommonController
{
    public $enableCsrfValidation;

    public function actionAuth()
    {
        $this->layout = false;
        $url = Yii::$app->request->referrer;
//        var_dump($url);exit;
        if (empty($url)) {
            $url = "/";
        }
        Yii::$app->session->setFlash('referrer', $url);
        return $this->render('auth');
    }

    public function actionReg()
    {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $data['User'] = $post;
            if (!isset($post['username']) || !$post['username']) {
                return tools::show(0, '用户名不能为空');
            }
            if (!isset($post['useremail']) || !$post['useremail']) {
                return tools::show(0, 'email不能为空');
            }
            if (!isset($post['userpass']) || !$post['userpass']) {
                return tools::show(0, '密码不能为空');
            }
            if (!isset($post['repass']) || !$post['repass']) {
                return tools::show(0, '请输入确定密码');
            }
            if ($post['userpass'] != $post['repass']) {
                return tools::show(0, '两次输入密码不一致');
            }
            $model = new User;
            if ($user = User::find()->where('username = :uname', [':uname' => $post['username']])->one()) {
                return tools::show(0, '用户名已被注册');
            }
            if ($user = User::find()->where('useremail = :email', [':email' => $post['useremail']])->one()) {
                return tools::show(0, '邮箱已被使用');
            }
            if ($model->reg($data)) {
                $session = Yii::$app->session;
                $session['loginname'] = $post['username'];
                $session['isLogin'] = 1;
                return tools::show(1, '注册成功',['url' => Yii::$app->session->getFlash('referrer')]);
            }
        }

    }

    public function actionLogin()
    {
        $this->enableCsrfValidation = false;
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $password = md5($post['userpass']);
            $loginname = $post['logname'];
            if (preg_match('/@/', $loginname)) {
                $user = User::find()->where('useremail = :uname and userpass = :pass', [':uname' => $loginname, ':pass' => $password])->one();
            } else {
                $user = User::find()->where('username = :uname and userpass = :pass', [':uname' => $loginname, ':pass' => $password])->one();
            }
            if ($user) {
                $session = Yii::$app->session;
                $session['loginname'] = $loginname;
                $session['isLogin'] = 1;
                return tools::show(1, '登录成功',['url' => Yii::$app->session->getFlash('referrer')]);
            }
            return tools::show(0, '用户名或者密码错误');
        }
    }

    public function actionLogout()
    {
        Yii::$app->session->remove('loginname');
        Yii::$app->session->remove('isLogin');
        if (!isset(Yii::$app->session['isLogin'])) {
            return $this->goBack(Yii::$app->request->referrer);
        }
    }


}
