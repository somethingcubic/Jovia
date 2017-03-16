<link rel="stylesheet" href="admin/css/compiled/user-list.css" type="text/css" media="screen" />
<!-- main container -->
<div class="content">

    <div class="container-fluid">
        <div id="pad-wrapper" class="users-list">
            <div class="row-fluid header">
                <h3>属性列表</h3>
                <div class="span10 pull-right">
                    <a href="<?php echo yii\helpers\Url::to(['variant/add']) ?>" class="btn-flat success pull-right">
                        <span>&#43;</span>
                        添加属性
                    </a>
                </div>
            </div>

            <?php
            if (Yii::$app->session->hasFlash('info'))
            {
                echo Yii::$app->session->getFlash('info');
            }
            ?>
            <!-- Users table -->
            <div class="row-fluid table">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th class="span3 sortable">
                                <span class="line"></span>分类名称
                            </th>
                            <th class="span3 sortable">
                                <span class="line"></span>属性名称
                            </th>
                            <th class="span3 sortable">
                                <span class="line"></span>属性类型
                            </th>
                            <th class="span3 sortable align-right">
                                <span class="line"></span>操作
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- row -->
                        <?php foreach ($variants as $variant): ?>
                            <tr class="first">
                                <td>
                                    <?php echo $data[$variant['cateid']]; ?>
                                </td>
                                <td>
                                    <?php echo $variant['title']; ?>
                                </td>
                                <td>
                                    <?php echo $variant['varianttype']==1 ? '唯一属性' : '可选属性'; ?>
                                </td>
                                <td class="align-right">
                                    <a href="<?php echo yii\helpers\Url::to(['variant/mod', 'variantid' => $variant['variantid']]); ?>">编辑</a>
                                    <a href="<?php echo yii\helpers\Url::to(['variant/del', 'variantid' => $variant['variantid']]); ?>">删除</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="pagination pull-right">
                <?php /* echo yii\widgets\LinkPager::widget([
                  'pagination' => $pager,
                  'prevPageLabel' => '&#8249;',
                  'nextPageLabel' => '&#8250;',
                  ]); */ ?>
            </div>
            <!-- end users table -->
        </div>
    </div>
</div>
<!-- end main container -->
