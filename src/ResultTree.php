<?php
/**
 * Created by JetBrains PhpStorm.
 * User: malte
 * Date: 23.12.12
 * Time: 22:02
 * To change this template use File | Settings | File Templates.
 */ 
class ResultTree {

    private $data;

    public function __construct() {
        $this->data = array();

    }

    public function addNode(array $node_data) {


        $this->data['nodes'][] = array(
            'name' => $node_data['guid'],
            'group' => substr($node_data['walkLevel'], 0, 8),
            'sumReshares' => $node_data['sumReshares'],
            'sumLikes' => $node_data['sumLikes'],
            'sumComments' => $node_data['sumComments'],
            'avatar' => $node_data['avatar'],
            'data' => $node_data['data']

        );


    }

    public function addLink(array $node_data) {
        if ($node_data['parent'] != '0') {




                $this->data['links'][] = array(
                    'target' => $this->find($node_data['guid']),
                    'source' => $this->find($node_data['parent']),
                    'value' => 1

                );


        }
    }


    public function getJson() {
        return json_encode($this->data);
    }

    function find($search) {

        $i = 0;
         foreach ($this->data['nodes'] as $node) {
            if ($node['name'] === $search) {
                return $i;

            }
            $i++;
        }
        return false;
    }

}