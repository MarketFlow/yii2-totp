<?php

use yii\bootstrap\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var \MarketFlow\Yii2\TOTP\models\SecondFactorForm $model
 */

echo Html::beginTag('div', ['class' => 'row']);
echo Html::beginTag('div', ['class' => 'col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-12']);
$form = ActiveForm::begin([
    'method' => 'POST',
    'options' => [
        'class' => 'form-vertical'
    ]
]);
?>

<?= $form->field($model, 'code', []) ?>

<?php if ($model->isAttributeActive('rememberDevice')) { ?>
    <?= $form->field($model, 'rememberDevice', [])->checkbox() ?>
<?php } ?>

<div class="form-group">
    <?= Html::submitButton(\Yii::t('yii2-totp', 'Submit'), ['class' => 'btn btn-primary col-xs-12']) ?>
</div>

<?php

$form->end();

echo Html::endTag('div');
echo Html::endTag('div');