<?php
/**
 * Created by PhpStorm.
 * User: qiansenmiao
 * Date: 2016/12/29
 * Time: 下午5:31
 */

namespace app\models;

use yii\db\ActiveRecord;
use Yii;

class ProductVariant extends ActiveRecord
{

    public static function tableName()
    {
        return "{{%product_variant}}";
    }

}