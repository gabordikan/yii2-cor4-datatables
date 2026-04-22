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
use yii\helpers\Url;
use Yii;

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
        
        //layout showing only items
        $this->layout = "{items}";
        
        //the table id must be set
        if (!isset($this->tableOptions['id'])) {
            $this->tableOptions['id'] = 'datatables_'.$this->getId();
        }
    }

    public function run() {
        $request = \Yii::$app->request;
        
        // DataTables Server-side AJAX kérés lekezelése
        if ($request->isAjax && $request->get('draw')) {
            \Yii::$app->response->clearOutputBuffers();
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $draw = $request->get('draw');
            $start = $request->get('start', 0);
            $length = $request->get('length', 25);

            // Lapozás (LIMIT/OFFSET) beállítása a DataProvider-en
            $this->dataProvider->pagination = [
                'pageSize' => $length == -1 ? 0 : $length,
                'page' => $length > 0 ? floor($start / $length) : 0,
            ];

            // Újra előkészítjük a lekérdezést az új limit/offset értékekkel
            $this->dataProvider->prepare(true);

            $models = $this->dataProvider->getModels();
            $keys = $this->dataProvider->getKeys();

            $data = [];
            foreach ($models as $index => $model) {
                $key = $keys[$index];
                $rowData = [];
                // Végigmegyünk a GridView definiált oszlopain
                foreach ($this->columns as $column) {
                    $tdHtml = $column->renderDataCell($model, $key, $index);
                    // Kinyerjük csak a cella belső HTML tartalmát a <td> tag-ek közül
                    if (preg_match('/<td[^>]*>(.*)<\/td>/is', $tdHtml, $matches)) {
                        $rowData[] = $matches[1];
                    } else {
                        $rowData[] = $tdHtml;
                    }
                }
                $data[] = $rowData;
            }

            $totalCount = $this->dataProvider->getTotalCount();

            // JSON válasz a DataTables-nek
            \Yii::$app->response->data = [
                'draw' => (int)$draw,
                'recordsTotal' => $totalCount,
                'recordsFiltered' => $totalCount, // A szűrést a SearchModel végzi a URL alapján
                'data' => $data,
            ];
            \Yii::$app->response->send();
            exit();
        }

        $clientOptions = $this->getClientOptions();

        if (!isset($clientOptions['url']) || !$clientOptions['url'])
        {
            $clientOptions['url'] = Url::base(); 
        }

        // Beállítjuk a JS számára a Server-side paramétereket
        $clientOptions['serverSide'] = true;
        $clientOptions['ajax'] = Url::current(); // AJAX a jelenlegi URL-re a paraméterekkel együtt

        if (!isset($clientOptions['prefix']) || !$clientOptions['prefix'])
        {
            $clientOptions['prefix'] = 'datatable';
            $models = $this->dataProvider->getModels();
            if (count($models) > 0) {
                $clientOptions['prefix'] =  $models[0]->prefix;
            }
        }

        $view = $this->getView();
        $id = $this->tableOptions['id'];

        DataTablesAsset::register($view);
        DataTablesCor4Asset::register($view);

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
