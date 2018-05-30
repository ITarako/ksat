<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\widgets\Alert;
use common\lib\Utils;

$this->title = 'Update User';
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$alert = new Alert();
$alert->run();

$role = Utils::getRoleById($user->id);
?>
<div class="user-update">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Please fill out the following fields to update user:</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'form-update']); ?>

                <?= $form->field($model, 'email')->textInput() ?>

                <?= $form->field($model, 'newEmail')->textInput(['autofocus' => true]) ?>

                <?= $form->field($model, 'newPassword')->passwordInput() ?>

                <div class="form-group">
                    <?= Html::label('Role', 'role', ['class'=>'control-label']) ?>
                    <?= Html::radioList('role', [$role], ['user' => 'User', 'admin'=>'Admin']) ?>
                </div>

                <div class="form-group">
                    <?= Html::submitButton('Update', ['class' => 'btn btn-primary', 'name' => 'update-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
