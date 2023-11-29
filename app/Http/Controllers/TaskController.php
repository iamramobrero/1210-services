<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends BaseController
{
    public function __construct()
    {
        $this->pageTitle = 'Tasks';
    }

    public function index(Request $request)
    {
        $this->apiToken = $request->cookie('apiToken');
        return view('tasks.index');
    }

    public function data(Request $request){

        $data = Task::select('tasks.*');

        if(isset($request->status) && $request->status != '')
            $data = $data->where('status', $request->status);

        if(isset($request->keyword) && $request->keyword != ''){
            $keywords = array_map('trim', explode(',', $request->keyword));

            $data = $data->where(function($query) use ($keywords){
                foreach($keywords as $keyword)
                    $query->orWhere('title', 'like', "%{$keyword}%")->orWhere('content', 'like', "%{$keyword}%");
            });
        }

        if(isset($request->sort_by) && $request->sort_by != ''){
            $sortBy ='id';
            $sortOrder = $request->input('sort_order','ASC');
            if($request->sort_by=='date') $sortBy = 'datetime_at';
            elseif($request->sort_by=='title') $sortBy = 'products.title';
            elseif($request->sort_by=='status'){
                $sortBy = 'products.status';
            };
            $data = $data->orderBy($sortBy,$sortOrder);
        }


        $data = $data->paginate(10);
        return TaskResource::collection($data);
    }


    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        //
    }
}
