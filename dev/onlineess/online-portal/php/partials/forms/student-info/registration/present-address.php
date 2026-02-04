
<section id="present-address">
    <div align="center" class="headline">
        <h2>PRESENT ADDRESS</h2>
    </div><br>
    
    <div class="row">
        <div class="col-md-6">
            <label for="view_presentstreetaddress" class="form-label">Street Address <span class="text-danger">*</span></label>
            <input type="text" id="view_presentstreetaddress" name="view_presentstreetaddress" 
                class="form-control-plaintext viewing" maxlength="50" required readonly>
        </div>
        <div class="col-md-3">
            <label for="view_presentprovinceid" class="form-label">Province <span class="text-danger">*</span></label>
            <input type="text" id="view_presentprovinceid" name="view_presentprovinceid" 
               class="form-control-plaintext viewing" maxlength="50" required readonly>
        </div>
        <div class="col-md-3">
            <label for="view_presentmunicipalityid" class="form-label">Municipality <span class="text-danger">*</span></label>
            <input type="text" id="view_presentmunicipalityid" name="view_presentmunicipalityid" 
                class="form-control-plaintext viewing" maxlength="50" required readonly>

        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-6">
            <label for="view_presentbarangayid" class="form-label">Barangay <span class="text-danger">*</span></label>
            <input type="text" id="view_presentbarangayid" name="view_presentbarangayid"
                class="form-control-plaintext viewing" maxlength="50" required readonly>
        </div>
        <div class="col-md-6">
            <label for="view_presentzipcode" class="form-label">Zipcode <span class="text-danger">*</span></label>
            <input type="text" id="view_presentzipcode" name="view_presentzipcode" class="form-control-plaintext viewing"
                onkeypress="return EnterOnlyNumberKey(event)" maxlength="10" required readonly>
        </div>
    </div>
</section>