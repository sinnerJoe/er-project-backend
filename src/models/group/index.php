<?php
require_once(__DIR__.'/../Model.php');
class Group extends Model {
    public function __construct(){
        parent::__construct(__DIR__);
    }
    public function getShallowGroups($year) {
        $data = $this->fetchCustom('getGroups.sql', [equality('ed_year')], ['ed_year' => $year]);

        return $this->orderData($data, [
            '_index' => 'college_group_id',
            'college_group_id' => 'id',
            'ed_year' => 'year',
            'name' => 'name'
        ]);
    }

    public function getGroups($year) {
        $data = $this->fetchCustom('getGroups.sql', [equality('ed_year')], ['ed_year' => $year]);

        return $this->orderData($data, [
            '_index' => 'college_group_id',
            'college_group_id' => 'id',
            'ed_year' => 'year',
            'name' => 'name',
            'coordinator' => [
                '_index' => 'coord_user_id',
                '_single' => TRUE,
                'coord_user_id' => 'id',
                'coord_first_name' => 'firstName',
                'coord_last_name' => 'lastName',
                'coord_email' => 'email'
            ],
            'students' => [
                '_index' => 'user_id',
                'user_id' => 'id',
                'first_name' => 'firstName',
                'last_name' => 'lastName',
                'email' => 'email',
                'created_at' => 'createdAt'
            ],
            'plan' => [
                '_index' => 'plan_id',
                '_single' => TRUE,
                'plan_id' => 'id',
                'plan_name' => 'name'
            ]
        ]);
    }

    public function createGroup($year, $name) {
        $this->create('college_group', 
            ['ed_year' => ':year', 'name' => ':name'], 
            ['year' => $year, 'name' => $name]
        );
    }

    public function deleteGroup($id) {
        $this->delete('college_group', [equality('college_group_id', ':id')], ['id' => $id]);
    }

    public function changePlan($groupId, $planId) {
        $this->update('college_group', 
            ['plan_id' => ':pid'], 
            [equality('college_group_id', ':id')],
            ['id' => $groupId, 'pid' => $planId]
        );
    }

    public function changeCoordinator($groupId, $coordinatorId) {
        $this->update('college_group', 
            ['coordinator_id' => ':cid'], 
            [equality('college_group_id', ':id')],
            ['id' => $groupId, 'cid' => $coordinatorId]
        );
    }
}