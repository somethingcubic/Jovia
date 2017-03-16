<?php

namespace app\models;

use yii\db\ActiveRecord;
use Yii;
use yii\helpers\ArrayHelper;

class Category extends ActiveRecord
{

    public static function tableName()
    {
        return "{{%category}}";
    }

    public function attributeLabels()
    {
        return [
            'pid' => '上级分类',
            'title' => '分类名称',
        ];
    }

    public function rules()
    {
        return [
                ['pid', 'required', 'message' => '上级分类不能为空'],
                ['title', 'required', 'message' => '分类名称不能为空'],
                ['createtime', 'safe'],
        ];
    }

    public function add($data)
    {
        $data['Category']['createtime'] = time();
        if ($this->load($data) && $this->save())
        {
            return true;
        }
        return false;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $path = self::find()->where('cateid = :id', [':id' => $this->pid])->one();
        $this->path = ($this->pid == 0) ? '0,' . $this->cateid : $path['path'] . ',' . $this->cateid;
        $this->level = substr_count($this->path, ',');
        $this->updateAttributes(['path' => $this->path, 'level' => $this->level]);
    }

    public function getData()
    {
        $cates = self::find()->orderBy('path')->all();
        return $cates = ArrayHelper::toArray($cates);
    }

    public function getTree()
    {
        $data = $this->getData();
        foreach ($data as &$v)
        {
            $v['title'] = str_repeat("|------", $v['level']) . $v['title'];
        }
        return $data;
    }

    public function getOptions()
    {
        $data = $this->getTree();
        $options = ['请选择分类(默认为顶级分类)'];
        foreach ($data as $cate)
        {
            $options[$cate['cateid']] = $cate['title'];
        }
        return $options;
    }

}
