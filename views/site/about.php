<?php
use yii\helpers\Url;
use yii\helpers\Html;
use app\assets\AppAsset;

$depends = [\yii\web\JqueryAsset::className()];
$theme_url = Url::base() . '/static/node_modules';

$this->registerCssFile( $theme_url .'/@samhammer/jquery.zoomer/jquery.fs.zoomer.css', ['depends' => $depends]);
// $this->registerJsFile('/static/node_modules/jquery-zoom/jquery.zoom.min.js', ['depends' => $depends]);
// $this->registerJsFile( $theme_url .'/@samhammer/jquery.zoomer/jquery.fs.zoomer.js?v=0.002', ['depends' => $depends]);
$this->registerJsFile( Url::base() .'/static/js/jquery.fs.zoomer.min.js', ['depends' => $depends]);
$this->registerJsFile( Url::base() .'/static/js/jquery.doubletap.js', ['depends' => $depends]);


$this->title = 'About';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
.attachment .zoomer_wrapper { border-radius: 3px; height: 100%; margin: 0; overflow: hidden; width: 100%; }
.attachment .zoomer.dark_zoomer { background: rgba(0,0,0,0.5); }
.attachment .zoomer.dark_zoomer img { box-shadow: 0 0 5px rgba(0, 0, 0, 0.5); }
.attachment .zoomer .zoomer-controls {  }
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
    height: 70%;
    position: fixed;
    top: 15%;
    right: 0;
    bottom: 0;
    left: 0; 
    z-index: -9999;
}
</style>
<div id="site-about">
    <h1><?= Html::encode($this->title) ?></h1>
    <a href="#" class="zoomerClick">
        <img src="/static/img/tmp-dog.png" title="Test zoomer" alt="" />
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
        var zoomerOverlay = $("#popupZoomerOverlay")
        , popupZoomer = $("#popupZoomer");

        $('#site-about')
        .on('mouseup click', '.zoomerClick', function () {
            var src = $(this).find('img').attr('src');
            handleZoomer(src);
            $('#popupZoomer').css({ "visibility": "visible", "opacity": 1, "z-index": 9999 });
            $('#popupZoomerOverlay').show();
            return false;
        });

        $('#popupZoomerOverlay').on('click mouseup', function () {
            $('#popupZoomer').css({ "visibility": "hidden", "opacity": 0, "z-index": "-9999" });
            $(this).hide();
            $(".attachment .zoomer_basic").zoomer("destroy");
            return false;
        });

        popupZoomer.on('click', function (event) {
            var zoomerDiv = $(this);
            var target = $(event.target);
            if (!target.is(".zoomer-image")) {
                $(".attachment .zoomer_basic").zoomer("destroy");
                zoomerDiv.css({ 'visibility': 'hidden', 'opacity': '0', 'z-index': '-9999' });
                zoomerOverlay.css({ 'visibility': 'hidden', 'opacity': '0', 'z-index': '-9999' });
            }
            $("#noteList zoomerClick").each(function (index, elm) {
                var t = $(this);
                if (t.hasClass("hover")) {
                    t.removeClass("hover");
                }
            });
            return false;
        });
    });

    function handleZoomer(src) {
        var options = {
            callback: $.noop,
            controls: {
                position: "bottom",
                zoomIn: null,
                zoomOut: null,
                next: null,
                previous: null
            },
            customClass: "",
            enertia: 0.2,
            increment: 0.01,
            marginMin: 0, // Min bounds
            marginMax: 0, // Max bounds
            retina: false,
            tiled: true,
            source: src
        };
        $(".attachment .zoomer_basic").zoomer(options);

        $(window).on("resize", function(e) {
            $(".attachment .zoomer_wrapper").zoomer("resize");
        });

        $('.attachment').on('doubletap', '.zoomer', function () {
            var src = $(this).find('img').attr('src');
            $(".attachment .zoomer_basic").zoomer("destroy");
            handleZoomer(src);
            return false;
        });
    }
JS;
$this->registerJs($js, \yii\web\View::POS_END);