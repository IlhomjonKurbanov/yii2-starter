<?php

namespace justcoded\yii2\rbac\controllers;

use justcoded\yii2\rbac\forms\PermissionForm;
use Yii;
use app\traits\controllers\FindModelOrFail;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\web\Controller;
use justcoded\yii2\rbac\models\Permission;
use justcoded\yii2\rbac\models\ItemSearch;

/**
 * PermissionsController implements the CRUD actions for AuthItems model.
 */
class PermissionsController extends Controller
{
	use FindModelOrFail;

	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [
			'verbs' => [
				'class'   => VerbFilter::className(),
				'actions' => [
					'delete' => ['POST'],
				],
			],
		];
	}

	/**
	 * @return string
	 */
	public function actionIndex()
	{
		$searchModel = new ItemSearch();
		$dataProviderRoles = $searchModel->searchRoles(Yii::$app->request->queryParams);

		$dataProviderPermissions = $searchModel->searchPermissions(Yii::$app->request->queryParams);

		return $this->render('/index', [
			'searchModel' => $searchModel,
			'dataProviderRoles' => $dataProviderRoles,
			'dataProviderPermissions' => $dataProviderPermissions,
		]);
	}

	/**
	 * @return \yii\web\Response
	 */
	public function actionScanRoutes()
	{
		$file = '@app/../yii';
		$action = 'rbac/scan';

		$cmd = PHP_BINDIR . '/php ' . Yii::getAlias($file) . ' ' . $action;
		pclose(popen($cmd . ' > /dev/null &', 'r'));

		Yii::$app->session->setFlash('success', 'Routes scanned success.');

		return $this->redirect(['index']);
	}

	/**
	 * @return array|string|Response
	 */
	public function actionCreate()
	{
		$model = new PermissionForm();
		$model->scenario = $model::SCENARIO_CREATE;

		if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
			Yii::$app->response->format = Response::FORMAT_JSON;

			return ActiveForm::validate($model);
		}

		if ($model->load(Yii::$app->request->post())){
			$permission = new Permission();

			if($permission->store($model)) {
				Yii::$app->session->setFlash('success', 'Permission saved success.');
			}

			return $this->redirect(['index']);
		}

		return $this->render('create', [
			'model' => $model
		]);
	}

	/**
	 * @param $name
	 * @return array|string|Response
	 */
	public function actionUpdate($name)
	{
		$perm = Yii::$app->authManager->getPermission($name);
		$model = new PermissionForm($perm);

		if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
			Yii::$app->response->format = Response::FORMAT_JSON;
			return ActiveForm::validate($model);
		}

		if ($model->load(Yii::$app->request->post())){
			$role = new Permission();

			if($role->store($model)) {
				Yii::$app->session->setFlash('success', 'Permission saved success.');
			}

			return $this->redirect(['index']);
		}

		return $this->render('update', [
			'model' => $model
		]);
	}

	/**
	 * @param $name
	 * @return Response
	 */
	public function actionDelete($name)
	{
		if(!$post_data = Yii::$app->request->post('PermissionForm')){
			return $this->redirect(['index']);
		}

		$role = Yii::$app->authManager->getPermission($post_data['name']);

		if (Yii::$app->authManager->remove($role)){
			Yii::$app->session->setFlash('success', 'Permission removed success.');
		}else{
			Yii::$app->session->setFlash('error', 'Permission not removed.');
		}

		return $this->redirect(['index']);
	}
}

