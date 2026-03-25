<?php

namespace App\Http\Controllers\Approval;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;
use App\HospitalHistory;


class UpdatedRecordsController extends Controller
{  

    public function updatedRecords($id,$stage)
    {
        $audit_id = DB::table('audits')
            ->select('id')
            ->where('event', '=', 'updated')
            ->where('auditable_type','=','App\HospitalHistory')
            ->where('auditable_id','=',$id)
            ->orderBy('id', 'DESC')
            ->first();
          
    
        $hosp = HospitalHistory::find($id);
        $audit = $hosp->audits()->find($audit_id->id);
        $audits= $audit->getModified();

        //new values
        $new_values = DB::table('hospital_details_history')
            ->select('state','lga','ward','ownership','ownership_type','facility_level',
            'facility_level_option','operation_status','registration_status','license_status')
            ->where('id','=',$id)
            ->orderBy('id', 'DESC')
            ->first();

        //old values
        $old_values = DB::table('hospital_details')
            ->select('state','lga','ward','ownership','ownership_type','facility_level',
            'facility_level_option','operation_status','registration_status','license_status')
            ->where('id','=',$id)
            ->first(); 

        
        //get new hospital services
        $new_services = DB::select("SELECT s.name FROM hs_hospital_services_history h
            JOIN lst_hosp_services s ON s.id = h.service_id where h.hospital_id = ". $id . "");
    
        //get old hospital services
        $old_services = DB::select("SELECT s.name FROM hs_hospital_services h
        JOIN lst_hosp_services s ON s.id = h.service_id where h.hospital_id = ". $id . "");


       //create loolup array to display in view
       $lookup=array("Registration No"=>"registration_no","Commencement Date"=>"start_date","Facility Name"=>"facility_name","Alternate Facility Name"=>"alt_facility_name",
        "State"=>"state_id","LGA"=>"lga_id","Ward"=>"ward_id","Ownership"=>"ownership_id","Ownership Type"=>"ownership_type_id",
        "Hospital/ Clinic Level"=>"facility_level_id","Facility Level Options"=>"facility_level_option_id","Specialized Options"=>"facility_level_options_category_id",
        "Longitude"=>"longitude","Latitude"=>"latitude","Postal Address"=>"postal_address","Phone Number"=>"phone_number","Facility Close Date"=>"close_date",
        "Email Address"=>"email_address","Website"=>"website","Days of Operation"=>"operational_days","Hours of Operation"=>"operational_hours","Operation Status"=>"operational_status_id",
        "Registration Status"=>"registration_status_id","License Status"=>"license_status_id","Medical Doctors"=>"doctors","Pharmacists"=>"pharmacists","Out Patient"=>"outpatient",
        "Dentists"=>"dentist","Pharmacy Technicians"=>"pharmacy_technicians","Number of Nurses (Single Qualified)"=>"nurses","Laboratory Scientists"=>"lab_scientists","In Patient"=>"inpatient",
        "Number of Midwifes (Single Qualified)"=>"midwifes","Laboratory Technicians"=>"lab_technicians","Number of Nurse and Midwife (Double Qualified)"=>"nurse_midwife","Health Records/HIM Officers"=>"him_officers",
        "Community Health Officer"=>"community_health_officer","Community Health Extension Worker"=>"community_extension_workers","Junior Com Health Extension Worker"=>"jun_community_extension_worker",
        "Dental Technicians"=>"dental_technicians","Environmental Health Officers"=>"env_health_officers","Physical Location"=>"physical_location","Alternate Number"=>"alternate_number",
        "Total number of Beds"=>"beds","Onsite Laboratory"=>"onsite_laboratory","Number of Health Attendant/Assistant"=>"attendants", "State Unique ID"=>"state_unique_id", 
        "Onsite Imaging/ Radio-Diagnostics Center"=>"onsite_imaging","Onsite Pharmacy"=>"onsite_pharmarcy","Mortuary Services"=>"mortuary_services","Ambulance Services"=>"ambulance_services");


        $hosp_id = $id;
        $name=($hosp->facility_name);

        if ($stage=='1'){
            return view('approvals.updates_verify',compact('audits','lookup','old_values','new_values','hosp_id','name','old_services','new_services')); 
        }

        if ($stage=='2'){
            return view('approvals.updates_validate',compact('audits','lookup','old_values','new_values','hosp_id','name','old_services','new_services')); 
        }

        if ($stage=='3'){
            return view('approvals.updates_publish',compact('audits','lookup','old_values','new_values','hosp_id','name','old_services','new_services')); 
        }
    }
      
}
