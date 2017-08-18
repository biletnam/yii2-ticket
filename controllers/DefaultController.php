<?php

namespace bausch\ticket\controllers;

use Yii;
use yii\web\Controller;
use yii\helper\Url;

/**
 * Default controller for the `ticket` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the markdown readme and guid files.
     * @return mixed
     */
    public function actionIndex($page = 'README.md')
    {
        return $this->render('index', ['page' => $page]);
    }
    
    /**
     * Renders the markdown readme and guid files.
     * @return mixed
     */
    public function actionTest()
    {
		/*
        */
        Yii::$app->response->format = 'pdf';
		// Rotate the page
		Yii::$container->set(Yii::$app->response->formatters['pdf']['class'], [
			'format' => [216, 280], // Legal page size in mm
			'orientation' => 'Portrait', // This value will be used when 'format' is an array only. Skipped when 'format' is empty or is a string
			//'beforeRender' => function($mpdf, $data) {},
        ]);
        //$this->layout = '//print';
		//return $this->render('test', []); 
        return $this->renderAjax('test', []); //No layout...
    }
}
