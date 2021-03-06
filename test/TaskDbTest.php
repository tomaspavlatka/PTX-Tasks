<?php 
require_once realpath(__DIR__) .'/AppDbTest.php';
require_once MODEL . 'Task.php';

class TaskDbTest extends AppDbTest {

    public $TaskController;    
    private $_db;
    private $_params = array(
        'storage_type' => 'mysql',        
        'tasks_table' => 'tasks_test');

    public function setUp() {
        parent::setUp();

        // overwrite storage to be json.
        $this->TaskController = new TaskController();  
    }

    public function getDataSet() {
        return $this->createFlatXMLDataSet(dirname(__FILE__).'/db/tasks_test.xml');
    }

    /**
     * Tests add() function from TaskController
     */
    public function testAdd() {        
        
        // 1. First save.
        $data = array(
            'id' => null,
            'name' => 'Tomas',
            'content' => 'TEST PROJECT');
        $return_data = $this->TaskController->add($data, $this->_params);

        $expected_data = array( 
            'task_data' => array(                           
                'id' => 1,
                'name' => 'Tomas',
                'content' => 'TEST PROJECT',
                'time_spent' => 0,
                'task_status' => 'opened',
                'status' => 1));        
        $this->assertTrue($this->_compare_arrays_contains($expected_data, $return_data));

        // 2. Save.
        $return_data = $this->TaskController->add($data, $this->_params);
        $expected_data['task_data']['id'] = 2;
        $this->assertTrue($this->_compare_arrays_contains($expected_data, $return_data));
    }

    /**
     * Tests add() function 
     */
    public function testAddAsEdit() {
        // 1. First save.
        $data = array(
            'id' => 0,
            'name' => 'Tomas',
            'content' => 'TEST PROJECT');
        $return_data = $this->TaskController->add($data, $this->_params);

        $expected_data = array(   
            'task_data' => array(
                'id' => 1,
                'name' => 'Tomas',
                'content' => 'TEST PROJECT',
                'time_spent' => 0,
                'task_status' => 'opened',
                'status' => 1));        
        $this->assertTrue($this->_compare_arrays_contains($expected_data, $return_data));

        // 2. Save.
        $data = array(
            'id' => 1,
            'name' => 'Tomas 2',
            'content' => 'TEST PROJECT 2');
        $return_data = $this->TaskController->add($data, $this->_params);
        $expected_data['task_data']['name'] = 'Tomas 2';
        $expected_data['task_data']['content'] = 'TEST PROJECT 2';        
        $this->assertTrue($this->_compare_arrays_contains($expected_data, $return_data));
    }

    /**
     * Test add_time() for opened tasks.     
     */
    public function testAddTimeForOpenTasks() {
        // 1. First save.
        $data = array(
            'id' => 0,
            'name' => 'Tomas',
            'task_status' => 'opened',
            'content' => 'TEST PROJECT');
        $return_data = $this->TaskController->add($data, $this->_params);

        $data = array(
            'id' => 1, 
            'time_spent' => 90);
        $return_data = $this->TaskController->add_time($data, $this->_params);
        
        $expected_data = array(
            'task_data' => array(
                'id' => 1, 
                'time_spent' => 5400));
        $this->assertTrue($this->_compare_arrays_contains($expected_data, $return_data));        
    }

    /**
     * Test add_time() for closed tasks
     */ 
    public function testAddTimeForClosedTasks() {
        // 1. First save.
        $data = array(
            'id' => 0,
            'name' => 'Tomas',
            'task_status' => 'closed',
            'content' => 'TEST PROJECT');        
        $return_data = $this->TaskController->add($data, $this->_params);

        $data = array(
            'id' => 1, 
            'time_spent' => 90);
        $return_data = $this->TaskController->add_time($data, $this->_params);
        
        $expected_data = array(
            'errors' => array(
                'general' => 'You can report only for opened tasks.'));
        $this->assertTrue($this->_compare_arrays_contains($expected_data, $return_data));        
    }

