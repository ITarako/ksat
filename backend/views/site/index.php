<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use common\lib\Utils;
use common\widgets\Alert;
use \kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Administrative dashboard';
$alert = new Alert();
$alert->run();
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <p class="col-md-6">
            <?= Html::a('Create User', ['/user/create'], ['class' => 'btn btn-success']) ?>
            <?= Html::a('Operations list', ['operations'], ['class' => 'btn btn-success']) ?>
        </p>
    </div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary'=> "",
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'Email',
                'value' => function($user) {
                    return $user->email;
                }
            ],
            [
                'attribute' => 'Balance',
                'value' => function($user) {
                    return Utils::intToPennies($user->account->value);
                }
            ],
            [
                'attribute' => 'Actions',
                'format' => 'raw',
                'value' => function($model) {
                    return Html::a('Replenish', ['/site/replenish', 'email' => $model['email']], ['class' => 'btn btn-success'])
                        .' '. Html::a('Send', ['/site/send', 'email' => $model['email']], ['class' => Yii::$app->user->identity->email===$model['email'] ? 'btn btn-success disabled':'btn btn-success'])
                        .' '. Html::a('Show', ['/user/show', 'email' => $model['email']], ['class' => 'btn btn-success'])
                        .' '. Html::a('Update', ['/user/update', 'email' => $model['email']], ['class' => 'btn btn-success']);
                }
            ],
        ],
    ]); ?>
</div>
