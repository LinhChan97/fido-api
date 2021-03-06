<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\MyCollection;
use App\Rating;
use App\Doctor;
use App\Library\MyFunctions;
use App\Http\Resources\RatingResource;
use App\Http\Resources\RatingCollection;
use App\Patient;
use App\Library\MyValidation;
use Validator;

class RatingDoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($doctor_id)
    {
        $doctor = Doctor::find($doctor_id);
        if($doctor){
            return new RatingCollection($doctor->ratings()->orderBy('id', 'desc')->get());
        }
        return response()->json(['status_code' => 401, 'message' => 'ID not found'], 401);
        
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
    public function store(Request $request, $doctor_id)
    {
        
        $data = $request->all();
        $validator = Validator::make($request->all(), MyValidation::$ruleRating, MyValidation::$messageRating);
        if ($validator->fails()) {
            $message = $validator->messages()->getMessages();
            return response()->json(['status_code' => 202, 'message' => $message]);
        }
        if(!$doctor = Doctor::find($doctor_id)){
            return response()->json(['status_code' => 404, 'message'=> 'ID doctor not found']);
        }
        if(!$patient = Patient::find($request->patient_id)){
            return response()->json(['status_code' => 404, 'message'=> 'ID patient not found']);
        }
        $rating = Rating::create($data);
        MyFunctions::updateRating($rating->star, $rating->like, $doctor_id);
        if ($rating) {
            $rating->like = ($rating->like == null) ? 0 : $rating->like;
            $rating->patient_name = $patient->name;
            $rating->patient_avatar = $patient->avatar;
            $rating->doctor_id = $doctor_id;
            $rating->save();
            return response()->json(['status_code' => 200, 'data' => new RatingResource($rating)]);
        }
        return response()->json(['status_code' => 302, 'message'=> 'Can not create']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($doctor_id, $id)
    {
        $rating = Doctor::find($doctor_id)->ratings()->find($id);

        if ($rating) {
            return new RatingResource($rating);
        }
        return response()->json(['status_code' => 401, 'message' => 'ID not found'], 401);
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
    public function update(Request $request, $doctor_id, $id)
    {
        if(!$doctor = Doctor::find($doctor_id)){
            return response()->json(['status_code' => 404, 'message'=> 'ID doctor not found']);
        }
        if(!$patient = Patient::find($request->patient_id)){
            return response()->json(['status_code' => 404, 'message'=> 'ID patient not found']);
        }
        $rating = $doctor->ratings()->get()->find($id);
        if ($rating) {
            $data = $request->all();
            MyFunctions::updateRating($rating->star, $rating->like, $doctor_id);
            $rating->update($data);
            $rating->patient_name = $patient->name;
            $rating->patient_avatar = $patient->avatar;
            $rating->save();
            return response()->json(['status_code' => 200, 'data' => new RatingResource($rating)]);
        }
        return response()->json([
            'status_code' => 404,
            'message' => 'ID not found'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($doctor_id, $id)
    {
        $rating = Doctor::find($doctor_id)->ratings()->find($id);
        if ($rating) {
            $rating->delete();
            return response()->json(['status_code' => 204]);
        }
        return response()->json(['status_code' => 401, 'message' => 'ID not found']);
    }
}
