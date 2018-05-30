<?php
namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\User;
use common\models\Account;
use yii\web\ServerErrorHttpException;

/**
 * Create form
 */
class CreateForm extends Model
{
    public $email;
    public $password;


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
            ['email', 'unique', 'skipOnError' => true, 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function create()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->status = User::STATUS_ACTIVE;

        $rbac = Yii::$app->authManager;
        $userRole = $rbac->getRole('user');

        $account = new Account();

        $transaction = Yii::$app->db->beginTransaction();

        try {
            if ($user->save()) {
                $rbac->assign($userRole, $user->id);
                $account->id_user = $user->id;
                if ($account->save()) {
                    $transaction->commit();
                    return $user;
                }
            }
        } catch (\Throwable $e) {
            $transaction->rollBack();
        }

        return null;
    }
}
