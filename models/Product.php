<?php

namespace app\models;

use yii\db\ActiveRecord;
use app\models\ProductVariant;
use yii\db\Transaction;
use yii\web\UploadedFile;

class Product extends ActiveRecord
{
    public static function tableName()
    {
        return "{{%product}}";
    }

    public function rules()
    {
        return [
            ['cateid', 'number', 'min' => 1, 'message' => '必须选择一个分类'],
            ['title', 'required', 'message' => '标题不能为空'],
            [['ison', 'issale', 'ishot', 'istui'], 'in', 'range' => [0, 1], 'message' => '请选择至少一项'],
            ['title', 'filter', 'filter' => 'trim', 'skipOnArray' => true],
            [['desc', 'createtime', 'cover'], 'safe'],
        ];
    }

    public function add($data)
    {
        $pvdata = $data['Variant'];
        $specdata = $data['Specs'];
//        var_dump($specdata);exit;
        $data['Product']['createtime'] = time();
        $img = $data['Product']['cover'];
        $imgName = $this->md5Pic($img->baseName);
        $img->saveAs("uploads/" . $imgName . '.' . $img->extension);
        $data['Product']['cover'] = "uploads/" . $imgName . '.' . $img->extension;
        $tr = \Yii::$app->db->beginTransaction();
        try {
            if ($this->load($data) && $this->save()) {
                $productid = $this->productid;
                foreach ($pvdata as $k => $v) {
                    $vid = $k;
                    foreach ($v as $kk => $vv) {
                        $pv = new ProductVariant();
                        $pv->productid = $productid;
                        $pv->variantid = $vid;
                        $pv->variantnum = $kk;
                        $pv->variantvalue = $vv;
                        $pv->save();
                    }
                }
                $skus = $specdata['sku'];
                $tripids = $specdata['tripid'];
                $prices = $specdata['price'];
                foreach ($skus as $k => $sku) {
                    $ps = new ProductSpec();
                    $ps->productid = $productid;
                    $ps->sku = $sku;
                    $ps->tripid = $tripids[$sku];
                    $ps->price = $prices[$sku];
                    $pic = $specdata['pics'][$k];
                    $picName = $this->md5Pic($pic->baseName);
                    $picExt = $pic->extension;
                    $pic->saveAs("uploads/" . $picName . '.' . $picExt);
                    $ps->pic = "uploads/" . $picName . '.' . $picExt;
                    $ps->save();
                }
            }
            $tr->commit();
            return true;
        } catch (\Exception $exception) {
            $tr->rollBack();
            throw $exception;
            return false;
        }
    }

    public function updatePro($data, $productid)
    {
        $pvdata = $data['Variant'];
        $specdata = $data['Specs'];
//        var_dump($specdata);exit;
        $product = $this->find()->where('productid = :pid', [':pid' => $productid])->one();
        $tr = \Yii::$app->db->beginTransaction();
        try {
            if (!is_string($data['Product']['cover'])) {
                $img = $data['Product']['cover'];
                $imgName = $this->md5Pic($img->baseName);
                $img->saveAs("uploads/" . $imgName . '.' . $img->extension);
                $data['Product']['cover'] = "uploads/" . $imgName . '.' . $img->extension;
            }
            if ($product->load($data) && $product->save()) {
                ProductVariant::deleteAll('productid = :pid', [':pid' => $productid]);
                foreach ($pvdata as $k => $v) {
                    $vid = $k;
                    foreach ($v as $kk => $vv) {
                        $pv = new ProductVariant();
                        $pv->productid = $productid;
                        $pv->variantid = $vid;
                        $pv->variantnum = $kk;
                        $pv->variantvalue = $vv;
                        $pv->save();
                    }
                }
                $proSpecs = ProductSpec::find()->where('productid = :pid', [':pid' => $productid])->all();
                $specpics = array();
                foreach ($proSpecs as $proSpec) {
                    $specpics[$proSpec['sku']] = $proSpec['pic'];
                    $proSpec->delete();
                }
                if(count($specdata) < count($specpics)){
                    $flag = -1;
                }else{
                    $flag = 1;
                }
                $skus = $specdata['sku'];
                $tripids = $specdata['tripid'];
                $prices = $specdata['price'];
                foreach ($skus as $k => $sku) {
                    $ps = new ProductSpec();
                    $ps->productid = $productid;
                    $ps->sku = $sku;
                    $ps->tripid = $tripids[$sku];
                    $ps->price = $prices[$sku];
                    if (UploadedFile::getInstanceByName('Specs[pic][' . $sku . ']')) {
                        $pic = UploadedFile::getInstanceByName('Specs[pic][' . $sku . ']');
                        $picName = $this->md5Pic($pic->baseName);
                        $picExt = $pic->extension;
                        $pic->saveAs("uploads/" . $picName . '.' . $picExt);
                        $ps->pic = "uploads/" . $picName . '.' . $picExt;
                        if(array_key_exists($sku,$specpics)){
                            unlink($specpics[$sku]);
                            unset($specpics[$sku]);
                        }
                    } else {
                        $ps->pic = $specpics[$sku];
                        unset($specpics[$sku]);
                    }
                    $ps->save();
//                    var_dump($flag);exit;
                }
                if($flag == -1){
                    foreach($specpics as $specpic){
                        unlink($specpic);
                    }
                }
            }
            $tr->commit();
            return true;
        } catch (\Exception $exception) {
            $tr->rollBack();
            throw $exception;
        }
    }

    public function deletePro($productid){
        $product = Product::find('productid = :pid',[':pid' => $productid])->one();
        $prospecs = ProductSpec::find('productid = :pid',[':pid' => $productid])->all();
        $tr = \Yii::$app->db->beginTransaction();
        try{
            ProductVariant::deleteAll('productid = :pid',[':pid' => $productid]);
            if(!empty($prospecs)){
                foreach ($prospecs as $prospec){
                    unlink($prospec['pic']);
                    $prospec->delete();
                }
            }
            if(!is_null($product['cover'])){
                unlink($product['cover']);
                $product->delete();
            }
            $tr->commit();
            return true;
        }catch (\Exception $e){
            $tr->rollBack();
            throw $e;
            return false;
        }
    }

    public function md5Pic($name){
        $num = uniqid();
        return md5($name.$num);
    }


}