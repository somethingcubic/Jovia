    <link rel="stylesheet" href="assets/admin/css/compiled/user-list.css" type="text/css" media="screen" />
    <!-- main container -->
    <div class="content">
        
        <div class="container-fluid">
            <div id="pad-wrapper" class="users-list">
                <div class="row-fluid header">
                    <h3>订单列表</h3>
                </div>

                <!-- Users table -->
                <div class="row-fluid table">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th class="span2 sortable">
                                    <span class="line"></span>订单编号
                                </th>
                                <th class="span2 sortable">
                                    <span class="line"></span>下单人
                                </th>
                                <th class="span3 sortable">
                                    <span class="line"></span>收货地址
                                </th>
                                <th class="span2 sortable">
                                    <span class="line"></span>订单总价
                                </th>
                                <th class="span3 sortable">
                                    <span class="line"></span>商品列表
                                </th>
                                <th class="span3 sortable">
                                    <span class="line"></span>订单状态
                                </th>
                                <th class="span2 sortable align-right">
                                    <span class="line"></span>操作
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        <!-- row -->
                        <?php foreach($orders as $order): ?>
                        <tr class="first">
                            <td>
                                <?php echo $order->orderid ?>
                            </td>
                            <td>
                                <?php echo $order->username?>
                            </td>
                            <td>
                                <?php echo $order->address ?>
                            </td>

                            <td>
                                <?php echo $order->amount ?>
                            </td>
                            <td>
                                <?php foreach($order->products as $product): ?>
                                    <?php echo $product['num'] ?> x <a href="<?php echo yii\helpers\Url::to(['/product/detail', 'productid' => $product['productid']]) ?>"><?php echo $product['title'] ?></a>
                                <?php endforeach; ?>
                            </td>
                            <?php
                                if (in_array($order->status, [0])) {
                                    $info = "error";
                                }

                                if (in_array($order->status, [100,202])) {
                                    $info = "info";
                                }
                                
                                if (in_array($order->status, [201])) {
                                    $info = "warning";
                                }

                                if (in_array($order->status, [220,260])) {
                                    $info = "success";
                                }


                            ?>
                                <td><span class="label label-<?php echo $info ?>"> <?php echo $order->zhstatus ?> </span></td>
                            <td class="align-right">
                                <?php if ($order->status == 202): ?>
                                    <a href="#" onclick="send('<?php echo $order->orderid ?>');return false;">发货</a>
                                <?php endif; ?>
                                <a href="<?php echo yii\helpers\Url::to(['order/detail', 'orderid' => $order->orderid]) ?>">查看</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="pagination pull-right">
                    <?php echo yii\widgets\LinkPager::widget([
                        'pagination' => $pager,
                        'prevPageLabel' => '&#8249;',
                        'nextPageLabel' => '&#8250;',
                    ]) ?>
                </div>
                <!-- end users table -->
            </div>
        </div>
    </div>
    <!-- end main container -->

    <script src="/js/jquery-1.10.2.min.js"></script>
    <script src="js/dialog/layer.js"></script>
    <script src="js/dialog.js"></script>
    <script>

        var SCOPE = {
            'send_url' : "<?php echo \yii\helpers\Url::to(['order/send']) ?>",
        };

        function send(orderid){
            $.ajax({
                url: SCOPE.send_url,
                type: 'get',
                data: {'orderid':orderid},
                dataType: 'json',
                async: false,
                success: function (result) {
                    if (result.status == 1) {
                        dialog.notifyrl(result.message);
                    }
                }
            });
        }
    </script>
