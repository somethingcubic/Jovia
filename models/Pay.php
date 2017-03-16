<?php
namespace app\models;

use app\common\helps\tools;
use app\models\Order;
use app\models\OrderDetail;
use app\models\Product;
use app\models\ProductSpec;
use linslin\yii2\curl\Curl;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Api\PaymentExecution;
use PayPal\Api\ShippingAddress;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use yii\helpers\Url;
use Yii;

class Pay{


    public static function paypal($orderid){
        $totalprice = Order::find()->where('orderid = :oid', [':oid' => $orderid])->one()->amount;
        $details = OrderDetail::find()->where('orderid = :oid', [':oid' => $orderid])->asArray()->all();
        $payer = new Payer();
        $payer->setPaymentMethod("paypal");
        $items = [];
        foreach($details as $detail){
            $product = Product::find()->where('productid = :pid', [':pid' => $detail['productid']])->one();
            $item = new Item();
            $item->setName($product['title'])
                ->setCurrency('USD')
                ->setQuantity($detail['productnum'])
                ->setSku($detail['productsku']) // Similar to `item_number` in Classic API
                ->setPrice($detail['price']);
            $items[] = $item;
        }
        $itemList = new ItemList();
        $itemList->setItems($items);

        $amount = new Amount();
        $amount->setCurrency("USD")
            ->setTotal($totalprice);

        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($itemList)
            ->setDescription("Payment description")
            ->setInvoiceNumber($orderid);

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl("http://www.51buygogo.cn/index.php?r=pay/return")
            ->setCancelUrl("http://www.51buygogo.cn/index.php?r=pay/cancel");

        $payment = new Payment();
        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions(array($transaction));

        $apiContext = Yii::$app->Ecom->auth();

        $payment->create($apiContext);

        $approvalUrl = $payment->getApprovalLink();
        // 打印出用户授权地址，这里仅仅实现支付过程，流程没有进一步完善。
        return $approvalUrl;
    }

    public static function success($data){
        $paymentId = $data['paymentId'];
        $apiContext = Yii::$app->Ecom->auth();
        $payment = Payment::get($paymentId,$apiContext);

        $execution = new PaymentExecution();
        $execution->setPayerId($data['PayerID']);
//        var_dump($apiContext);exit;

        try {
            // Execute the payment
            $result = $payment->execute($execution, $apiContext);
            return true;
        } catch (Exception $ex) {
            return false;
        }

    }

    public static function notify($data)
    {

        $payment_status = $data['payment_status'];
        $txn_id = $data['txn_id'];
        $status = Order::PAYFAILED;
        if($payment_status == 'Completed')
        {
            $status = Order::PAYSUCCESS;
            $orderid = $data['invoice'];
            $order_info = Order::find()->where('orderid = :oid', [':oid' => $orderid])->one();
            if (!$order_info) {
                return false;
            }
            if ($order_info->status == Order::CHECKORDER) {
                Order::updateAll(['status' => $status, 'txn_id' => $txn_id, 'payext' => json_encode($data)], 'orderid = :oid', [':oid' => $order_info->orderid]);
            } else {
                return false;
            }
        }
        return true;
    }

    public static function verified($data){

        $req = 'cmd=_notify-validate';
        if(function_exists('get_magic_quotes_gpc')) $get_magic_quotes_exists = true;
        foreach ($data as $key => $value) {
            if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1){
                $value = urlencode(stripslashes($value));
            }else{
                $value = urlencode($value);
            }
            $req.= "&$key=$value";
        }
        $ch = curl_init('https://www.sandbox.paypal.com/cgi-bin/webscr');
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
        $res = curl_exec($ch);
        if (strcmp ($res, "VERIFIED") == 0) {
            return true;
        } else if (strcmp ($res, "INVALID") == 0) {
            return false;
        }
    }

}