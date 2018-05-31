<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\lib\Utils;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Operations';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-operations">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => "",
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'Value',
                'value' => function($operation) {
                    return Utils::intToPennies($operation->value);
                }
            ],
            'created_at',
            [
                'attribute' => 'Sender',
                'value' => function($operation) {
                    #return $operation->sender ? $operation->sender->user->email : null;
                    return $operation->senderUser ? $operation->senderUser->email : null;
                }
            ],
            [
                'attribute' => 'Receiver',
                'value' => function($operation) {
                    return $operation->receiverUser->email;
                }
            ],
            [
                'attribute' => 'Creator',
                'value' => function($operation) {
                    return $operation->creatorUser ? $operation->creatorUser->email : null;
                    #return $operation->creator ? $operation->creator->user->email : null;
                }
            ],
        ],
    ]); ?>
</div>
