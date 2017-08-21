<?php
/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\Markdown;

$this->title = 'Ticket System';
//$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
//$this->params['breadcrumbs'][] = $page;

//use yii\web\View;

/* @var $this View */

//echo $page;
?>

<div class="site-default-ticket">
    
	<?= $this->render('/_linkbar.php') ?>
	
	<div class='jumbotron'>
		<h2><?= Html::encode($this->title) ?></h2>
	</div>
	
</div>

<?php
	
    if (($pos = strrpos($page, '/')) === false) {
        $baseDir = '';
        $this->title = substr($page, 0, strrpos($page, '.'));
    } else {
        $baseDir = substr($page, 0, $pos) . '/';
        $this->title = substr($page, $pos + 1, strrpos($page, '.') - $pos - 1);
    }
    
    $file = "@bausch/ticket/{$page}";
    //$body = $this->render($file);
    $body = file_get_contents(Url::to($file));
	
	//echo $baseDir . '<br/>';
	//echo Url::current() . '<br/>';
	echo Url::to('@bausch/ticket/') . '<br/>';
	//echo Url::toRoute('ticket') . '<br/>';
    //Replace all the links in a markdown file and make them relative to module.
    $body = preg_replace_callback('/\]\((.*?)\)/', function($matches) use($baseDir) {
        $link = $matches[1];
        //echo $link;
		if (strpos($link, '://') === false) {
            if ($link[0] == '/') {
				//$link = Url::toRoute('ticket') . '/' . ltrim($link, '/');
                $link = Url::current(['page' => ltrim($link, '/')], true);
            } else {
                $link = Url::current(['page' => $baseDir . $link], true);
				//$link = Url::toRoute('ticket') . '/' . ltrim($link, '/');
            }
        }
		//echo '<br/>' . $link . '<br/>';
        return "]($link)";
    }, $body);
    
    echo Markdown::process($body, 'gfm');
