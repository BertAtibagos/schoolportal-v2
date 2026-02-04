<div class="modal fade custom-size" id="confirmation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
			<div class="modal-header">
			</div>
            <div class="modal-body">
                <p id="confirmation-msg" style='font-size: 16px;
												font-family: tahoma, sans-serif;
												font-weight: normal;
												color: blue;'>
				</p>
            </div>
            <div class="modal-footer">
				<button type="button" class="btn btn-success btn-primary" id="confirmation-yes">Yes</button>
				<button type="button" class="btn btn-block btn-primary" id="confirmation-no">No</button>
                <button type="button" class="btn btn-danger btn-primary" id="confirmation-cancel" data-dismiss="modal" >Cancel</button>
            </div>
        </div>
    </div>
	<input type="hidden" id="inputhiddentriggerstatus" name="inputhiddentriggerstatus" />
	<input type="hidden" id="inputhiddentriggertype" name="inputhiddentriggertype" />
</div>

<?php
	echo "<script src='../../js/custom/confirmation-modal-script.js'></script>";
?>