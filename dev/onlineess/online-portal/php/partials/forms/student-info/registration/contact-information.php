<section>
	<div align="center" class="headline">
        <h2>CONTACT INFORMATION</h2>
    </div><br>
    <div class="row">
        <div class="col-md-4">
            <label for="view_mobilenumber" class="form-label">Mobile Number <span class="text-danger">*</span></label>
            <input type="text" id="view_mobilenumber" name="view_mobilenumber" 
                class="form-control-plaintext viewing" onkeypress="return EnterOnlyNumberKey(event)" maxlength="12" required readonly>
        </div>
        <div class="col-md-4">
            <label for="view_telephone" class="form-label">Telephone <span class="text-danger">*</span></label>
            <input type="text" id="view_telephone" name="view_telephone" class="form-control-plaintext viewing"
                onkeypress="return EnterOnlyNumberKey(event)" maxlength="15" required readonly>
        </div>
        <div class="col-md-4">
            <label for="view_emailaddress" class="form-label">Email Address <span class="text-danger">*</span></label>
            <input type="text" id="view_emailaddress" name="view_emailaddress"  
                class="form-control-plaintext viewing" maxlength="50" readonly>
        </div>
    </div>
    <br>
</section>