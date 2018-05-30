<?php

namespace backend\services;

use Yii;
use common\models\User;
use common\models\Account;
use common\models\Operations;
use common\lib\Utils;
use yii\data\ActiveDataProvider;

class UserService
{
    public function show($user)
    {
        $account = $user->account;

        $sendedArr = Operations::find()->where('id_sender='.$account->id)->asArray()->all();
        if(empty($sendedArr)) {
            $sended = 0;
        } else {
            $sended = array_reduce($sendedArr, function($res, $item){
                $res += $item['value'];
                return $res;
            });
        }

        $receivedArr = Operations::find()->where('id_receiver='.$account->id)->asArray()->all();
        if(empty($receivedArr)) {
            $received = 0;
        } else {
            $received = array_reduce($receivedArr, function($res, $item){
                $res += $item['value'];
                return $res;
            });
        }

        $query = Operations::find()->where(['or', 'id_receiver='.$account->id, 'id_sender='.$account->id])->orderBy('id ASC');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false
        ]);

        return [$account, $sended, $received, $dataProvider];
    }
}