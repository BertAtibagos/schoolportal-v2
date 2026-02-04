<section>
    
    <div align="center" class="headline">
        <h2>STUDENT INFORMATION</h2>
    </div><br>
    
    <div class="row">
        <div class="col-md-3"><label class="form-label" for="view_firstname">First name <span class="text-danger">*</span></label>
            <input readonly type="text" id="view_firstname" name="view_firstname" class="form-control-plaintext viewing"
                maxlength="40">
        </div>
        
        <div class="col-md-3"><label class="form-label" for="view_middlename">Middle name <span class="text-danger">*</span></label>
            <input readonly type="text" id="view_middlename" name="view_middlename" class="form-control-plaintext viewing" 
                maxlength="40">
        </div>

        <div class="col-md-3">
            <label class="form-label" for="view_lastname">Last name <span class="text-danger">*</span></label>
            <input readonly type="text" id="view_lastname" name="view_lastname" class="form-control-plaintext viewing" 
                maxlength="40">
        </div>
        
        <div class="col-md-3">
            <label class="form-label" for="view_suffix">Suffix</label>
            <input type="text" id="view_suffix" name="view_suffix" class="form-control-plaintext viewing" maxlength="10" readonly>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-3">
            <label for="view_age" class="form-label">Age <span class="text-danger">*</span></label>
            <!-- TODO: PRE-fill registration -->
            <input type="number" min="0" id="view_age" name="view_age" class="form-control-plaintext viewing" onkeypress="return EnterOnlyNumberKey(event)" maxlength="3" readonly>
        </div>
        <div class="col-md-3">
            <label for="view_gender" class="form-label">Gender <span class="text-danger">*</span></label>
            <input type="text" id="view_gender" name="view_gender" class="form-control-plaintext viewing" disabled>

        </div>
        <div class="col-md-3">
            <label for="view_birthdate" class="form-label">Birth date <span class="text-danger">*</span></label>
            <input type="text" id="view_birthdate" name="view_birthdate" class="form-control-plaintext viewing" disabled>
        </div>
        <div class="col-md-3">
            <label for="view_birthplace" class="form-label">Birth place <span class="text-danger">*</span></label>
            <input type="text" id="view_birthplace" name="view_birthplace" class="form-control-plaintext viewing" maxlength="50" disabled>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-3"><label for="view_nationality" class="form-label">Nationality <span class="text-danger">*</span></label>
            <input type="text" id="view_nationality" name="view_nationality" class="form-control-plaintext viewing" maxlength="50" disabled>
        </div>
        <div class="col-md-3"><label for="view_religion" class="form-label">Religion <span class="text-danger">*</span></label>
            <input type="text" id="view_religion" name="view_religion" class="form-control-plaintext viewing" maxlength="50" disabled>
        </div>
        <div class="col-md-3"><label for="view_mothertongue" class="form-label">Mother Tongue <span class="text-danger">*</span></label>
            <input type="text" id="view_mothertongue" name="view_mothertongue" class="form-control-plaintext viewing"
                maxlength="50" disabled>
        </div>
        <div class="col-md-3">
            <label for="view_civilstatus" class="form-label">Civil Status <span class="text-danger">*</span> </label>
            <input type="text" id="view_civilstatus" name="view_civilstatus" class="form-control-plaintext viewing" maxlength="50" disabled>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-3"><label for="view_numberofsiblings" class="form-label">Number of Siblings <span class="text-danger">*</span></label>
            <input type="number" id="view_numberofsiblings" name="view_numberofsiblings" 
                class="form-control-plaintext viewing" max="20" min="0" disabled>
        </div>
    </div>
</section>