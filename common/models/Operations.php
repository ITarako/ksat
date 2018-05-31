<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "operations".
 *
 * @property int $id
 * @property int $value
 * @property string $created_at
 * @property int $id_sender
 * @property int $id_receiver
 * @property int $id_creator
 *
 * @property Account $sender
 * @property Account $receiver
 * @property Account $creator
 */
class Operations extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'operations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['value', 'id_receiver'], 'required'],
            [['value', 'id_sender', 'id_receiver', 'id_creator'], 'default', 'value' => null],
            [['value', 'id_sender', 'id_receiver', 'id_creator'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['id_sender'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['id_sender' => 'id']],
            [['id_receiver'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['id_receiver' => 'id']],
            [['id_creator'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['id_creator' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'value' => 'Value',
            'created_at' => 'Datetime',
            'id_sender' => 'Id Sender',
            'id_receiver' => 'Id Receiver',
            'id_creator' => 'Id Creator',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSender()
    {
        return $this->hasOne(Account::className(), ['id' => 'id_sender']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReceiver()
    {
        return $this->hasOne(Account::className(), ['id' => 'id_receiver']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(Account::className(), ['id' => 'id_creator']);
    }

    public function afterFind()
    {
        parent::afterFind();

        $dateTime = new \DateTime($this->created_at);
        #$dateTime->setTimeZone(new \DateTimeZone('Europe/Moscow'));
        $this->created_at = $dateTime->format('Y-m-d H:i');
    }
}
