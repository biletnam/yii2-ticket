<?php

namespace bausch\ticket\assets;

use Yii;
use yii\web\AssetBundle;

class TicketAsset extends AssetBundle
{
	public $sourcePath = '@bower';
	public $css = [ 
        'vis/dist/vis.css', 
    ];
	public $js = [
        'vis/dist/vis.js'
    ];
}