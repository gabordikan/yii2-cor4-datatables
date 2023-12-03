<?php
/**
 * @copyright Gabor Dikan
 * @author Gabor Dikan <gabordikan@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php The MIT License (MIT)
 * @package yii2-cor4-datatable
 */
namespace gabordikan\cor4\datatables;
use yii\web\AssetBundle;

class DataTablesBootstrapAsset extends AssetBundle 
{
    public $sourcePath = '@bower/datatables-bootstrap3'; 

    public $css = [
        "BS3/assets/css/datatables.css",
    ];

    public $js = [
        "BS3/assets/js/datatables.js",
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}