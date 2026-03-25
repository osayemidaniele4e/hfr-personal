@extends("layouts.master")


@section('content-title')
Approvers Activity Summary

@endsection

@section("content")
 
 
<div class="box">
    
        <div class="box-body">
                <form class="form-horizontal"  action="{{route('approvers.report')}}" method="GET">
                        @csrf
            
                        <div class="form-group">
                            <div class="col-sm-5">
                                <select class="form-control select2" id="state_id" name ="state_id">
                                    <option value="1">All States</option>
                                    @foreach(getStates() as $st)
                                        <option value="{{$st->id}}"  {{ ($st->id == $data['state_id'] ? "selected":"") }}>{{$st->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-sm-5">
                                <select class="form-control select2" id="level" name="level">
                                    <option value="2" {{ (2 == $data['level'] ? "selected":"") }}>Validators Summary</option>
                                    <option value="3" {{ (3 == $data['level'] ? "selected":"") }}>Publishers Summary</option>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <button type="submit" class="btn btn-success btn-block pull-right  btn-sm">Show</button>
                            </div>

                        </div>
               
                    <hr>
                </form>

                @if ($summary->count() == 0)
                    <div class="alert alert-success alert-dismissible">
                        No record found!
                    </div>
                @else

                <table id="table2" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Name</th>
                       
                        @if ($data['level'] == 2)
                            <th>Total Validations</th>                              
                        @endif
                        @if ($data['level'] == 3 )
                            <th>Total Publications</th>                              
                        @endif                
                    </tr>
                    </thead>
                    <tbody>
                
                        @foreach($summary as $s)
                            <tr> 
                         
                                @if ($data['level'] == 2)
                                    <td>{{$s->validated_by}}</td>                             
                                @endif
                                @if ($data['level'] == 3 )
                                    <td>{{$s->published_by}}</td>                                                                 
                                @endif 
                                <td>{{$s->total}}</td>
                                                      
                            </tr>
                        @endforeach
                        
                    </tbody>
                </table>
            @endif
        
        
    </div>
    <!-- /.box-body -->
    <div class="box-footer">
    
        {{-- download buttons  --}}
  

      
    </div>
    
</div>
<!-- /.box -->

    
@endsection 
    
    
@push('bk_script')
    @include('partials.notification')
    @include('partials.dynamic_state_script')

    <script>
        
        $(document).ready( function () {
            $('#table2').DataTable( {
                "paging":   true,
                "ordering": true,
                "info":     true
            });

        });
    
    
     
    </script>
    
@endpush