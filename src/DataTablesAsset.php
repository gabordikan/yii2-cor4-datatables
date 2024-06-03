<?php

namespace gabordikan\cor4\datatables;
use yii\web\AssetBundle;

class DataTablesAsset extends AssetBundle 
{
    public $sourcePath = '@bower';

    public $css = [
        "datatables.net-jqui/css/dataTables.jqueryui.min.css",
        "datatables.net-bs/css/dataTables.bootstrap.min.css",

        "datatables.net-buttons-jqui/css/buttons.jqueryui.min.css",
        "datatables.net-buttons-bs/css/buttons.bootstrap.min.css",
    ];

    public $js = [
        "datatables.net/js/jquery.dataTables.min.js",
        "datatables.net-jqui/js/dataTables.jqueryui.min.js",
        "datatables.net-bs/js/dataTables.bootstrap.min.js",

        "datatables.net-buttons/js/dataTables.buttons.min.js",
        "datatables.net-buttons/js/buttons.html5.min.js",
        "datatables.net-buttons/js/buttons.print.min.js",
        "datatables.net-buttons-jqui/js/buttons.jqueryui.min.js",
        "datatables.net-buttons-bs/js/buttons.bootstrap.min.js",

        "jszip/dist/jszip.min.js",
        "pdfmake/build/pdfmake.min.js",
        "pdfmake/build/vfs_fonts.js",
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}