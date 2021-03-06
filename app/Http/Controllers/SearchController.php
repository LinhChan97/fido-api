<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Doctor;
use DB;
use App\Http\Resources\DoctorCollection;
use App\Patient;
use App\Http\Resources\MyCollection;
use App\Employee;
use App\Http\Resources\PatientCollection;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $data = array(
            'name' => $request->name,
            'specialist_id' => $request->specialist_id,
            'address_id' => $request->address_id,
            'filter' => $request->filter,
        );
        $condition1 = array(['actived', '=', 1]);
        if ($data['address_id']) {
            array_push($condition1, array('address_id', '=', $data['address_id']));
        }
        if ($data['specialist_id']) {
            array_push($condition1, array('specialist_id', '=', $data['specialist_id']));
        }
        if ($data['name']) {
            array_push($condition1, array('name', 'LIKE',  '%' . ($data['name']) . '%'));
        }
        if ($data['filter']) {
            array_push($condition1, array('rating', '<>',  null));
            $doctors = Doctor::with('address', 'specialist', 'sub_specialist', 'employee', 'ratings')->where($condition1)->orderBy('rating', 'desc')->paginate(10);
        }else
        {
            $doctors = Doctor::with('address', 'specialist', 'sub_specialist', 'employee', 'ratings')->where($condition1)->paginate(10);   
        }
        return new DoctorCollection($doctors);
    }

    public function searchPatient(Request $request)
    {
        $data = array(
           'key' => $request->key,
        );
        $patients = Patient::where('name', 'LIKE', '%'.$data['key'].'%')->orWhere('email', 'LIKE', $data['key'])->paginate(10);
        return response()->json(['status_code' => 200, 'data' => new PatientCollection($patients)], 200);
    }

    public function searchEmployee(Request $request)
    {
        $data = array(
           'key' => $request->key,
        );
        $employees = Employee::where('name', 'LIKE', '%'.$data['key'].'%')->orWhere('email', 'LIKE', $data['key'])->paginate(10);
        return new MyCollection($employees);
    }

}
