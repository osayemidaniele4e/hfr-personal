<?php

namespace App\Http\Controllers;

use App\Exports\HFExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Models\Download;
use Notification;
use App\Notifications\SendDownloadVerificationCode;


/**
 * @group Administration - Downloads
 *
 * APIs for handling facility downloads and user registration for downloading datasets.
 *
 * This controller manages public registration, token verification, dataset filtering, exporting, 
 * and admin download requests.
 */
class DownloadController extends Controller
{

    /**
     * Display the filtered facilities list for download (requires verified token).
     *
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        if (!$request->session()->has('token_verified')) {
            return redirect()->route('openRegistrationForm');
        }

        //set values facility list when no filter
        $data['lga_id'] = 1;
        $data['ward_id'] = 1;
        $data['facility_name'] = "";
        $data['geo_codes'] = 0;
        $data['facility_level_id'] = 0;
        $data['ownership_id'] = 0;
        $data['operational_status_id'] = 0;
        $data['registration_status_id'] = 0;
        $data['license_status_id'] = 0;
        $data['service_type'] = 0;
        $data['service_category_id'] = 0;
        $data['facility_type_id'] = 0;

        return view('public.download_list', compact('data'));
    }



    /**
     * Export facilities dataset to Excel based on filter criteria.
     *
     *
     * @bodyParam lga_id integer Filter by LGA ID. Example: 1
     * @bodyParam ward_id integer Filter by ward ID. Example: 1
     * @bodyParam facility_name string Filter by facility name. Example: My Facility
     * @bodyParam geo_codes integer Geo code filter (0,1,2). Example: 0
     * @bodyParam facility_type_id integer Facility type filter. Example: 1
     * @bodyParam facility_level_id integer Facility level filter. Example: 2
     * @bodyParam ownership_id integer Ownership filter. Example: 3
     * @bodyParam operational_status_id integer Operational status filter. Example: 1
     * @bodyParam registration_status_id integer Registration status filter. Example: 1
     * @bodyParam license_status_id integer License status filter. Example: 1
     * @bodyParam service_type integer Service type (1=Outpatient, 2=Inpatient). Example: 1
     *
     * @response 200 File download (Excel)
     * @response 302 Redirect if no records found
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function export(Request $request)
    {
        $lga_id = $request->lga_id;
        $ward_id = $request->ward_id;
        $facility_name = $request->facility_name;
        $facility_type_id = $request->facility_type_id;
        $facility_level_id = $request->facility_level_id;
        $ownership_id = $request->ownership_id;
        $operational_status_id = $request->operational_status_id;
        $registration_status_id = $request->registration_status_id;
        $license_status_id = $request->license_status_id;

        if ($request->geo_codes == 0) {
            $cond = "<>";
            $value = 'XXX';
        }
        if ($request->geo_codes == 1) {
            $cond = "<>";
            $value = '';
        }
        if ($request->geo_codes == 2) {
            $cond = "=";
            $value = '';
        }

        if ($request->service_type == 1) {
            $outpatient = 'Yes';
            $inpatient = '';
        } elseif ($request->service_type == 2) {
            $outpatient = '';
            $inpatient = 'Yes';
        } else {
            $outpatient = '';
            $inpatient = '';
        }

        if ($ward_id == 0) {
            $ward_id = '';
        }
        if ($facility_level_id == 0) {
            $facility_level_id = '';
        }
        if ($ownership_id == 0) {
            $ownership_id = '';
        }
        if ($operational_status_id == 0) {
            $operational_status_id = '';
        }
        if ($registration_status_id == 0) {
            $registration_status_id = '';
        }
        if ($license_status_id == 0) {
            $license_status_id = '';
        }



        $facilities = DB::table('hospital_details')
            ->select(
                'unique_id',
                'registration_no',
                'start_date',
                'facility_name',
                'state',
                'lga',
                'ward',
                'ownership',
                'facility_level',
                'longitude',
                'latitude',
                'operation_status',
                'registration_status',
                'license_status'
            )
            ->whereIn('state_id', $request->state)
            ->where('lga_id', 'like', '%' . $lga_id . '%')
            ->where(DB::Raw("IFNULL(ward_id, '')"), 'like', '%' . $ward_id . '%')
            ->where('facility_level_id', 'like', '%' . $facility_level_id . '%')
            ->where('ownership_id', 'like', '%' . $ownership_id . '%')
            ->where('operational_status_id', 'like', '%' . $operational_status_id . '%')
            ->where('registration_status_id', 'like', '%' . $registration_status_id . '%')
            ->where('license_status_id', 'like', '%' . $license_status_id . '%')
            ->where(DB::Raw("IFNULL(outpatient, '')"), 'like', '%' . $outpatient . '%')
            ->where(DB::Raw("IFNULL(inpatient, '')"), 'like', '%' . $inpatient . '%')
            ->where('latitude', $cond, $value)
            ->orderBy('state')
            ->orderBy('lga')
            ->orderBy('facility_name')
            ->get();

        if ($facilities->count() == 0) {
            session()->flash("alert-success", "No record found with selected criteria!");
            return redirect()->back();
        } else {
            $column_header = array(
                "facility_code",
                "reg_number",
                "start_date",
                "facility_name",
                "state",
                "lga",
                "ward",
                "ownership",
                "facility_level",
                "longitude",
                "latitude",
                "operation_status",
                "regulatory_status",
                "license_status"
            );

            return Excel::download(new HFExport($facilities, $column_header), 'data.xlsx');
        }
    }


     /**
     * Display registration form for users to access downloads.
     *
     *
     * @return \Illuminate\View\View
     */
    public function openRegistrationForm(Request $request)
    {
        if ($request->session()->has('token_verified')) {
            return redirect()->route('downloadFacilitiesList');
        }

        return view('public.download_registration');
    }


