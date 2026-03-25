
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
           


    });
</script>