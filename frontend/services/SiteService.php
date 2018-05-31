<?php

namespace frontend\services;

use Yii;
use common\models\User;
use common\models\Account;
use common\models\Operations;
use common\lib\Utils;
use yii\data\ActiveDataProvider;

class SiteService
{
    public function verify($auth_key, $email)
    {
        $user = User::find()->where(['email'=>$email])->one();

        if(empty($user) || !$user->validateAuthKey($auth_key)) {
            Yii::$app->session->setFlash('error', 'Auth key is invalid');
            return null;
        }

        if($user->status === User::STATUS_ACTIVE) {
            Yii::$app->session->setFlash('error', "User @email already authorized");
            return null;
        }

        $user->status = User::STATUS_ACTIVE;
        $account = new Account();
        $account->id_user = $user->id;

        $rbac = Yii::$app->authManager;
        $notVerifiedRole = $rbac->getRole('not_verified');
        $userRole = $rbac->getRole('user');

        $transaction = Yii::$app->db->beginTransaction();

        try {
            if($user->save() && $account->save()) {
                $rbac->revoke($notVerifiedRole, $user->id);
                $rbac->assign($userRole, $user->id);
                $transaction->commit();
                return $user;
            }
        } catch (\Throwable $e) {
            $transaction->rollBack();
        }

        return null;
    }

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

        $query = Operations::find()->where(['or', 'id_receiver='.$account->id, 'id_sender='.$account->id])->orderBy('id ASC')->with('senderUser', 'receiverUser', 'creatorUser');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false
        ]);

        return [$account, $sended, $received, $dataProvider];
    }
}