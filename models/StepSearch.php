<?php

namespace bausch\ticket\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use bausch\ticket\models\Step;

/**
 * StepSearch represents the model behind the search form about `bausch\ticket\models\Step`.
 */
class StepSearch extends Step
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['step_id_pk', 'workflow_id_fk'], 'integer'],
            [['step', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'safe'],
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
        $query = Step::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'step_id_pk' => $this->step_id_pk,
            'workflow_id_fk' => $this->workflow_id_fk,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'step', $this->step])
            ->andFilterWhere(['like', 'created_by', $this->created_by])
            ->andFilterWhere(['like', 'updated_by', $this->updated_by]);

        return $dataProvider;
    }
}
