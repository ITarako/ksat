<?php

use yii\db\Migration;
use common\models\User;
use common\models\Account;

/**
 * Class m180525_062154_addRolesAndAdmin
 */
class m180525_062154_addRolesAndAdmin extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $rbac = \Yii::$app->authManager;

        $notVerifiedRole = $rbac->createRole('not_verified');
        $notVerifiedRole->description = 'Не подтвержденный пользователь';
        $rbac->add($notVerifiedRole);

        $userRole = $rbac->createRole('user');
        $userRole->description = 'Пользователь';
        $rbac->add($userRole);

        $adminRole = $rbac->createRole('admin');
        $adminRole->description = 'Администратор';
        $rbac->add($adminRole);

        $rbac->addChild($adminRole, $userRole);
        $rbac->addChild($userRole, $notVerifiedRole);

        $email = 'admin@admin.com';
        $password = 'adminadmin';

        $user = new User();
        $user->status = User::STATUS_ACTIVE;
        $user->email = $email;
        $user->setPassword($password);
        $user->generateAuthKey();

        $account = new Account();

        if($user->save()){
            $rbac->assign($adminRole, $user->id);
            $account->id_user = $user->id;
            $account->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        # TODO: delete Admin
        $manager = \Yii::$app->authManager;
        $manager->removeAll();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180525_062154_addRolesAndAdmin cannot be reverted.\n";

        return false;
    }
    */
}
