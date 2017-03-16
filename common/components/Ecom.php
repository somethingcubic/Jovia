<?php
namespace app\common\components;

use Yii;
use yii\base\Component;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;

class Ecom extends Component{

    public $client_id;
    public $client_secret;

    public function auth(){
        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                $this->client_id,
                $this->client_secret
            )
        );

        $apiContext->setConfig(
            array(
                'mode' => 'sandbox',
                'log.LogEnabled' => true,
                'log.FileName' => '../PayPal.log',
                'log.LogLevel' => 'DEBUG', // PLEASE USE `INFO` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS
                'cache.enabled' => true,
                // 'http.CURLOPT_CONNECTTIMEOUT' => 30
                // 'http.headers.PayPal-Partner-Attribution-Id' => '123123123'
                //'log.AdapterFactory' => '\PayPal\Log\DefaultLogFactory' // Factory class implementing \PayPal\Log\PayPalLogFactory
            )
        );
        return $apiContext;
    }
}