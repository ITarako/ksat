<?php
namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\User;
use common\lib\Utils;
use yii\web\ServerErrorHttpException;

/**
 * Update form
 */
class UpdateForm extends Model
{
    public $email;
    public $newEmail;
    public $newPassword;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['email', 'required'],
            [['email', 'newEmail'], 'trim'],
            [['email', 'newEmail'], 'email'],
            [['email', 'newEmail'], 'string', 'max' => 255],
            ['newEmail', 'unique', 'skipOnError' => true, 'targetClass' => '\common\models\User', 'targetAttribute' => ['newEmail' => 'email'], 'message' => 'This email address has already been taken.'],
            ['email', 'exist', 'skipOnError' => true, 'targetClass' => '\common\models\User', 'message' => 'This email address does not exist.'],
            ['newPassword', 'string'],

        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function update($role)
    {
        if (!$this->validate()) {
            return null;
        }
        if($this->newPassword && strlen($this->newPassword)<6) {
            Yii::$app->session->setFlash('error', 'Min lenght of password is 6 characters');
            return null;
        }

        $user = User::findByEmail($this->email);
        if($this->newEmail) {
            $user->email = $this->newEmail;
        }
        if($this->newPassword) {
            $user->setPassword($this->newPassword);
        }

        $newRoleName = $role;
        $oldRoleName = Utils::getRoleById($user->id);

        $rbac = Yii::$app->authManager;
        $newRole = $rbac->getRole($newRoleName);
        $oldRole = $rbac->getRole($oldRoleName);

        $transaction = Yii::$app->db->beginTransaction();

        try {
            if ($user->save()) {
                $rbac->revoke($oldRole, $user->id);
                $rbac->assign($newRole, $user->id);
                $transaction->commit();
                return $user;
            }
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }

        return null;
    }
}
