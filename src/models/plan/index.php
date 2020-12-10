<?PHP
require_once(__DIR__.'/../Model.php');
class Plan extends Model {
    public function __construct(){
        parent::__construct(__DIR__);
    }
    public function createPlan($name) {
        return $this->create("plan", 
        ["name" => ":name"],
        ["name" => $name])->lastInsertId();
    }

    public function createPlannedAssignment($planId, $startDate, $endDate, $assignId) {
        return $this->create("planned_assign", [
                "start_date" => ":start_date",
                "end_date" => ":end_date",
                "assign_id" => ":assign_id",
                "plan_id" => ":plan_id"
            ], [
                "start_date" => $startDate,
                "end_date" => $endDate,
                "plan_id" => $planId,
                "assign_id" => $assignId
            ]
        );
    }

    private function orderPlans($data) {
        return $this->orderData($data, [
            '_index' => 'plan_id',
            'plan_id' => 'id',
            'name' => 'name',
            'plan_updated_at' => 'updatedAt',
            'plannedAssignments' => [
                '_index' => 'planned_assign_id',
                'planned_assign_id' => 'id',
                'start_date' => 'startDate',
                'end_date' => 'endDate',
                'assignment' => [
                    '_index' => 'assign_id',
                    '_single' => TRUE,
                    'assign_id' => 'id',
                    'title' => 'title',
                    'description' => 'description'
                ] 
            ]
        ]);
    }

    public function fetchAllPlans() {
        $data = $this->fetchAll("fetchPlans.sql");
        return $this->orderPlans($data);
    }

    public function fetchPlanById($id) {
        $data = $this->fetchCustom("fetchPlans.sql", [equality('plan_id')], ['plan_id' => $id]);
        return $this->orderPlans($data)[0];
    }

    public function deletePlan($id) {
        return $this->delete('plan', [equality('plan_id')], ['plan_id' => $id]);
    }

    public function deletePlannedAssignment($id) {
        return $this->delete('planned_assign', [equality('planned_assign_id')], ['planned_assign_id' => $id]);
    }
}