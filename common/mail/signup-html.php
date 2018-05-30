<?php
use yii\helpers\Html;
?>
<p>
    Verify your email at this
    <?= Html::a('link', \Yii::$app->urlManager->createAbsoluteUrl(['site/verify', 'auth_key' => $auth_key, 'email' => $email])) ?>
</p>