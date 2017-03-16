<?php
/**
 * Created by PhpStorm.
 * User: qiansenmiao
 * Date: 2016/12/30
 * Time: 下午5:48
 */

namespace app\models;

use yii\db\ActiveRecord;
use Yii;

class ProductSpec extends ActiveRecord
{

    public static function tableName()
    {
        return "{{%product_spec}}";
    }

}