    /**
     * Test close() for opened tasks.
     */
    public function testCloseForOpenTasks() {
        // 1. First save.
        $data = array(
            'id' => 0,
            'name' => 'Tomas',
            'task_status' => 'opened',
            'content' => 'TEST PROJECT');
        $return_data = $this->TaskController->add($data, $this->_params);

        $data = array(
            'id' => 1);
        $return_data = $this->TaskController->close($data, $this->_params);

        $expected_data = array(
            'task_data' => array(
                'id' => 1, 
                'task_status' => 'closed'));
        $this->assertTrue($this->_compare_arrays_contains($expected_data, $return_data));  
    }

    /**
     * Test close() for closed tasks.
     */
    public function testCloseForClosedTasks() {
        // 1. First save.
        $data = array(
            'id' => 0,
            'name' => 'Tomas',
            'task_status' => 'closed',
            'content' => 'TEST PROJECT');        
        $return_data = $this->TaskController->add($data, $this->_params);

        $data = array(
            'id' => 1);
        $return_data = $this->TaskController->close($data, $this->_params);

        $expected_data = array(
            'errors' => array(
                'general' => 'This task is already closed.'));
        $this->assertTrue($this->_compare_arrays_contains($expected_data, $return_data));  
    }

    /**
     * Test close() for opened tasks.
     */
    public function testOpenForOpenTasks() {
        // 1. First save.
        $data = array(
            'id' => 0,
            'name' => 'Tomas',
            'task_status' => 'opened',
            'content' => 'TEST PROJECT');
        $return_data = $this->TaskController->add($data, $this->_params);

        $data = array(
            'id' => 1);
        $return_data = $this->TaskController->open($data, $this->_params);

        $expected_data = array(
            'errors' => array(
                'general' => 'This task is already opened.'));
        $this->assertTrue($this->_compare_arrays_contains($expected_data, $return_data));  
    }

    /**
     * Test close() for closed tasks.
     */
    public function testOpenForClosedTasks() {
        // 1. First save.
        $data = array(
            'id' => 0,
            'name' => 'Tomas',
            'task_status' => 'closed',
            'content' => 'TEST PROJECT');
        $return_data = $this->TaskController->add($data, $this->_params);

        $data = array(
            'id' => 1);
        $return_data = $this->TaskController->open($data, $this->_params);

        $expected_data = array(
            'task_data' => array(
                'id' => 1, 
                'task_status' => 'opened'));
        $this->assertTrue($this->_compare_arrays_contains($expected_data, $return_data));  
    }

    /**
     * Test task()
     */
    public function testTask() {
        // 1. First save.
        $data = array(
            'id' => 0,
            'name' => 'Tomas',
            'task_status' => 'closed',
            'content' => 'TEST PROJECT');
        $return_data = $this->TaskController->add($data, $this->_params);

        $data = array(
            'id' => 1);
        $return_data = $this->TaskController->task($data, $this->_params);

        $expected_data = array(
            'task_data' => array(
                'id' => 1, 
                'name' => 'Tomas',
                'task_status' => 'closed',
                'content' => 'TEST PROJECT'));
        $this->assertTrue($this->_compare_arrays_contains($expected_data, $return_data));
    }

    /**
     * Tests index()
     */
    public function testIndex() {
        // 1. First save.
        $data = array(
            'id' => 0,
            'name' => 'Tomas',
            'task_status' => 'closed',
            'content' => 'TEST PROJECT');

        for($i = 0; $i < 20; $i++) {
            $return_data = $this->TaskController->add($data, $this->_params);            
        }

        $data = array(
            'page' => 0);
        $return_data = $this->TaskController->index($data, $this->_params);

        $expected_data = array(
            'paginator' => array(
                'active' => 1, 
                'pages' => 2,
                'records' => 20));
        $this->assertTrue($this->_compare_arrays_contains($expected_data, $return_data));

        $data = array(
            'page' => 2);
        $return_data = $this->TaskController->index($data, $this->_params);

        $expected_data = array(
            'paginator' => array(
                'active' => 2, 
                'pages' => 2,
                'records' => 20));
        $this->assertTrue($this->_compare_arrays_contains($expected_data, $return_data));
    }
}