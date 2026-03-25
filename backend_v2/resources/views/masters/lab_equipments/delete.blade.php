<!-- Modal delete record -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-sm" role="document">
          <div class="modal-content">
           
          <form action="{{route('equipments.destroy','id')}}" method="post">
              @csrf
              @method("DELETE")
              <div class="modal-body">
                  <div id="message"></div>
                  <input type="hidden" name="equip_id" id="equip_id" value="">  
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">No</button>
                <button type="submit" class="btn btn-warning btn-sm">Yes</button>
              </div>
            </form>
            
          </div>
        </div>
 </div> 
      