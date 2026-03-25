@extends("layouts.pub.master")

@section('custom_css')

@endsection



@section("content")   
<div class="latest-area section-padding bg-white">
    <div class="container">
        <div role="alert" class="alert alert-success"> 
                A total of {{$facilities->total()}} record(s) found                    
        </div> 

        <div class="box-body">
 
                <table id="hosp" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>State</th>
                        <th>LGA</th>
                        <th>Facility Code</th>
                        <th>Facility Name</th>
                        <th>Facility Level</th>
                        <th>Ownership</th>
                        <th>Details</th>
                    </tr>
                    </thead>
                    <tbody>
                
                          
                        @foreach($facilities as $fac)
                        <tr>
                            <td>{{$fac->state}}</td>
                            <td>{{$fac->lga}}</td>
                            <td>{{$fac->unique_id}}</td>
                            <td>{{$fac->facility_name}}</td>
                            <td>{{$fac->facility_level}}</td>
                            <td>{{$fac->ownership}}</td>
                            <td>
                              
                                <a href="#">
                                    <button class="btn btn-success btn-sm"  type="button" data-toggle="modal" data-target="#view_details"
                                    data-id="{{$fac->id}}" data-unique_id="{{$fac->unique_id}}" data-registration_no="{{$fac->registration_no}}" data-start_date="{{$fac->start_date}}"
                                    data-facility_name="{{$fac->facility_name}}" data-alt_facility_name="{{$fac->alt_facility_name}}" data-state="{{$fac->state}}"
                                    data-lga="{{$fac->lga}}" data-ward="{{$fac->ward}}" data-ownership="{{$fac->ownership}}" data-ownership_type="{{$fac->ownership_type}}"
                                    data-facility_level="{{$fac->facility_level}}" data-facility_level_option="{{$fac->facility_level_option}}"
                                    data-physical_location="{{$fac->physical_location}}" data-alternate_number="{{$fac->alternate_number}}" data-longitude="{{$fac->longitude}}" data-latitude="{{$fac->latitude}}"
                                    data-postal_address="{{$fac->postal_address}}" data-phone_number="{{$fac->phone_number}}" data-email_address="{{$fac->email_address}}"
                                    data-website="{{$fac->website}}" data-operational_days="{{$fac->operational_days}}" data-operational_hours="{{$fac->operational_hours}}"
                                    data-operation_status="{{$fac->operation_status}}" data-registration_status="{{$fac->registration_status}}" data-license_status="{{$fac->license_status}}"
                                    data-doctors="{{$fac->doctors}}" data-pharmacists="{{$fac->pharmacists}}" data-dentist="{{$fac->dentist}}" data-pharmacy_technicians="{{$fac->pharmacy_technicians}}"
                                    data-nurses="{{$fac->nurses}}" data-lab_scientists="{{$fac->lab_scientists}}" data-midwifes="{{$fac->midwifes}}" data-lab_technicians="{{$fac->lab_technicians}}"
                                    data-nurse_midwife="{{$fac->nurse_midwife}}" data-him_officers="{{$fac->him_officers}}" data-community_health_officer="{{$fac->community_health_officer}}"
                                    data-community_extension_workers="{{$fac->community_extension_workers}}" data-jun_community_extension_worker="{{$fac->jun_community_extension_worker}}"
                                    data-dental_technicians="{{$fac->dental_technicians}}" data-env_health_officers="{{$fac->env_health_officers}}" data-inpatient="{{$fac->inpatient}}"
                                    data-outpatient="{{$fac->outpatient}}" data-beds="{{$fac->beds}}" data-onsite_laboratory="{{$fac->onsite_laboratory}}"
                                    data-onsite_imaging="{{$fac->onsite_imaging}}" data-onsite_pharmarcy="{{$fac->onsite_pharmarcy}}" data-mortuary_services="{{$fac->mortuary_services}}"
                                    data-attendants = "{{ $fac->attendants }}" data-ambulance_services="{{ $fac->ambulance_services }}" data-state_unique_id="{{ $fac->state_unique_id }}" 
                                    data-outpatient = "{{ $fac->outpatient }}" data-inpatient="{{ $fac->inpatient }}" >
                                    View
                                    </button>
                                </a> 
                               
                            </td>
                        </tr>
                    @endforeach
                        
                    </tbody>
                </table>
            
        </div>
        <!-- /.box-body -->
        <div class="box-footer">
            <div class="row">
              
                  @php
                    $perpage = $facilities->perpage();
                    $currentpage = $facilities->currentpage();
                    $from = ($currentpage-1)*$perpage+1;
                    
                    if ($facilities->currentpage() == $facilities->lastpage()) {
                      $to = $facilities->total();
                    } else {
                      $to = $currentpage*$perpage;
                    }
                  @endphp
             
                  <div class="col-md-4">
                      Showing {{$from}} to {{$to}} of {{$facilities->total()}} entries
                     
                  </div>
                  <div class="col-md-8">
                      <div class="pull-right">
                          {{$facilities->links()}}                  
                      </div>
                  </div>

            </div>
          </div>
    </div> <!-- /contanier-->
</div> <!-- / -->
    

@include('hospitals.details_modal')

@endsection 

