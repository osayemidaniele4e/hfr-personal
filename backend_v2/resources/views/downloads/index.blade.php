@extends("layouts.master")


@section('content-title')
Guest Data Downloads


@endsection

@section("content")
<div class="box">
  <div class="box-body">
    
    <table id="table1" class="table table-bordered table-striped" style="width:100%">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Organisation</th>
          <th>Designation</th>
          <th>Country</th>
          <th>Purpose of Data</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        
        @foreach($downloads as $d)
        <tr>
          <td>{{ $d->id }}</td>
          <td>{{$d->firstname}} {{$d->lastname}}</td>
          <td>{{$d->email}}</td>
          <td>{{$d->organisation}}</td>
          <td>{{$d->designation}}</td>
          <td>{{$d->country}}</td>
          <td>{{$d->purpose}}</td>
          <td> {{ Carbon\Carbon::parse($d->created_at)->toFormattedDateString() }}</td>
        </tr>
        @endforeach
      </tbody>

      <tfoot>
    
      </tfoot>
    </table>
    
    
  </div>
  <!-- /.box-body -->
</div>
<!-- /.box -->
@endsection 


@push("bk_script")
<script>
  $(document).ready( function () {
      $('#table1').DataTable( {
        "paging":   true,
        "ordering": true,
        "info":     true,
        "order": [[ 0, "desc" ]],
        "columnDefs": [
              {
                  "targets": [ 0 ],
                  "visible": false
              }
          ]

      } );
  } );
</script>
@endpush