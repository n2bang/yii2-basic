<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<!--div class="site-index">

    <div class="jumbotron">
        <h1>Congratulations!</h1>

        <p class="lead">You have successfully created your Yii-powered application.</p>

        <p><a class="btn btn-lg btn-success" href="http://www.yiiframework.com">Get started with Yii</a></p>
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-4">
                <h2>Heading</h2>

                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et
                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip
                    ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu
                    fugiat nulla pariatur.</p>

                <p><a class="btn btn-default" href="http://www.yiiframework.com/doc/">Yii Documentation &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2>Heading</h2>

                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et
                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip
                    ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu
                    fugiat nulla pariatur.</p>

                <p><a class="btn btn-default" href="http://www.yiiframework.com/forum/">Yii Forum &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2>Heading</h2>

                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et
                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip
                    ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu
                    fugiat nulla pariatur.</p>

                <p><a class="btn btn-default" href="http://www.yiiframework.com/extensions/">Yii Extensions &raquo;</a></p>
            </div>
        </div>

    </div>
</div-->

<?php
use yii\helpers\Url;
use yii\helpers\Html;
use app\assets\AppAsset;

$depends = [\yii\web\JqueryAsset::className()];
$theme_url = Url::base() . '/static/node_modules';

// $this->registerCssFile( $theme_url .'/formstone/dist/css/viewer.css', ['depends' => $depends]);
$this->registerJsFile('/static/node_modules/jquery-zoom/jquery.zoom.min.js', ['depends' => $depends]);
$this->registerJsFile( Url::base() .'/static/js/jquery.doubletap.js', ['depends' => $depends]);

$this->title = 'Home';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
.attachment .zoomer_wrapper { border: 1px solid #ddd; border-radius: 3px; height: 300px; margin: 0; overflow: hidden; width: 100%; }
.attachment .zoomer.dark_zoomer { background: #333; }
.attachment .zoomer.dark_zoomer img { box-shadow: 0 0 5px rgba(0, 0, 0, 0.5); }
.attachment .zoomer .zoomer-controls { display: none; }
#popupZoomerOverlay { position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0; 
    background: rgba(0,0,0,0.5);
    z-index: 9998;
    display: none;
}
#popupZoomer { 
    visibility: hidden; opacity: 0;
    height: 300px; background: white; 
    position: fixed;
    top: 30%;
    right: 0;
    bottom: 0;
    left: 0; 
    z-index: -9999;
}
</style>
<div id="site-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <a href="#" class="zoom">
        <img src="/static/img/tmp-dog.png" alt="" />
    </a>
    
</div>
<div id="popupZoomerOverlay"></div>
<div id="popupZoomer" class="attachment">
    <div class="zoomer_wrapper zoomer_basic">
    </div>
</div>
<?php
$js = <<<JS
    $(document).ready(function() {
        /*
        $('#site-index').on('touchstart', '.zoomerClick', function () {
            $(this).data('moved', '0');
        })
        .on('mousemove touchmove', '.zoomerClick', function () {
            $(this).data('moved', '1');
        })
        .on('mouseup touchend', '.zoomerClick', function () {
            if ($(this).data('moved') == 0) {
                var src = $(this).find('img').attr('src');
                handleZoomer(src);
                $('#popupZoomer').css({ "visibility": "visible", "opacity": 1 });
                $('#popupZoomerOverlay').show();
            }
            return false;
        });
        */

        $('#site-index').on('click', '.zoom', function () {
            var src = $(this).find('img').attr('src');
            handleZoomer(src);
            $('#popupZoomer').css({ "visibility": "visible", "opacity": 1, "z-index": 9999 });
            $('#popupZoomerOverlay').show();
            return false;
        });

        $('#popupZoomerOverlay').on('click', function () {
            $('#popupZoomer').css({ "visibility": "hidden", "opacity": 0 });
            $(this).hide();
            //$(".attachment .zoomer_basic").zoom("zoom.destroy");
            return false;
        });
    });

    function handleZoomer(src) {
        var options = {
            callback: false,
            onZoomIn: false,
            onZoomOut: false,
            touch: true,
            url: src
        };
        $(".attachment .zoomer_basic").zoom(options);
        $(".zoomImg").css({"opacity": 1});

        // $(window).on("resize", function(e) {
        //     $(".attachment .zoomer_wrapper").zoom("resize");
        // });

        $('.attachment').on('doubletap', '.zoomer', function () {
            var src = $(this).find('img').attr('src');
            $(".attachment .zoomer_basic").zoom("zoom.destroy");
            handleZoomer(src);
            return false;
        });
    }
JS;
$this->registerJs($js, \yii\web\View::POS_END);