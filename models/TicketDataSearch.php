<?php

namespace bausch\ticket\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use bausch\ticket\models\TicketData;

/**
 * TicketDataSearch represents the model behind the search form about `bausch\ticket\models\TicketData`.
 */
class TicketDataSearch extends TicketData
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ticket_data_id_pk', 'ticket_id_fk'], 'integer'],
            [['category', 'obj', 'val', 'extra', 'created_at', 'created_by'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = TicketData::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        $dataProvider->pagination->defaultPageSize=15;
        $dataProvider->sort = ['defaultOrder' => ['ticket_id_pk' => SORT_DESC]];
        
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'ticket_data_id_pk' => $this->ticket_data_id_pk,
            //'ticket_id_fk' => $this->ticket_id_fk,
            //'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'category', $this->category])
            ->andFilterWhere(['like', 'obj', $this->obj])
            ->andFilterWhere(['like', 'val', $this->val])
            ->andFilterWhere(['like', 'created_by', $this->created_by]);

        return $dataProvider;
    }
}
