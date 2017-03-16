<?php
    use yii\bootstrap\ActiveForm;
    use yii\helpers\Html;
?>
    <link rel="stylesheet" href="admin/css/compiled/new-user.css" type="text/css" media="screen" />
    <!-- main container -->
    <div class="content">
        <div class="container-fluid">
            <div id="pad-wrapper" class="new-user">
                <div class="row-fluid header">
                    <h3>添加新属性</h3>
                </div>
                <div class="row-fluid form-wrapper">
                    <!-- left column -->
                    <div class="span9 with-sidebar">
                        <div class="container">
                                <?php
                                if (Yii::$app->session->hasFlash('info')) {
                                    echo Yii::$app->session->getFlash('info');
                                }
                                $model->varianttype = '1';
                                $form = ActiveForm::begin([
                                    'fieldConfig' => [
                                        'template' => '<div class="span12 field-box">{label}{input}</div>{error}',
                                    ],
                                    'options' => [
                                        'class' => 'new_user_form inline-input',
                                    ],
                                    ]);
                                echo $form->field($model, 'cateid')->dropDownList($list);
                                echo $form->field($model, 'title')->textInput(['class' => 'span9']);
                                echo $form->field($model, 'varianttype')->inline(true)->radioList(['1'=>'唯一','2'=>'可选'],['class' => 'span8']);
                                ?>
                                <div class="span11 field-box actions">
                                    <?php echo Html::submitButton('添加', ['class' => 'btn-glow primary']); ?>
                                    <span>OR</span>
                                    <?php echo Html::resetButton('取消', ['class' => 'reset']); ?>
                                </div>
                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>

                    <!-- side right column -->
                    <div class="span3 form-sidebar pull-right">
                        <div class="alert alert-info hidden-tablet">
                            <i class="icon-lightbulb pull-left"></i>
                            请在左侧表单当中填写要添加的分类，请选择好上级分类
                        </div>                        
                        <h6>分类属性说明</h6>
                        <p>多选属性请在"属性类型"中选择"可选"</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end main container -->
    
    