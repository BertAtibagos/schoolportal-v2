<section>
    <div align="center" class="headline">
        <h2>PERMANENT ADDRESS</h2>
    </div><br>

    <div class="row">
        <div class="col-md-6">
            <label for="view_permanentstreetaddress" class="form-label">Street Address <span class="text-danger">*</span></label>
            <input type="text" id="view_permanentstreetaddress" name="view_permanentstreetaddress" 
                class="form-control-plaintext viewing" maxlength="50" required readonly>
        </div>
        <div class="col-md-3">
            <label for="view_permanentprovinceid" class="form-label">Province <span class="text-danger">*</span></label>
            <input type="text" id="view_permanentprovinceid" name="view_permanentprovinceid" class="form-control-plaintext viewing" maxlength="50" required readonly>
        </div>
        <div class="col-md-3">
            <label for="view_permanentmunicipalityid" class="form-label">Municipality <span class="text-danger">*</span></label>
            <input type="text" id="view_permanentmunicipalityid" name="view_permanentmunicipalityid" 
                class="form-control-plaintext viewing" maxlength="50" required readonly>
        </div>
    </div>

    <br>
    
    <div class="row">
        <div class="col-md-6">
            <label for="view_permanentbarangayid" class="form-label">Barangay <span class="text-danger">*</span></label>
            <input type="text" id="view_permanentbarangayid" name="view_permanentbarangayid" class="form-control-plaintext viewing" maxlength="50" required readonly>
        </div>
        <div class="col-md-6">
            <label for="view_permanentzipcode" class="form-label">Zipcode <span class="text-danger">*</span></label>
            <input type="text" id="view_permanentzipcode" name="view_permanentzipcode" 
                class="form-control-plaintext viewing" onkeypress="return EnterOnlyNumberKey(event)" maxlength="10" required readonly>
        </div>
        <!-- <div class="col-md-4 row pt-3">
            <input type="checkbox" id="different_address" class="col-md-2 margin-auto" name="different_address" />
            <label for="different_address" class="col-md-10">Present address different from permanent address.</label>
        </div> -->
    </div>
</section>