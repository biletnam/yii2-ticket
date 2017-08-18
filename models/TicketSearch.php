<?php

namespace bausch\ticket\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use bausch\ticket\models\Ticket;

/**
 * TicketSearch represents the model behind the search form about `bausch\ticket\models\Ticket`.
 */
class TicketSearch extends Ticket
{
    //Variables not in the default Ticket model must be declared.
    public $param1; // area
    public $param2; // line | location
    public $param3; // station | section
    public $param4; // machine
    public $param5; // unit
      
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ticket_id_pk'], 'integer'],
            [
                [
                'process',
                'as_reported', 'as_determined', 'applied_fix',
                'machine_status', 'ticket_status',
                'created_at', 'created_by',
                'taken_at', 'taken_by',
                'closed_at', 'closed_by',
                'canceled_at', 'canceled_by',
                'ticket_id_fk',
                'param1', 'param2', 'param3', 'param4', 'param5', // custom search params
                ],
                'safe'
            ],
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
        $query = Ticket::find();
        $query->select('
            ticket.*,
            a.val AS val1,
            b.val AS val2,
            c.val AS val3,
            d.val AS val4,
            e.val AS val5
        ');
            //d.val AS val4,
        /*
        */
        //for param1
        //$query->leftJoin(['ticket_module.ticket_data a'])->AndOnCondition(['a.obj' => 'Area']);;
        //$query->leftJoin(['ticket_module.ticke/t_data b'])->AndOnCondition(['b.obj' => ['Line','Location']]);
        //$query->leftJoin(['ticket_module.ticket_data c'])->AndOnCondition(['c.obj' => ['Section','Station']]);
        //$query->leftJoin(['ticket_module.ticket_data d'])->AndOnCondition(['d.obj' => 'Area']);
        //$query->leftJoin(['ticket_module.ticket_data e'])->AndOnCondition(['a.obj' => 'Area']);;
        //$query->joinWith(['ticketData']);
        
        $query->joinWith([
            'ticketData a' => function ($query) {
                $query->andOnCondition(['a.category' => 'Location']);
            }]); //for param1
        $query->joinWith([
            'ticketData b' => function ($query) {
                $query->andOnCondition(['b.category' => 'Line']);
            }]); //for param2
        $query->joinWith([
            'ticketData c' => function ($query) {
                $query->andOnCondition(['c.category' => 'Machine']);
            }]); //for param3
        $query->joinWith([
            'ticketData d' => function ($query) {
                $query->andOnCondition(['d.category' => ['Section', 'Station']]);
            }]); //for param4
        $query->joinWith([
            'ticketData e' => function ($query) {
                $query->andOnCondition(['e.category' => 'Unit']);
            }]); //for param5
       
        $query->distinct(); // don't include dupes!
        
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'ticket_id_pk',
                ]
            ],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        
        //$dataProvider->sort->attributes['ticketdatavals'] = [
        //    'asc' => ['x.val' => SORT_ASC],
        //    'desc' => ['x.val' => SORT_DESC],
        //];

        $dataProvider->setSort([
            'defaultOrder' => [
                'ticket_id_pk' => SORT_DESC,
            ],
        ]);
        
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        /*
        $query->andFilterWhere([
            'ticket_id_pk' => $this->ticket_id_pk,
            'created_at' => $this->created_at,
            'taken_at' => $this->taken_at,
            'closed_at' => $this->closed_at,
            'canceled_at' => $this->canceled_at,
        ]);
        */
        
        $query->andFilterWhere(['like', 'process', $this->process])
            ->andFilterWhere(['like', 'as_reported', $this->as_reported])
            ->andFilterWhere(['like', 'as_determined', $this->as_determined])
            ->andFilterWhere(['like', 'applied_fix', $this->applied_fix])
            ->andFilterWhere(['like', 'machine_status', $this->machine_status])
            ->andFilterWhere(['like', 'ticket_status', $this->ticket_status])
            ->andFilterWhere(['like', 'created_by', $this->created_by])
            ->andFilterWhere(['like', 'taken_by', $this->taken_by])
            ->andFilterWhere(['like', 'closed_by', $this->closed_by])
            ->andFilterWhere(['like', 'canceled_by', $this->canceled_by])
            ->andFilterWhere(['like', 'a.val', $this->param1])
            ->andFilterWhere(['like', 'b.val', $this->param2])
            ->andFilterWhere(['like', 'c.val', $this->param3])
            ->andFilterWhere(['like', 'd.val', $this->param4])
            ->andFilterWhere(['like', 'e.val', $this->param5])
        ;
        

        return $dataProvider;
    }
}
