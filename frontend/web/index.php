<?php

$currDomain = 'dathangtrung.vn';

//Check server name
$allowed_domains = array($currDomain);
if (!in_array($_SERVER['HTTP_HOST'], $allowed_domains, TRUE))
{
    $_SERVER['HTTP_HOST'] = $currDomain;
}	

// PHP - Removing a forward-slash from the tail-end of an URL
$serverRequestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';

//huynq - url browse có ký tự lạ đưa về 404
$current_url = strtolower($serverRequestUri);
$array_string_error = ['onbeforecopy','onbeforecut','onbeforepaste','oncopy','oncut','oninput','onkeydown','onkeypress','onkeyup','onpaste','textInput','onabort', 'onbeforeunload','onhashchange','onload','onoffline','ononline','onreadystatechange','onstop','onunload','onreset','onsubmit','onclick','oncontextmenu','ondblclick','onlosecapture','onmouseenter','onmousedown','onmouseleave','onmousemove','onmouseout','onmouseover','onmouseup','onmousewheel','onscroll','onmove','onmoveend','onmovestart','ondrag','ondragend','ondragenter','ondragleave','ondragover','ondragstart','ondrop','onresize','onresizeend','onresizestart','onactivate','onbeforeactivate','onbeforedeactivate','onbeforeeditfocus','onblur','ondeactivate','onfocus','onfocusin','onfocusout','oncontrolselect','onselect','onselectionchange','onselectstart','onafterprint','onbeforeprint',  'onhelp','onerror','onerrorupdate','onafterupdate','onbeforeupdate','oncellchange','ondataavailable','ondatasetchanged','ondatasetcomplete','onrowenter','onrowexit','onrowsdelete','onrowsinserted','onbounce','onfinish','onstart','onchange','onfilterchange','onpropertychange','onsearch','onmessage','CheckboxStateChange','DOMActivate','DOMAttrModified','DOMCharacterDataModified','DOMFocusIn','DOMFocusOut','DOMMouseScroll','DOMNodeInserted','DOMNodeInsertedIntoDocument','DOMNodeRemoved','DOMNodeRemovedFromDocument','DOMSubtreeModified','dragdrop','dragexit','draggesture','overflow','overflowchanged','RadioStateChange','underflow','FSCommand','onAbort','onActivate','onAfterPrint','onAfterUpdate','onBeforeActivate','onBeforeCopy','onBeforeCut','onBeforeDeactivate','onBeforeEditFocus','onBeforePaste','onBeforePrint','onBeforeUnload','onBeforeUpdate','onBegin','onBlur','onBouncewindow','onCellChange','onChange','onClick','onContextMenu','onControlSelect','onCopy','onCut','onDataAvailable','onDataSetChanged','onDataSetComplete','onDblClick','onDeactivate','onDrag','onDragEnd','onDragLeave','onDragEnter','onDragOver','onDragDrop','onDragStart','onDrop','onEnd','onError','onErrorUpdate','onFilterChange','onFinish','onFocus','onFocusIn','onFocusOut','onHashChange','onHelp','onInput','onKeyDown','onKeyPress','onKeyUp','onLayoutComplete','onLoad','onLoseCapture','onMediaComplete','onMediaError','onMessage','onMouseDown','onMouseEnter','onMouseLeave','onMouseMove','onMouseOut','onMouseOver','onMouseUp','onMouseWheel','onMove','onMoveEnd','onMoveStart','onOffline','onOnline','onOutOfSync','onPaste','onPause','onPopState','onProgress','onPropertyChange','onReadyStateChange','onRedo','onRepeat','onReset','onResize','onResizeEnd','onResizeStart','onResume','onReverse','onRowsEnter','onRowExit','onRowDelete','onRowInserted','onScroll','onSeek','onSelect','onSelectionChange','onSelectStart','onStart','onStop','onStorage','onSyncRestored','onSubmit','onTimeError','onTrackChange','onUndo','onUnload','onURLFlip','seekSegmentTime','behavior','xss:expression','javascript','alert','<script>','</script>','livescript','vbscript','<!--','-->','<?php','document.domain','sTYLe','script','<','>','%3C','%3E','%40','%23','%23','prompt','onmouse',')','(','('];


// foreach($array_string_error as $k=>$v){
//     if(substr_count($current_url,$v)){
//         header("Location: /404.html");
//         exit();
//     }
// }


function pr($str)
{
    echo "<pre>";
    print_r($str);
    echo "</pre>";
}

// ini_set('display_errors', 1);
// error_reporting(E_ALL);
// defined('YII_DEBUG') or define('YII_DEBUG', true);
// defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../../common/config/aliases.php');
include_once __DIR__ . '/../../common/helpers/PHPExcel.php';
include_once __DIR__ . '/../../common/helpers/GoogleTranslate.php';
include_once __DIR__ . '/../../common/helpers/recaptchalib.php';
include_once __DIR__ . '/../../common/config/constants.php';
$config = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../common/config/main.php'),
    require(__DIR__ . '/../../common/config/main-local.php'),
    require(__DIR__ . '/../config/main.php'),
    require(__DIR__ . '/../config/main-local.php')
);
$application = new yii\web\Application($config);
$application->run();

?>