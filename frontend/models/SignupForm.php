<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;
use common\models\Account;
use yii\web\ServerErrorHttpException;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
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

    public function signup(){
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();

        $rbac = Yii::$app->authManager;
        $notVerifiedRole = $rbac->getRole('not_verified');

        $transaction = Yii::$app->db->beginTransaction();

        try {
            if ($user->save()) {
                $rbac->assign($notVerifiedRole, $user->id);
                $transaction->commit();
            }
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw new ServerErrorHttpException();
        }

        try {
            $sended =  Yii::$app
                ->mailer
                ->compose(
                    ['html'=>'signup-html'],
                    ['auth_key' => $user->getAuthKey(), 'email'=>$user->email]
                )
                ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
                ->setTo($this->email)
                ->setSubject('Confirm your account for ' . Yii::$app->name)
                ->send();
            if($sended) {
                Yii::$app->session->setFlash('success', 'Check your email');
                return $user;
            }
        } catch (\Swift_TransportException $e){
            throw new ServerErrorHttpException();
        }
        return null;
    }
}
