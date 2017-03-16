<?php
/**
 * Created by PhpStorm.
 * User: qiansenmiao
 * Date: 2017/1/6
 * Time: 上午11:54
 */
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$pv = array();
foreach ($provariant as $k => $v) {
    $id = $v['variantid'] . '_' . $v['variantnum'];
    $val = $v['variantvalue'];
    $pv[$id] = $val;
}
?>

<link rel="stylesheet" href="admin/css/compiled/new-user.css" type="text/css" media="screen"/>
<link rel="stylesheet" href="css/shop.css" type="text/css"/>
<div class="content">
    <div class="container-fluid">
        <div id="pad-wrapper" class="new-user">
            <div class="row-fluid header">
                <h3>Product Details</h3>
            </div>
        </div>
    </div>
    <div class="row-fluid form-wrapper">
        <!-- left column -->
        <div class="span9 with-sidebar">
            <div class="container">
                <div class="wst-tab-item">
                    <table id="baseinfo" class="specs-custom-table">
                        <tr>
                            <td>Title :</td>
                            <td><?php echo $product['title']; ?></td>
                        </tr>
                        <tr>
                            <td>Category :</td>
                            <td><?php echo $opts[$product['cateid']]; ?></td>
                        </tr>
                        <tr>
                            <td>Ison :</td>
                            <td>
                                <input type="radio" <?php if ($product['ison'] == 1) echo "checked"; ?>/> 上架
                                <input type="radio" <?php if ($product['ison'] == 0) echo "checked"; ?>/> 下架
                            </td>
                        </tr>
                        <tr>
                            <td>Ishot :</td>
                            <td>
                                <input type="radio" <?php if ($product['ishot'] == 1) echo "checked"; ?>/> 热卖
                                <input type="radio" <?php if ($product['ishot'] == 0) echo "checked"; ?>/> 非热卖
                            </td>
                        </tr>
                        <tr>
                            <td>Issale :</td>
                            <td>
                                <input type="radio" <?php if ($product['issale'] == 1) echo "checked"; ?>/> 促销
                                <input type="radio" <?php if ($product['issale'] == 0) echo "checked"; ?>/> 非促销
                            </td>
                        </tr>
                        <tr>
                            <td>Istui :</td>
                            <td>
                                <input type="radio" <?php if ($product['istui'] == 1) echo "checked"; ?>/> 推荐
                                <input type="radio" <?php if ($product['istui'] == 0) echo "checked"; ?>/> 非推荐
                            </td>
                        </tr>
                        <tr>
                            <td>Desc :</td>
                            <td><?php echo $product['desc']; ?></td>
                        </tr>
                        <tr>
                            <td>Cover :</td>
                            <td><img src="<?php echo $product['cover']; ?>" style="width:100px;height:100px;"></td>
                        </tr>
                    </table>
                </div>
                <form action="<?php echo \yii\helpers\Url::to(['product/details']) ?>" enctype="multipart/form-data" method="post">
                    <input type="hidden" name="productid" value="<?php echo $product['productid'] ?>" />
                    <div class="wst-tab-item" style="max-width: 80%;overflow-x: auto;">
                        <div id="specsVarBox">
                            <table class="specs-sale-table" style="width:100%">
                                <thead id="spec-sale-hed">
                                <tr>
                                    <th>SKU</th>
                                    <th>Trip_id</th>
                                    <?php
                                    foreach ($variant as $k => $v):
                                        ?>
                                        <th><?php echo $v['title']; ?></th>
                                    <?php endforeach; ?>
                                    <th id="thCol">Price</th>
                                    <th>Image</th>
                                </tr>
                                </thead>
                                <tbody id="spec-sale-tby">
                                <?php
                                foreach ($spec as $k => $v):
                                    ?>
                                    <tr>
                                        <td><?php echo $v['sku']; ?></td>
                                        <td><?php echo $v['tripid']; ?></td>
                                        <?php
                                        $arr = explode('-', $v['sku']);
                                        foreach ($arr as $vv):
                                            ?>
                                            <td>
                                                <?php echo $pv[$vv]; ?>
                                            </td>
                                        <?php endforeach; ?>
                                        <td><?php echo $v['price']; ?></td>
                                        <td>
                                            <img src="<?php echo $v->pic; ?>" class="img-circle avatar hidden-phone" style="width: 20px;height: 20px;" />
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php if ($v['pic'] == null): ?>
                        <div class="span11 field-box actions">
                            <input type="submit" value="submit" class="btn-glow primary" />
                        </div>
                        <?php else: ?>
                        <div class="span11 field-box actions">
                            <input type="button" value="<-back" onclick="window.location.href='<?php echo \yii\helpers\Url::to(['product/list']) ?>'" class="btn-glow primary" />
                            <input type="button" value="edit->" onclick="window.location.href='<?php echo \yii\helpers\Url::to(['product/mod','productid'=>$product['productid']]); ?>'" class="btn-glow primary" />
                        </div>
                    <?php endif; ?>

                </form>
            </div>
        </div>
    </div>
</div>