<?php

namespace gabordikan\cor4\datatables\traits;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use Yii;

/**
 * use this trait in your SearchModel to extend filters from DataTable with special characters
 */
Trait SearchModel
{
    public string $prefix;

    public function __construct($prefix = null)
    {
        $this->prefix = $prefix ?? $this->tableName();

        parent::__construct();
    }

    public function getIndexes()
    {
        return [
        ];
    }

    public function getColumns()
    {
        return [
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function addDefaultCondition($query) 
    {
        return $query;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search()
    {
        $query = self::getQuery();

        $params = Yii::$app->request->queryParams;

        if (!isset($_COOKIE[$this->prefix.'_search'])) {
            $_COOKIE[$this->prefix.'_search'] = [];
        }

        if (isset($params['search'])) {
            foreach ($params['search'] as $idx=>$searchText) {
                setcookie($this->prefix.'_search['.$idx.']', $searchText);
                $_COOKIE[$this->prefix.'_search'][$idx] = $searchText;
            }
        }

        // grid filtering conditions

        $searchText2 = "";
        $searchText3 = "";

        foreach($_COOKIE[$this->prefix.'_search'] as $idx=>$searchText) {
            if (strpos($searchText, '|')) { //Interval: from|to
                $intervals = explode('|', $searchText);
                $searchOperator = 'between';
                $searchText2 = $intervals[0];
                $searchText3 = $intervals[1];
            } else { //Modifiers
                switch (substr($searchText,0,1)) {
                    case '<':
                        $searchOperator = '<';
                        $searchText2 = substr($searchText,1);
                        break;
                    case '>':
                        $searchOperator = '>';
                        $searchText2 = substr($searchText,1);
                        break;
                    case '=':
                        $searchOperator = '=';
                        $searchText2 = substr($searchText,1);
                        break;
                    case '!':
                        if (substr($searchText,1,1) == "=") {
                            $searchOperator = '!=';
                            $searchText2 = substr($searchText,2);
                        } else {
                            $searchOperator = 'not like';
                            $searchText2 = substr($searchText,1);
                        }
                        break;
                    default:
                        $searchOperator = 'like';
                        $searchText2 = $searchText ?? '';
                    break;
                }
            }

            if (isset($this->getIndexes()[$idx])) { 
                if ($searchOperator == "between") {
                    $query->andWhere(
                        [$searchOperator,$this->getIndexes()[$idx], $searchText2, $searchText3]
                    );
                } else {
                    $query->andWhere(
                        [$searchOperator,$this->getIndexes()[$idx], $searchText2]
                    );
                }
            }
        }

        $query = $this->addDefaultCondition($query);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 25,
            ],
        ]);

        return $dataProvider;
    }

    public function getData($dataProvider)
    {
        $returnData = [];

        $data = $dataProvider->getModels();

        for($i=0; $i<count($data); $i++) {
            $arr = [];
            foreach ($this->getColumns() as $index=>$column) {
                if (strpos($column,'.') !== false) {
                    list($table,$col) = explode('.',$column);
                    $arr[] = [
                        $data[$i][$table][$col],
                    ];
                } else {
                    $arr[] = [
                        $data[$i][$column],
                    ];
                }
            }
            $returnData[] = $arr;
        }

        return $returnData;
    }
}
