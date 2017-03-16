<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\file\FileInput;

$options = [
    'item' => function ($index, $label, $name, $checked, $value) {
        // check if the radio button is already selected
        $checked = ($checked) ? 'checked' : '';
        $return = '<label class="radio-inline">';
        $return .= '<input type="radio" name="' . $name . '" value="' . $value . '" ' . $checked . '>';
        $return .= $label;
        $return .= '</label>';
        return $return;
    }];
?>

<link rel="stylesheet" href="css/shop.css" type="text/css"/>
<link rel="stylesheet" href="admin/css/compiled/new-user.css" type="text/css" media="screen"/>
<link rel="stylesheet" href="ueditor/themes/default/css/ueditor.css" type="text/css"/>
<!-- main container -->
<div class="content">
    <div class="container-fluid">
        <div id="pad-wrapper" class="new-user">
            <div class="row-fluid header">
                <h3>Modify Product</h3>
            </div>
        </div>
    </div>
    <div id='tab' class="wst-tab-box">
        <ul class="wst-tab-nav">
            <li class="on">商品信息</li>
            <li>商品描述</li>
            <li>属性规格</li>
        </ul>
        <div class="wst-tab-content" style='width:60%;margin-bottom: 10px;border:0px;'>
            <?php
            $form = ActiveForm::begin([
                'fieldConfig' => [
                    'template' => '<tr><th width="150">{label}</th><td>{input}</td></tr>{error}',
                ],
                'options' => [
                    'id' => 'editform',
                    'enctype' => 'multipart/form-data',
                ],
            ]);
            ?>
            <div class="wst-tab-item" style="position: relative;">
                <table id="baseinfo" class="wst-form">
                    <?php
                    echo $form->field($product, 'cateid')->dropDownList($opts, ['id' => 'cates']);
                    echo $form->field($product, 'title')->textInput(['class' => 'span9']);
                    echo $form->field($product, 'ison')->radioList([0 => '下架', 1 => '上架'], $options);
                    echo $form->field($product, 'issale')->radioList([0 => '非促销', 1 => '促销'], $options);
                    echo $form->field($product, 'ishot')->radioList([0 => '非热卖', 1 => '热卖'], $options);
                    echo $form->field($product, 'istui')->radioList([0 => '非推荐', 1 => '推荐'], $options);
                    ?>
                    <tr>
                        <th width="150" class="control-label" for="product-cover">Cover：</th>
                        <td>
                            <img src="<?php echo $product['cover']; ?>" style="width:150px; height:150px;"/>
                            <input type="file" id="product-cover" name="Product[cover]" value="<?php echo $product['cover']; ?>" />
                        </td>
                    </tr>

                </table>


            </div>
            <div class="wst-tab-item" style="position: relative;display: none;">
                <?php echo $form->field($product, 'desc')->textarea(['id' => 'desc'])->label(false); ?>
            </div>
            <div class="wst-tab-item" style="position: relative;display: none;">
                <div id="specsVarBox">

                </div>
            </div>
        </div>
        <hr>
        <div class="span11 field-box actions">
            <?php echo Html::submitButton('提交', ['class' => 'btn-glow primary']); ?>
            <span>OR</span>
            <?php echo Html::Button('取消', ['class' => 'reset','onclick' => "window.location.href='".\yii\helpers\Url::to(['product/list'])."'"]); ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<!-- side right column -->

<!-- end main container -->
<script type="text/javascript" charset="utf-8" src="ueditor/third-party/jquery-1.10.2.min.js"></script>
<script type="text/javascript" charset="utf-8" src="ueditor/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="ueditor/ueditor.all.min.js"></script>
<script type="text/javascript" charset="utf-8" src="ueditor/lang/zh-cn/zh-cn.js"></script>
<script type="text/javascript" charset="utf-8" src="js/product.js"></script>
<script>
    UE.getEditor('desc', {
        initialFrameHeight: 200,
        autoHeightEnabled: false,
        autoFloatEnabled: false,
        elementPathEnabled: false,
        wordCount: false,
    });

    $("#tab .wst-tab-nav li").click(function () {
        var i = $(this).index();
        $(this).addClass("on").siblings().removeClass();
        $('.wst-tab-content .wst-tab-item').eq(i).show().siblings().hide();
    });

    var procateid = <?php echo $product['cateid']; ?>;
    var variant = eval(<?php echo json_encode(\yii\helpers\ArrayHelper::toArray($variant)); ?>);
    var spec = eval(<?php echo json_encode(\yii\helpers\ArrayHelper::toArray($spec)); ?>);
    var provariant = eval(<?php echo json_encode(\yii\helpers\ArrayHelper::toArray($provariant)); ?>);
    showVariant(variant,spec,provariant);

    $("select#cates").change(function () {
        var cateid = $(this).val();
        if (cateid != procateid) {
            var url = "<?php echo yii\helpers\Url::to(['product/getcates']); ?>";
            getVariant(cateid, url);
        }else{
            showVariant(variant,spec,provariant);
        }
    });


    //上传图片


</script>
