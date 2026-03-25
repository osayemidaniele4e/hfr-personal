 <!-- Left side column. contains the sidebar -->
 <aside class="main-sidebar">
     <!-- sidebar: style can be found in sidebar.less -->
     <section class="sidebar">
         <!-- Sidebar user panel -->
         <!-- search form -->
         <!-- /.search form -->
         <!-- sidebar menu: : style can be found in sidebar.less -->
         <ul class="sidebar-menu" data-widget="tree">
             <li>
                 <a target="_blank" rel="noopener noreferrer" href="{{ env('FRONTEND_URL') }}">
                     <i class="fa fa-home"></i>
                     <span>Public Portal</span>
                 </a>
             </li>
             <li>
                 <a href="{{ route('admin_home') }}">
                     <i class="fa fa-dashboard"></i>
                     <span>Dashboard</span>
                 </a>
             </li>
             @if (auth()->user()->hasPermissionTo(62))
                 <li class="treeview">
                     <a href="#">
                         <i class="fa fa-gears"></i>
                         <span>Masters</span>
                         <span class="pull-right-container">
                             <span class="label label-success pull-right">+</span>
                         </span>
                     </a>
                     <ul class="treeview-menu">
                         @if (auth()->user()->hasPermissionTo(47))
                             <li><a href="{{ route('states.index') }}"><i class="fa fa-gear"></i> States</a></li>
                         @endif
                         @if (auth()->user()->hasPermissionTo(51))
                             <li><a href="{{ route('lgas.index') }}"><i class="fa fa-gear"></i> LGAs</a></li>
                         @endif
                         @if (auth()->user()->hasPermissionTo(55))
                             <li><a href="{{ route('wards.index') }}"><i class="fa fa-gear"></i> Wards</a></li>
                         @endif
                         @if (auth()->user()->hasPermissionTo(31))
                             <li><a href="{{ route('hospital-services.index') }}"><i class="fa fa-gear"></i> Hospital
                                     Services</a></li>
                         @endif
                         @if (auth()->user()->hasPermissionTo(35))
                             <li><a href="{{ route('imaging-services.index') }}"><i class="fa fa-gear"></i> Imaging
                                     Services</a></li>
                         @endif
                         @if (auth()->user()->hasPermissionTo(39))
                             <li><a href="{{ route('equipments.index') }}"><i class="fa fa-gear"></i> Laboratory
                                     Equipments</a></li>
                         @endif
                         @if (auth()->user()->hasPermissionTo(43))
                             <li><a href="{{ route('certifications.index') }}"><i class="fa fa-gear"></i> Laboratory
                                     Certification</a></li>
                         @endif
                     </ul>
                 </li>
             @endif
             <li class="treeview">
                 @if (auth()->user()->hasPermissionTo(21) or auth()->user()->hasPermissionTo(17))
                     <a href="#">
                         <i class="fa fa-users"></i>
                         <span>User Management</span>
                         <span class="pull-right-container">
                             <span class="label label-success pull-right">+</span>
                         </span>
                     </a>
                 @endif
                 <ul class="treeview-menu">
                     @if (auth()->user()->hasPermissionTo(21))
                         <li><a href="{{ route('roles.index') }}"><i class="fa fa-user-secret"></i>Roles</a></li>
                     @endif
                     @if (auth()->user()->hasPermissionTo(17))
                         <li><a href="{{ route('users.index') }}"><i class="fa fa-user"></i>Users</a></li>
                     @endif
                 </ul>
             </li>
             <li>
                 @if (auth()->user()->hasPermissionTo(1))
                     <a href="{{ route('hospitals.index') }}">
                         <i class="fa fa-h-square"></i>
                         <span>Hospitals and Clinics</span>
                     </a>
                 @endif

             </li>

             <li>
                 @if (auth()->user()->hasPermissionTo(2) or auth()->user()->hasPermissionTo(3) or auth()->user()->hasPermissionTo(4))
                     <a href="{{ route('myrequest.pending') }}">
                         <i class="fa fa-pencil"></i>
                         <span>My Requests</span>
                         <span class="pull-right-container">
                             <span class="label label-success pull-right">{{ $request_count[3] }}</span>
                         </span>
                     </a>
                 @endif
                 {{-- <ul class="treeview-menu">        
                        <li><a href="{{ route('myrequest.pending') }}"><i class="fa fa-pencil-square"></i>My Pending Requests <font color="orange">[{{ $request_count[0] }}]</font> </a></li>
                        <li><a href="{{ route('myrequest.rejected') }}"><i class="fa fa-pencil-square"></i>My Rejected Requests <font color="orange">[{{ $request_count[1] }}]</font></a></li>  
                        <li><a href="{{ route('myrequest.approved') }}"><i class="fa fa-pencil-square"></i>My Approved Requests <font color="orange">[{{ $request_count[2] }}]</font></a></li>
                    </ul> --}}
             </li>
             <li class="treeview">
                 @if (auth()->user()->hasPermissionTo(59) or auth()->user()->hasPermissionTo(60) or auth()->user()->hasPermissionTo(61))
                     <a href="#">
                         <i class="fa fa-check-circle"></i>
                         <span>My Approvals</span>
                         <span class="pull-right-container">
                             <span class="label label-success pull-right">{{ $approval_count[3] }}</span>
                         </span>
                     </a>
                 @endif
                 <ul class="treeview-menu">
                     @if (auth()->user()->hasPermissionTo(59))
                         <li><a href="{{ route('verify.pending') }}"><i class="fa  fa-check"></i>Verification <font
                                     color="orange">[{{ $approval_count[0] }}]</font></a></li>
                     @endif
                     @if (auth()->user()->hasPermissionTo(60))
                         <li><a href="{{ route('validate.pending') }}"><i class="fa  fa-check"></i>Validation <font
                                     color="orange">[{{ $approval_count[1] }}]</font></a></li>
                     @endif
                     @if (auth()->user()->hasPermissionTo(61))
                         <li><a href="{{ route('publish.pending') }}"><i class="fa  fa-check"></i>Publication <font
                                     color="orange">[{{ $approval_count[2] }}]</font></a></li>
                     @endif
                     @if (auth()->user()->hasPermissionTo(68))
                         <li><a href="{{ route('approval.tracking') }}"><i class="fa  fa-check"></i>Approvals Tracking
                                 <font color="orange"></font></a></li>
                     @endif

                 </ul>
             </li>
             <li>
                 @if (auth()->user()->hasPermissionTo(9))
                     <a href="{{ route('pharmacies.index') }}">
                         <i class="fa fa-medkit"></i>
                         <span>Pharmaceutical Premises</span>
                     </a>
                 @endif
             </li>
             <li>
                 @if (auth()->user()->hasPermissionTo(5))
                     <a href="{{ route('laboratory.index') }}">
                         <i class="fa fa-stethoscope"></i>
                         <span>Laboratory Premises</span>
                     </a>
                 @endif
             </li>
             <li>
                 @if (auth()->user()->hasPermissionTo(13))
                     <a href="{{ route('imaging.index') }}">
                         <i class="fa fa-hospital-o"></i>
                         <span>Radiological Premises</span>
                     </a>
                 @endif
             </li>

             <li>
                 @if (auth()->user()->hasPermissionTo(29))
                     <a href="{{ route('getMessages') }}">
                         <i class="fa  fa-comments"></i>
                         <span>Users Feedback</span>
                     </a>
                 @endif
             </li>
             <li>
                 @if (auth()->user()->hasPermissionTo(30))
                     <a href="{{ route('download.requests') }}">
                         <i class="fa  fa-download"></i>
                         <span>Download Requests</span>
                     </a>
                 @endif
             </li>


             <li>
                 @if (auth()->user()->hasPermissionTo(25))
                     <a href="{{ route('resources') }}">
                         <i class="fa  fa-folder"></i>
                         <span>Resources</span>
                     </a>
                 @endif
             </li>
             <li class="treeview">
                 @if (auth()->user()->hasPermissionTo(65))

                     <a href="#">
                         <i class="fa  fa-file-text"></i>
                         <span>Reports</span>
                         <span class="pull-right-container">
                             <span class="label label-success pull-right">+</span>
                         </span>
                     </a>

                     <ul class="treeview-menu">
                         @if (auth()->user()->hasPermissionTo(73))
                             <li><a href="{{ route('updates.selection') }}"><i class="fa fa-file-text-o"></i>Facilities
                                     Update Report</a></li>
                         @endif
                         @if (auth()->user()->hasPermissionTo(74))
                             <li><a href="{{ route('status.index') }}"><i class="fa fa-file-text-o"></i>Facilities
                                     Status Summary</a></li>
                         @endif
                         @if (auth()->user()->hasPermissionTo(75))
                             <li><a href="{{ route('status.detailsIndex') }}"><i
                                         class="fa fa-file-text-o"></i>Facilities Status Details</a></li>
                         @endif
                         @if (auth()->user()->hasPermissionTo(76))
                             <li><a href="{{ route('services.index') }}"><i class="fa fa-file-text-o"></i>Services
                                     Rendered Report</a></li>
                         @endif
                         @if (auth()->user()->hasPermissionTo(69))
                             <li><a href="{{ route('approvers.index') }}"><i class="fa fa-file-text-o"></i>Approvers
                                     Summary Report</a></li>
                         @endif


                     </ul>
                 @endif
             </li>
             <li class="treeview">
                 <a href="#">
                     <i class="fa  fa-exchange"></i>
                     <span>HFR-DHIS2</span>
                     <span class="pull-right-container">
                         <span class="label label-success pull-right">+</span>
                     </span>
                 </a>

                 <ul class="treeview-menu">
                     @if (auth()->user()->hasPermissionTo(66))
                         <li><a href="{{ route('dhis.logs') }}"><i class="fa fa-file-text"></i>Logs</a></li>
                     @endif
                     @if (auth()->user()->hasPermissionTo(70))
                         <li><a href="{{ route('dhis.lookup') }}"><i class="fa fa-table"></i>Lookup Table</a></li>
                     @endif

                 </ul>

             </li>

             <hr>
             <li class="treeview">
                 <a href="#">
                     <i class="fa fa-gears"></i>
                     <span>Website</span>
                     <span class="pull-right-container">
                         <span class="label label-success pull-right">+</span>
                     </span>
                 </a>
                 <ul class="treeview-menu">
                     @if (auth()->user()->hasPermissionTo(47))
                         <li><a href="{{ route('slider.index') }}"><i class="fa fa-gear"></i> Slider</a></li>
                     @endif
                     @if (auth()->user()->hasPermissionTo(51))
                         <li><a href="{{ route('about-us.index') }}"><i class="fa fa-gear"></i> Origin</a></li>
                         <li><a href="{{ route('process.index') }}"><i class="fa fa-gear"></i>Process</a></li>
                     @endif

                 </ul>
             </li>

         </ul>
     </section>
     <!-- /.sidebar -->
 </aside>
