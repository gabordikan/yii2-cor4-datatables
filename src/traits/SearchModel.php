<?php

namespace gabordikan\cor4\datatables\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use Yii;

/**
 * MozgasSearch represents the model behind the search form of `app\models\Mozgas`.
 */
Trait SearchModel
{

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

        if (!isset($_COOKIE[$this->tableName().'_search'])) {
            $_COOKIE[$this->tableName().'_search'] = [];
        }

        if (isset($params['search'])) {
            foreach ($params['search'] as $idx=>$searchText) {
                setcookie($this->tableName().'_search['.$idx.']', $searchText);
                $_COOKIE[$this->tableName().'_search'][$idx] = $searchText;
            }
        }

        // grid filtering conditions

        $searchText2 = "";

        foreach($_COOKIE[$this->tableName().'_search'] as $idx=>$searchText) {
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
                    $searchOperator = 'not like';
                    $searchText2 = substr($searchText,1);
                    break;
                default:
                    $searchOperator = 'like';
                    $searchText2 = $searchText ?? '';
                break;
            }

            $query->andFilterWhere(
                [$searchOperator,$this->getIndexes()[$idx],$searchText2]
            );
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
