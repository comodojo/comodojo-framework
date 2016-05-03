<?php namespace Comodojo\Test\Task;

use \Comodojo\Test\Base;
use \Comodojo\Test\TestPackageTrait;
use \Comodojo\Task\Controller as TaskController;
use \Comodojo\Task\View as TaskView;
use \Comodojo\Task\Iterator as TaskIterator;

class TaskTest extends Base {

    use TestPackageTrait;

    public function testCreateEditAndReadTask() {

        $pid = $this->createTestPackage();

        $task_data = array(
            "name" => 'Test',
            "class" => "comodojo\\test",
            "description" => "text",
            "package" => $pid
        );

        $task = new TaskController($this->configuration(), $this->database());

        $data = $task->merge($task_data)->persist();

        $this->assertInstanceOf('Comodojo\Task\Controller', $data);

        $taskc = clone($task);

        $task->description = "text2";
        $data = $task->persist();

        $this->assertInstanceOf('Comodojo\Task\Controller', $data);

        $taskr = new TaskView($this->configuration(), $this->database());
        $taskr->load($data->id);

        $this->assertEquals($task_data["name"], $taskr->name);
        $this->assertEquals($task_data["class"], $taskr->class);
        $this->assertEquals("text2", $taskr->description);
        $this->assertEquals($task_data["package"], $taskr->package);

        $package = $taskr->getPackage();

        $this->assertInstanceOf('Comodojo\Package\View', $package);

        $this->assertEquals($task_data["name"], $taskc->name);
        $this->assertEquals($task_data["class"], $taskc->class);
        $this->assertEquals($task_data["description"], $taskc->description);
        $this->assertEquals($task_data["package"], $taskc->package);

    }

    public function testListAndDeletePackages() {

        $tasks = TaskIterator::load($this->configuration(), $this->database(), true);

        foreach ($tasks as $task) {

            $this->assertInstanceOf('Comodojo\Task\Controller', $task);

            $result = $task->delete();

            $this->assertTrue($result);

        }

    }

}
