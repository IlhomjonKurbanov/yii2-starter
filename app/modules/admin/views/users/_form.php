<?php

use app\models\User;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * @var $this yii\web\View
 * @var $model \app\modules\admin\forms\UserForm
 * @var $form yii\widgets\ActiveForm
 */
?>

<div class="user-form box">
	
	<?php $form = ActiveForm::begin([
		'layout' => 'horizontal',
	]); ?>

	<div class="box-body">
		<?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>
		
		<?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>
		
		<?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
		
		<?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
		
		<?= $form->field($model, 'password')->passwordInput() ?>
		
		<?= $form->field($model, 'passwordRepeat')->passwordInput() ?>
		
		<?= $form->field($model, 'status')->dropDownList(User::getStatusesList()) ?>
		
		<?= $form->field($model, 'roles')->dropDownList(User::getRolesList(), [
			'multiple' => 'multiple',
		]) ?>
	</div>
	<div class="box-footer text-right">
		<?= Html::submitButton($model->isNewRecord ? 'Save' : 'Update', ['class' => 'btn btn-success']) ?>
	</div>
	
	<?php ActiveForm::end(); ?>
</div>
