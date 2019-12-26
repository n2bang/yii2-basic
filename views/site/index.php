<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>

<?php
use yii\helpers\Url;
use yii\helpers\Html;
use app\assets\AppAsset;

$depends = [\yii\web\JqueryAsset::className()];
$theme_url = Url::base() . '/static/node_modules';

// $this->registerCssFile( $theme_url .'/formstone/dist/css/viewer.css', ['depends' => $depends]);
$this->registerJsFile('/static/node_modules/hammerjs/hammer.min.js', ['depends' => $depends]);
$this->registerJsFile( Url::base() .'/static/js/jquery.doubletap.js', ['depends' => $depends]);

$this->title = 'Home';
$this->params['breadcrumbs'][] = $this->title;
?>
<style type="text/css">
.demo-box {
  width: 300px;
  height: 200px;
  overflow: hidden;
  position: relative;
}
.demo-box > img {
  height: 400px;
  width: 1900px;
  position: absolute;
  margin-left: -950px;
  display: none;
  pointer-events: none;
  margin-top: -220px;
  left: 50%;
  top: 50%;
  max-width: none;
}
.demo-box-wrap, .target-wrap {
  border: 1px solid #333;
  background: #333;
  width: 300px;
  height: 200px;
  margin-left: auto;
  margin-right: auto;
}
.demo-box img.active {
  display: block;
}
</style>

<div id="site-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="demo-box-wrap">
        <div class="demo-box pinch">
            <img class="active" src="https://hammerjs.github.io/assets/img/pano-1.jpg">
        </div>
    </div>
</div>

<div id="popupZoomerOverlay"></div>
<div id="popupZoomer" class="attachment">
    <div class="zoomer_wrapper zoomer_basic">
    </div>
</div>
<?php
$js = <<<JS
    $(document).ready(function() {
        var el = document.querySelector(".pinch");
        var ham = new Hammer( el, {
            domEvents: true
        } );
        var width = 1900;
        var height = 400;
        var left = 950;
        var top = 220;
        ham.get('pinch').set({ enable: true });
        ham.on( "pinch", function( e ) {
            console.log( "pinch" );
            if ( width * e.scale >= 300 ) {
                var img = el.childNodes[1];
                img.style.width = (width * e.scale) + 'px';
                img.style.marginLeft = (-left * e.scale) + 'px';
                img.style.height = (height * e.scale) + 'px';
                img.style.marginTop = (-top * e.scale) + 'px';
            }
            console.log( e.scale );
        } );

        ham.on( "pinchend", function( e ) {
            width = width * e.scale;
            height = height * e.scale;
            left = left * e.scale;
            top = top * e.scale;
            console.log( width );
        } );
    });

JS;
$this->registerJs($js, \yii\web\View::POS_END);