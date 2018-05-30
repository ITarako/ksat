<?php
namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\User;
use common\models\Operations;
use common\lib\Utils;

/**
 * Replenish form
 */
class ReplenishForm extends Model
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
            ['email', 'exist', 'skipOnError' => true, 'targetClass' => '\common\models\User', 'message' => "This email doesn't exist."],

            ['value', 'required'],
            ['value', 'double', 'min' => 0.01],
        ];
    }


    public function replenish()
    {
        if (!$this->validate()) {
            return null;
        }

        $receiver = User::find()->where(['email'=>$this->email])->one();

        $operation = new Operations();
        $operation->value = Utils::penniesToInt($this->value);
        $operation->id_creator = Yii::$app->user->id;
        $operation->id_receiver = $receiver->id;

        $accReceiver = $receiver->account;
        $accReceiver->value += $operation->value;

        $transaction = Yii::$app->db->beginTransaction();

        try {
            if ($operation->save() && $accReceiver->save()) {
                $transaction->commit();
                return true;
            }
        } catch (\Throwable $e) {
            $transaction->rollBack();
        }

        return null;
    }
}
