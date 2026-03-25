<script>
    $(document).ready(function(){
        //if state change fill lga
        $('#state_id').change(function(){
            if($(this).val() != '')
            {
                var stateID= $('#state_id').val();
                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url:"{{route('getLgaList')}}",
                    method:"POST",
                    data:{id:stateID, _token:_token},
                    success:function(result)
                    {
                        $('#lga_id').html(result);
                    }         
                })
            }
        });
           //if lga change fill wards
           $('#lga_id').change(function(){
            if($(this).val() != '')
            {
                var lgaID= $('#lga_id').val();
                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url:"{{route('getWardList')}}",
                    method:"POST",
                    data:{lgaId:lgaID,_token:_token},
                    success:function(result)
                    {
                        $('#ward_id').html(result);
                    }         
                })
            }
        });
         
        
        // $('#state').change(function(){
        //     $('#lga').val('');
        //     $('#lga')
        //     .find('option')
        //     .remove()
        //     .end();
        //     $('#ward')
        //     .find('option')
        //     .remove()
        //     .end();
        // });
        
        // $('#lga').change(function(){
        //     $('#ward')
        //     .find('option')
        //     .remove()
        //     .end();
        // });
        
       


    });
</script>