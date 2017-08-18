<?php

namespace bausch\ticket\controllers;

use Yii;
use yii\web\Controller;

/**
 * Dashboard controller for the ticket module.
 */
class DashboardController extends Controller
{
	/**
	 * List open tickets.
	 * @return mixed
	 */
	public function actionIndex()
	{
		return $this->render('index');
	}
}
