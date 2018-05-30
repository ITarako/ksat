<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\grid\GridView;
use common\lib\Utils;

$this->title = 'show';
$this->params['breadcrumbs'][] = $this->title;
$balance = 0;
?>
<div class="site-show">

        <div class="panel panel-default col-md-8">
            <h3 class="panel panel-heading">
                <?=$account->user->email?> account:
            </h4>
            <div class="panel-body row">
                <h4 class="col-md-4 text-primary">Balance: <?= Utils::intToPennies($account->value)?> RUB</h4>
                <h4 class="col-md-4 text-danger">Sended: <?= Utils::intToPennies($sended)?> RUB</h4>
                <h4 class="col-md-4 text-success">Received: <?= Utils::intToPennies($received)?> RUB</h4>
            </div>
        </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary'=> "",
        'rowOptions' => function ($model) {
            if (!empty($model->creator)) {
                return ['class' => 'warning'];
            }
        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'Value',
                'value' => function($operation) {
                    return Utils::intToPennies($operation->value);
                }
            ],
            [
                'attribute' => 'Balance',
                'value' => function($operation) use (&$balance, $account) {
                    if ($operation->receiver->id === $account->id) {
                        $balance += $operation->value;
                    } else {
                        $balance -= $operation->value;
                    }
                    return Utils::intToPennies($balance);
                }
            ],
            'created_at',
            [
                'attribute' => 'From',
                'value' => function($operation) {
                    return $operation->sender ? $operation->sender->user->email : $operation->creator->user->email;
                }
            ],
            [
                'attribute' => 'To',
                'value' => function($operation) {
                    return $operation->receiver->user->email;
                }
            ],
        ],
    ]); ?>


</div>
