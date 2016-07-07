<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Task;
use App\Repositories\TaskRepository;

class TaskController extends Controller
{
	protected $tasks;

	public function __construct(TaskRepository $tasks)
    {
        $this->middleware('auth');

		$this->tasks = $tasks;
    }

	public function index(Request $request)
    {
		$tasks = $this->tasks->forUser($request->user());

		return view('tasks', [
			'tasks' => $tasks
		]);
    }

	public function store(Request $request)
    {
		$validator = Validator::make($request->all(), [
			'name' => 'required|max:10'
		]);


		if($validator->fails())
		{
			return redirect('/')
				->withInput()
				->withErrors($validator);
		}

		$request->user()->tasks()->create([
			'name' => $request->name
		]);

		return redirect("/");
    }

	public function destroy(Task $task)
	{
		$this->authorize('destroy', $task);

		$task->delete();

		return redirect("/");
	}
}
