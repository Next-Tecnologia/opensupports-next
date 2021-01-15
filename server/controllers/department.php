<?php
$departmentControllers = new ControllerGroup();
$departmentControllers->setGroupPath('/department');

$departmentControllers->addController(new GetDepartmentsController());
$departmentControllers->addController(new GetDepartmentStaffsController());

$departmentControllers->finalize();