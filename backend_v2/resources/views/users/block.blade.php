<!-- Modal delete record -->
<div class="modal fade" id="disableUser" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-sm" role="document">
          <div class="modal-content">
           
          <form action="{{route('blockUser')}}" method="POST">
              @csrf
              @method("PUT")

              <div class="modal-body">
                  <p class="text-center" id = "message">
                    
                  </p>
                    <input type="hidden" id="userid" name="userid" value="">  
                    <input type="hidden" id="status" name="status" value="">  

              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">No</button>
                <button type="submit" class="btn btn-warning btn-sm">Yes</button>
              </div>
            </form>
            
          </div>
        </div>
 </div> 
      