    /**
     * Store registration details and send verification token via email.
     *
     *
     * @bodyParam firstname string required First name of the user. Example: John
     * @bodyParam lastname string required Last name of the user. Example: Doe
     * @bodyParam organisation string Organization name. Example: My Org
     * @bodyParam country string required Country name. Example: Nigeria
     * @bodyParam designation string required User designation. Example: Data Officer
     * @bodyParam purpose string required Purpose for downloading data. Example: Research
     * @bodyParam email string required Email address. Example: john@example.com
     * @bodyParam g-recaptcha-response string required Google reCAPTCHA token.
     *
     * @response 302 Redirect to token verification page
     * @response 422 Validation errors
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'organisation' => 'nullable|string|max:100',
            'country' => 'required',
            'designation' => 'required',
            'country' => 'required',
            'purpose' => 'required|max:200',
            'email' => 'required|string|email|max:100',
            'g-recaptcha-response' => 'required|captcha',
        ]);

        Download::create($request->all());

        $code = $this->generateToken();

        $request->session()->put('download_verify', [
            'token' => $code,
            'expire_at' => Carbon::now()->addMinutes(15),
        ]);

        if ($this->sendToken2Email($request->email, $code)) {
            return redirect()->route('getValidateForm');
        }

        session()->flash("alert-danger", "Something went wrong, try again!");
        return redirect()->route('openRegistrationForm');
    }


     /**
     * Display token verification form for download access.
     *
     *
     * @return \Illuminate\View\View
     */
    public function getValidationForm(Request $request)
    {
        if (!$request->session()->has('download_verify')) {
            return view('public.download_registration');
        }

        return view('public.download_token');
    }

    public function generateToken()
    {
        $code = mt_rand(12345678, 98765432);
        return $code;
    }

    public function sendToken2Email($email, $code)
    {
        try {
            Notification::route('mail', $email)
                ->notify(new SendDownloadVerificationCode($code));
        } catch (\Exception $ex) {
            return false; //un able send code
        }
        return true;
    }


     /**
     * Validate the token entered by the user to grant download access.
     *
     *
     * @bodyParam token integer required Verification token sent to email. Example: 12345678
     *
     * @response 302 Redirect to facilities list if token is valid
     * @response 302 Back to token form if invalid
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    //validate the token if valid  show download page
    public function validateToken(Request $request)
    {
        $token_expire = 0;
        $token_match = 0;

        //check if token has expired
        if ($request->session()->get('download_verify.expire_at') < Carbon::now()) {
            $token_expire = 1;
        } else {
            $token_expire = 0;
        }

        //check if token matches
        if ($request->token != $request->session()->get('download_verify.token')) {
            $token_match = 0;
        } else {
            $token_match = 1;
        }

        if (($token_expire == 0) and ($token_match == 1)) {
            $request->session()->put('token_verified', [
                'verified' => 'Yes',
            ]);
            return redirect()->route('downloadFacilitiesList');
        } else {
            return back()->withErrors([
                'token' => 'Invalid token',
            ]);
        }
    }




     /**
     * Admin: List all download requests.
     *
     *
     * @return \Illuminate\View\View
     */
    //for admin module
    public function DownloadRequests()
    {
        $downloads = Download::orderBy('id', 'DESC')->get();

        return view('downloads.index', compact("downloads"));
    }
}
