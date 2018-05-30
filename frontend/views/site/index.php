<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use common\widgets\Alert;

$alert = new Alert();
$alert->run();

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Welcome!</h1>

        <p>
            <?= Html::a('Sign Up', ['/site/signup'], ['class'=>'btn btn-lg btn-success']) ?>
            <?= Html::a('Login', ['/site/login'], ['class'=>'btn btn-lg btn-success']) ?>
        </p>
    </div>
</div>
