<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Group;
use App\Http\Resources\GroupResource;
use App\Http\Resources\MyCollection;
use App\Library\MyResponse;
use Validator;
use App\Library\MyValidation;

// define('ERROR', 1);
// define('SUCCESS', 0);


class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new MyCollection(Group::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
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
        $validator = Validator::make($request->all(), MyValidation::$ruleGroup, MyValidation::$messageGroup);

        if ($validator->fails()) {
            $message = $validator->messages()->getMessages();
            return response()->json([$message], 401);    
        }
        $data = $request->all();
        $data['password'] = bcrypt($data['password']);
        array_push($data, 'api_token', Str::random(10));
        $group = Group::create($data);
        if($group){
            return new GroupResource($group);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $group = Group::find($id);
        if ($group) {
            return new GroupResource($group);
        }

        return response()->json(['error' => 'ID not found']);   
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $group = Group::find($id);
        if ($group) {
            $groupUpdated = $request->all();
            $group->update($groupUpdated);
            return new GroupResource($group);
        }
        return response()->json(['error' => 'ID not found']);   
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $group = Group::find($id);
        if ($group) {
            $group->delete();
            return response()->json(['message' => 'Deleted']);   
        }
        return response()->json(['error' => 'ID not found']);   
    }
}
