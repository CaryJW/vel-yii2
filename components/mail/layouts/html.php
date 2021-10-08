<?php
/* @var $content string */

/* @var $title string */

use yii\helpers\Html;

?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= Html::encode($title) ?></title>
</head>
<body>
<?php $this->beginBody() ?>
<div class="main">
    <?= $content ?>
</div>
<?php $this->endBody() ?>

</body>
</html>
<?php $this->endPage() ?>
