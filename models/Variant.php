<?php

namespace app\models;

use yii\db\ActiveRecord;
use Yii;

class Variant extends ActiveRecord
{

    public static function tableName()
    {
        return "{{%variant}}";
    }

    public function attributeLabels()
    {
        return [
            'cateid' => '所属分类',
            'title' => '属性名称',
            'varianttype' => '属性种类',
        ];
    }

    public function rules()
    {
        return [
            [['cateid','title','varianttype'], 'required','message' => '必填项'],
            ['cateid','number','min'=>1,'message' => '请选择一个分类'],
        ];
    }
    
    

    public function add($data)
    {
//        var_dump($data);exit;
        if ($this->load($data) && $this->save())
        {
            return true;
        }
        return false;
    }

}