@push('custom_scripts')
    @include('partials.dynamic_lgas_only')

    <script>
            $(document).ready( function () {
    
               $('#view_details').on('show.bs.modal', function (event) {
                    var button = $(event.relatedTarget)
                    var modal = $(this)
                    $("#specialservice").empty();
                    $("#medical").empty();
                    $("#surgical").empty();
                    $("#gyn").empty();
                    $("#pediatrics").empty();
                    $("#dental").empty();
    
                    modal.find('.modal-body #unique_id').text(button.data('unique_id'));
                    modal.find('.modal-body #state_unique_id').text(button.data('state_unique_id'));
                    modal.find('.modal-body #registration_no').text(button.data('registration_no'));
                    modal.find('.modal-body #start_date').text(button.data('start_date'));
                    modal.find('.modal-body #facility_name').text(button.data('facility_name'));
                    modal.find('.modal-body #alt_facility_name').text(button.data('alt_facility_name'));
                    modal.find('.modal-body #state').text(button.data('state'));
                    modal.find('.modal-body #lga').text(button.data('lga'));
                    modal.find('.modal-body #ward').text(button.data('ward'));
                    modal.find('.modal-body #ownership').text(button.data('ownership'));
                    modal.find('.modal-body #ownership_type').text(button.data('ownership_type'));
                    modal.find('.modal-body #facility_level').text(button.data('facility_level'));
                    modal.find('.modal-body #facility_level_option').text(button.data('facility_level_option'));
                    modal.find('.modal-body #physical_location').text(button.data('physical_location'));
                    modal.find('.modal-body #longitude').text(button.data('longitude'));
                    modal.find('.modal-body #latitude').text(button.data('latitude'));
                    modal.find('.modal-body #postal_address').text(button.data('postal_address'));
                    modal.find('.modal-body #phone_number').text(button.data('phone_number'));
                    modal.find('.modal-body #alternate_number').text(button.data('alternate_number'));
                    modal.find('.modal-body #email_address').text(button.data('email_address'));
                    modal.find('.modal-body #website').text(button.data('website'));
                    modal.find('.modal-body #operational_days').text(button.data('operational_days'));
                    modal.find('.modal-body #operational_hours').text(button.data('operational_hours'));
                    modal.find('.modal-body #operation_status').text(button.data('operation_status'));
                    modal.find('.modal-body #registration_status').text(button.data('registration_status'));
                    modal.find('.modal-body #license_status').text(button.data('license_status'));
                    modal.find('.modal-body #doctors').text(button.data('doctors'));
                    modal.find('.modal-body #pharmacists').text(button.data('pharmacists'));
                    modal.find('.modal-body #dentist').text(button.data('dentist'));
                    modal.find('.modal-body #pharmacy_technicians').text(button.data('pharmacy_technicians'));
                    modal.find('.modal-body #nurses').text(button.data('nurses'));
                    modal.find('.modal-body #lab_scientists').text(button.data('lab_scientists'));
                    modal.find('.modal-body #attendants').text(button.data('attendants'));
                    modal.find('.modal-body #midwifes').text(button.data('midwifes'));
                    modal.find('.modal-body #lab_technicians').text(button.data('lab_technicians'));
                    modal.find('.modal-body #nurse_midwife').text(button.data('nurse_midwife'));
                    modal.find('.modal-body #him_officers').text(button.data('him_officers'));
                    modal.find('.modal-body #community_health_officer').text(button.data('community_health_officer'));
                    modal.find('.modal-body #community_extension_workers').text(button.data('community_extension_workers'));
                    modal.find('.modal-body #jun_community_extension_worker').text(button.data('jun_community_extension_worker'));
                    modal.find('.modal-body #dental_technicians').text(button.data('dental_technicians'));
                    modal.find('.modal-body #env_health_officers').text(button.data('env_health_officers'));
                    modal.find('.modal-body #attendats').text(button.data('attendants'));
                    modal.find('.modal-body #outpatient').text(button.data('outpatient'));
                    modal.find('.modal-body #inpatient').text(button.data('inpatient'));
                    modal.find('.modal-body #beds').text(button.data('beds'));
                    modal.find('.modal-body #onsite_laboratory').text(button.data('onsite_laboratory'));
                    modal.find('.modal-body #onsite_imaging').text(button.data('onsite_imaging'));
                    modal.find('.modal-body #onsite_pharmarcy').text(button.data('onsite_pharmarcy'));
                    modal.find('.modal-body #mortuary_services').text(button.data('mortuary_services'));
                    modal.find('.modal-body #ambulance_services').text(button.data('ambulance_services'));
                        
                    var hosp_id = button.data('id');
                    var _token = $('input[name="_token"]').val();
                    $.ajax({
                        url:"{{route('hospitals.getServices')}}",
                        method:"POST",
                        data:{hosp_id:hosp_id,_token:_token},
                        success:function(result)
                        {
                            $.each(result, function(i, item) {   
                                if (item.category_id=="1"){
                                    $("#medical").append("<span class='label label-default'>" + item.name + "</span> ");
                                }
                                if (item.category_id=="2"){
                                    $("#surgical").append("<span class='label label-default'>" + item.name + "</span> ");
                                }
                                if (item.category_id=="3"){
                                    $("#gyn").append("<span class='label label-default'>" + item.name + "</span> ");
                                }
                                if (item.category_id=="4"){
                                    $("#pediatrics").append("<span class='label label-default'>" + item.name + "</span> ");
                                }
                                if (item.category_id=="5"){
                                    $("#dental").append("<span class='label label-default'>" + item.name + "</span> ");
                                }
                                if (item.category_id=="6"){
                                    $("#specialservice").append("<span class='label label-default'>" + item.name + "</span> ");
                                }
                            });     
                        
                        }         
                    })
    
                });//end
                    
           
            });
    </script>
@endpush