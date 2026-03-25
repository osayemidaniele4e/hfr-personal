<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Auth;
use Notification;
use App\Notifications\SendEmailNewUser;
use App\HospitalHistory;


class UserController extends Controller
    {
        use HasRoles;

        public function index()
        {
            $users = User::where('state_id', 'like','%'. Auth::user()->state_id. '%')
                    ->orderBy('state_id')
                    ->orderBy('firstname')
                    ->paginate(10);
     
            return view('users.index', compact("users"));  
        }
        
    public function store(Request $request)
        {
            $data = $request->all();
            $request->validate([
                'firstname' => 'required|string|max:50',
                'lastname' => 'required|string|max:50',
                'job' => 'nullable|string|max:50',
                'organisation' => 'nullable|string|max:50',
                'role' => 'required',
                'state_id'=>'required',
                'lga_id'=>'required',
                'email' => 'required|string|email|max:255|unique:users',
            ]);

            $user=User::create([
                'firstname' => $data['firstname'],
                'lastname' => $data['lastname'],
                'email' => $data['email'],
                'mobile' => $data['mobile'],
                'state_id' => $data['state_id'],
                'job_title' => $data['job'],
                'status'=>'-1',
                'organisation' => $data['organisation'],
                'password' => Hash::make('Nigeria@HFR20'),
            ]);
            
            $user->assignRole($data['role']);
            $user->syncPermissions($request->lga_id);

            $this->sendEmailtoUser($data['firstname'],'Nigeria@HFR20', $data['email']);

            session()->flash("alert-success", "User registered successfully!");
            return back();
    }

    public function sendEmailtoUser($name,$password,$email)
    {
        try {
            Notification::route('mail', $email)
                        ->notify(new SendEmailNewUser($name,$password,$email));
        } catch (\Exception $ex) {
            return false; //un able send code
        }
        return true;
    }

    public function update(Request $request)
    {
        $request->validate([
            'firstname1' => 'required|string|max:255',
            'lastname1' => 'required|string|max:255',
            'role1' => 'required',
            'state_id1'=>'required',
            'lga_id1'=>'required',
            'job1' => 'nullable|string|max:50',
            'organisation1' => 'nullable|string|max:50',
        ]);

        $hosp = new HospitalHistory;
        $lgas = $hosp->arrayValuesTostring($request->lga_id1);

        $user = User::findOrFail($request->UserID);
        $user->firstname = $request->firstname1;
        $user->lastname = $request->lastname1;
        $user->job_title = $request->job1;
        $user->organisation = $request->organisation1;
        $user->mobile = $request->mobile1;
        $user->state_id = $request->state_id1;
        $user->save();

        $user->syncRoles($request->role1);
        $user->syncPermissions($request->lga_id1);

        session()->flash("alert-success", "User updated successfully!");
        return back();
    }

    public function search(Request $request)
    {
      if($request->role_id == 0){
            $users = User::where('state_id','like','%'.$request->state.'%')
                    ->where('status','like','%'.$request->status.'%')
                    ->where('firstname','like','%'.$request->name.'%')
                    ->paginate(10)
                    ->appends($request->all());
      }
      else{
            $users = User::role($request->role_id)
                    ->where('state_id','like','%'.$request->state.'%')
                    ->where('status','like','%'.$request->status.'%')
                    ->where('firstname','like','%'.$request->name.'%')
                    ->paginate(10)
                    ->appends($request->all());                    
      }

       $request->flash('request',$request); 
       return view('users.index', compact("users"));  
    }

    public function block(Request $request)
    {
       
        if($request->status ==1){
            $user = new User;
            $user = User::findOrFail($request->userid);
            $user->status = 0;
            $user->save();
    
            session()->flash("alert-success", "User blocked successfully!");
            return redirect()->back();
        }
        if($request->status ==0){
            $user = new User;
            $user = User::findOrFail($request->userid);
            $user->status = 1;
            $user->save();
    
            session()->flash("alert-success", "User activated successfully!");
            return redirect()->back();
        }
      
    }

    public function delete(Request $request)
    {
        User::destroy($request->user);
        session()->flash("alert-success", "User deleted successfully!");
        return back();
      
    }

    public function profile()
    {       
        $users = Auth::user();

        return view('users.userprofile', compact("users"));  
    }

    
    public function updateProfile (Request $request)
    {
        //dd($request->all());
        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'mobile' => 'string|max:40',
            'job' => 'nullable|string|max:50',
            'organisation' => 'nullable|string|max:50',
        ]);

        $data = $request->all();

        $user = Auth::user();
        $user->firstname = $data['firstname'];
        $user->lastname = $data['lastname'];
        $user->mobile = $data['mobile'];
        $user->job_title = $data['job'];
        $user->organisation = $data['organisation'];
        $user->save();

        session()->flash("alert-success", "Profile updated successfully!");
        return back();
    }

    
    public function changePassword(Request $request){
 
        if (!(Hash::check($request->current_password, Auth::user()->password))) {
            // The passwords matches
            session()->flash("alert-danger", "Your current password does not matches with the password you provided. Please try again.");
            return redirect()->back();
        }
 
        if(strcmp($request->current_password, $request->new_password) == 0){
            //Current password and new password are same
            session()->flash("alert-danger", "New Password cannot be same as your current password. Please choose a different password.");
            return redirect()->back();
        }
 
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);
 
        //Change Password
        $user = Auth::user();
        $user->password = Hash::make($request->new_password);
        $user->save();
        
        session()->flash("alert-success","Password changed successfully !");
        return redirect()->back();
    }

    public function newUserChangePasswordForm(){
        return view('auth.change_password');
    }

    public function newUserChangePassword(Request $request){
 
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);
 
        //Change Password
        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->status = 1;
        $user->save();
        
        session()->flash("alert-success","Password changed successfully !");
        return redirect()->route('admin_home');
        
    }


    public function getRoleID(Request $request){
        $data = DB::table('roles')
        ->select('id')
        ->where('name', $request->role)
        ->get();
    
        return $data[0]->id;
    }
}
