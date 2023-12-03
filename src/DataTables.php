<?php
/**
 * @copyright Gabor Dikan
 * @author Gabor Dikan <gabordikan@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php The MIT License (MIT)
 * @package yii2-cor4-datatables
 */

namespace gabordikan\cor4\datatables;

use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class DataTables extends \yii\grid\GridView {

    /**
    * @var array the HTML attributes for the container tag of the datatables view.
    * The "tag" element specifies the tag name of the container element and defaults to "div".
    * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
    */
    public $options = [];
    
    /**
    * @var array the HTML attributes for the datatables table element.
    * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
    */
    public $tableOptions = ["class"=>"table table-striped table-bordered","cellspacing"=>"0", "width"=>"100%"];
    
    /**
    * @var array the HTML attributes for the datatables table element.
    * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
    */
    public $clientOptions = [];

    public function init() {
        parent::init();
        
        //disable filter model by grid view
        $this->filterModel = null;
        
        //disable sort by grid view
        $this->dataProvider->sort = false;
        
        //disable pagination by grid view
        $this->dataProvider->pagination = false;
        
        //layout showing only items
        $this->layout = "{items}";
        
        //the table id must be set
        if (!isset($this->tableOptions['id'])) {
            $this->tableOptions['id'] = 'datatables_'.$this->getId();
        }
    }

    public function run() {
        $clientOptions = $this->getClientOptions();
        $view = $this->getView();
        $id = $this->tableOptions['id'];
        
        DataTablesBootstrapAsset::register($view);
        DataTablesAsset::register( $this->getView() );

        $options = Json::encode($clientOptions);

        $view->registerJs("cor4DataTables('#$id', $options);");
        
        //base list view run
        if ($this->showOnEmpty || $this->dataProvider->getCount() > 0) {
            $content = preg_replace_callback("/{\\w+}/", function ($matches) {
                $content = $this->renderSection($matches[0]);

                return $content === false ? $matches[0] : $content;
            }, $this->layout);
        } else {
            $content = $this->renderEmpty();
        }
        $tag = ArrayHelper::remove($this->options, 'tag', 'div');
        echo Html::tag($tag, $content, $this->options);

    }

    public function renderTableBody()
    {
        $models = array_values($this->dataProvider->getModels());
        if (count($models) === 0) {
            return "<tbody>\n</tbody>";
        } else {
            return parent::renderTableBody();
        }
    }

    /**
     * Returns the options for the datatables view JS widget.
     * @return array the options
     */
    protected function getClientOptions()
    {
        return $this->clientOptions;
    }
}

?>