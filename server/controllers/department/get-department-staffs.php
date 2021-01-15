<?php
use RedBeanPHP\Facade as RedBean;

/**
 * @api {post} /department/get-department-staffs Get department list
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

class GetDepartmentStaffsController extends Controller {
    const PATH = '/get-department-staffs';
    const METHOD = 'POST';

    public function validations() {
// changed of permission 'staff_1' to 'user'
        return[
            'permission' => 'user',
            'requestData' => []
        ];
    }

    public function handler() {
        $staffList = $this->getStaffList();
        $staffListArray = [];

        foreach ($staffList as $department) {
            $staffListArray[] = [
                'id' => $department->id,
                'name' => $department->name,
            ];
        }

        Response::respondSuccess([
            'staffs' => $staffListArray,
        ]);
    }

    private function getStaffList() {
        $user = Controller::getLoggedUser();
        $departmentId = Controller::request('departmentId');
        if ($user->id) { // added second condition
            $departmentQuery = 'SELECT `staff`.`id`, `staff`.`name` from `staff`
            INNER JOIN `department_staff` ON `department_staff`.`staff_id` = `staff`.`id`
            WHERE `department_staff`.`department_id` = ?';
            $departments = RedBean::getAll($departmentQuery, [$departmentId]);
            return RedBean::convertToBeans('staff', $departments);
        }
        return [];
    }

}