<?php

use yii\bootstrap\Html;
use yii\widgets\ActiveForm;

/**
 * @var $this yii\web\View
 * @var \GAuth\Auth $gAuth
 * @var array $action
 * @var string $codeParam
 */

$model = new \yii\base\DynamicModel([
    $codeParam => null
]);

echo Html::beginTag('div', ['class' => 'row']);
echo Html::beginTag('div', ['class' => 'col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-12']);
$form = ActiveForm::begin([
    'method' => 'POST',
    'options' => [
        'class' => 'form-vertical'
    ]
]);
?>

<?= $form->field($model, $codeParam, []) ?>

<div class="form-group">
    <div class="col-lg-offset-1 col-lg-11">
        <?= Html::submitButton(\Yii::t('yii2-totp', 'Submit'), ['class' => 'btn btn-primary']) ?>
    </div>
</div>

<?php

$form->end();

echo Html::endTag('div');
echo Html::endTag('div');