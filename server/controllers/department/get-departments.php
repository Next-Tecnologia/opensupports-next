<?php
use RedBeanPHP\Facade as RedBean;

/**
 * @api {post} /department/get-department Get department list
 * @apiVersion 4.8.0
 *
 * @apiName Get department list
 *
 * @apiGroup department
 *
 * @apiDescription This path retrieves the list of departments.
 *
 * @apiPermission staff1
 *
 * @apiUse NO_PERMISSION
 * @apiUse INVALID_PAGE
 * @apiUse INVALID_ORDER
 *
 * @apiSuccess {Object} data
 * @apiSuccess {[Client](#api-Data_Structures-ObjectClient)[]} data.clients Array of clients found
 */

class GetDepartmentsController extends Controller {
    const PATH = '/get-departments';
    const METHOD = 'POST';

    public function validations() {
// changed of permission 'staff_1' to 'user'
        return[
            'permission' => 'user',
            'requestData' => []
        ];
    }

    public function handler() {
        $DepartmentList = $this->getDepartmentList();
        $DepartmentListArray = [];

        foreach ($DepartmentList as $department) {
            $DepartmentListArray[] = [
                'id' => $department->id,
                'name' => $department->name,
                'isFranchising' => $department->is_franchising
            ];
        }

        Response::respondSuccess([
            'departments' => $DepartmentListArray,
        ]);
    }

    private function getDepartmentList() {
        $user = Controller::getLoggedUser();
        $isStaff = Controller::request('isStaff');
        if ($user->id) { // added second condition
            if($isStaff == 'true') {
                $departmentQuery = 'SELECT `department`.`id`, `department`.`name`
                FROM `department_staff`
                INNER JOIN `staff` ON `staff`.`id` = `department_staff`.`staff_id`
                INNER JOIN `department` ON `department`.`id` = `department_staff`.`department_id`
                WHERE `staff`.`id` = ?';
                $departments = RedBean::getAll($departmentQuery, [$user->id]); // changed $userDepartment to $this->getUserDepartment()
                return RedBean::convertToBeans('department', $departments);
            }else {
                $departmentQuery = 'SELECT `department`.`id`, `department`.`name`
                FROM `user`
                INNER JOIN `client` ON `client`.`id` = `user`.`client_id`
                INNER JOIN `department` ON `department`.`id` = `client`.`department_id`
                WHERE `user`.`id` = ?';
                $departments = RedBean::getAll($departmentQuery, [$user->id]); // changed $userDepartment to $this->getUserDepartment()
                return RedBean::convertToBeans('department', $departments);   
            }
        }
        return [];
    }

}
