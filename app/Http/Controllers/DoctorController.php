<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Library\MyValidation;
use App\Http\Resources\DoctorResource;
use App\Doctor;
use App\Http\Resources\DoctorCollection;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new DoctorCollection(Doctor::with('address', 'specialist', 'sub_specialist', 'employee', 'ratings')->where('actived', '=', 1)->get());
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
        return MyController::store($request, 'App\\Doctor', MyValidation::$ruleDoctor, MyValidation::$messageDoctor);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return new DoctorResource(Doctor::with('address', 'specialist', 'sub_specialist', 'employee', 'ratings')->find($id));
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
        return MyController::update($request, $id, 'App\\Doctor');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return MyController::destroy($id, 'App\\Doctor');
    }
}
