<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "goods_photo".
 *
 * @property integer $id
 * @property integer $goods_id
 * @property string $img
 */
class GoodsPhoto extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods_photo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id'], 'integer'],
            [['img'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'goods_id' => '所属商品id',
            'img' => '图片地址',
        ];
    }
}
