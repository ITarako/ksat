<?php
namespace common\models;

use Yii;
use yii\base\Model;
use common\lib\Utils;

class SendForm extends Model
{
    public $email;
    public $value;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'exist', 'skipOnError' => true, 'targetClass' => '\common\models\User', 'message' => "This email doesn't exists."],

            ['value', 'required'],
            ['value', 'double', 'min' => 0.01],
        ];
    }

    public function send()
    {
        if (!$this->validate()) {
            return null;
        }

        if (Yii::$app->user->identity->email === $this->email) {
            Yii::$app->session->setFlash('error', 'You can not send money to yourself');
            return null;
        }
        $receiver = User::findByEmail($this->email)->account;
        $sender = Yii::$app->user->identity->account;

        $operation = new Operations();
        $operation->value = Utils::penniesToInt($this->value);
        $operation->id_sender = $sender->id;
        $operation->id_receiver = $receiver->id;

        $senderBalanceAfterActions = $sender->value - $operation->value;
        if($senderBalanceAfterActions < 0) {
            Yii::$app->session->setFlash('error', 'You do not have enough money');
            return null;
        }
        $sender->value = $senderBalanceAfterActions;
        $receiver->value += $operation->value;

        $transaction = Yii::$app->db->beginTransaction();

        try {
            if ($operation->save() && $sender->save() && $receiver->save()) {
                $transaction->commit();
                return true;
            }
        } catch (\Throwable $e) {
            $transaction->rollBack();
        }

        return null;
    }
}
