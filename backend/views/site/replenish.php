<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ReplenishForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\widgets\Alert;

$this->title = 'Replenish';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-replenish">


    <h1><?= Html::encode($this->title) ?></h1>

    <p>Replenish account of somebody:</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'form-replenish']); ?>

                <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

                <?= $form->field($model, 'value') ?>

                <div class="form-group">
                    <?= Html::submitButton('Replenish', ['class' => 'btn btn-primary', 'name' => 'replenish-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
