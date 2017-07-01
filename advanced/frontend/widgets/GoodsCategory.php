<?php
use yii\helpers\Html;

foreach($models as $model){

    if($model->parent_id==0){
        echo '<div class="cat">';
        echo '<h3>'.Html::a($model->name,['goods/list','category_id'=>$model->id]).'<b></b></h3>';
        echo '<div class="cat_detail">';
        foreach($models as $first){

            if($first->parent_id==$model->id){

                echo '<dl class="dl_1st">';
                echo '<dl">';

                echo '<dt>'.Html::a($first->name,['goods/list','category_id'=>$first->id]).'</dt>';
//										var_dump(3);exit;

                foreach($models as $tow){
                    if($tow->parent_id==$first->id){
                        echo '<dd>'.Html::a($tow->name,['goods/list','category_id'=>$tow->id]).'</dd>';
                    }
                }
                echo '</dl>';
                echo '</dl>';

            }


        }
        echo '</div>';
        echo '</div>';
    }



}