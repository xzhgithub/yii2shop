<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "cart".
 *
 * @property integer $id
 * @property integer $goods_id
 * @property integer $amount
 * @property integer $member_id
 * @property integer $status
 */
class Cart extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cart';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id', 'amount'], 'required'],
            [['goods_id', 'amount', 'member_id', 'status'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'goods_id' => '商品id',
            'amount' => '商品数量',
            'member_id' => '用户id',
            'status' => '状态',
        ];
    }

    //将cookie中的商品信息添加到数据库
    public function addCart($cart){
        //获取用户id
        $member_id=\Yii::$app->user->identity->getId();
        //循环，添加商品信息
        foreach($cart as $goods_id=>$amount){
            //添加时如果有该商品，就只增加数量
            $goods=Cart::findOne(['goods_id'=>$goods_id,'member_id'=>$member_id,'status'=>1]);
            if($goods){
                $goods->amount+=$amount;
                $goods->save();
            }else{
                $model=new Cart();
                $model->goods_id=$goods_id;
                $model->amount=$amount;
                $model->status=1;
                $model->member_id=$member_id;
                $model->save();
            }

        }
        return true;
    }

}
