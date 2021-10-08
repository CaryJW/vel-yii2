<?php
/* @var $verifyCode string */

use yii\helpers\Html;

?>
<style>
    .code {
        font-weight: bolder;
    }
</style>

您好，本次验证的验证码为：<span class="code"><?= Html::encode($verifyCode) ?></span>