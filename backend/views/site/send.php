<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\SendForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\widgets\Alert;

$this->title = 'Send';
$this->params['breadcrumbs'][] = $this->title;
$alert = new Alert();
$alert->run();
?>

<div class="site-send">


    <h1><?= Html::encode($this->title) ?></h1>

    <p>Send your money to somebody:</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'form-send']); ?>

                <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

                <?= $form->field($model, 'value') ?>

                <div class="form-group">
                    <?= Html::submitButton('Send', ['class' => 'btn btn-primary', 'name' => 'send-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
