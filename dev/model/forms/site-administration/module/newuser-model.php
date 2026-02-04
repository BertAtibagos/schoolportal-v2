<?php 
    session_start();

    $_SESSION['role'] = 'admin';

    // Check if session exists and validate user role
    if (!isset($_SESSION['USERID']) || $_SESSION['role'] !== 'admin') {
        http_response_code(403);
        exit;
    }
?>
<section class="section-new-user">
    <div class="m-auto col-lg-5 pt-4" id="divChange">
        <div class="alert alert-danger alert-dismissible fade d-none" role="alert" id="errorcontainer">
            <p id="errormessage">Content</p>
            <button type="button" class="btn-close text-danger" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <h3>Add a new user</h3>
        
        <hr>
        <form autocomplete="off">
            <div class="col-auto my-2">
                <label for="userid">ID No.</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="userid">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 my-2">
                    <label for="usertype">Account Type</label>
                    <div class="input-group">
                        <select class="form-select" name="" id="usertype">
                            <option selected disabled> -- Select one -- </option>
                            <option value="student"> Student </option>
                            <option value="instructor"> Instructor </option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6 my-2">
                    <label for="userauth">Authentication</label>
                    <div class="input-group">
                        <select class="form-select" name="" id="userauth">
                            <option selected disabled> -- Select one -- </option>
                            <option value="manual"> Manual Login </option>
                            <option value="oauth2"> Oauth2 Services </option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-auto my-2">
                <label for="username">Username</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="username">
                </div>
            </div>
            <div class="col-auto my-2">
                <label for="password">Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" aria-describedby="view-pass">
                    <button class="btn btn-outline-secondary" type="button" id="view-pass"><i class="fa-solid fa-eye"></i></button>
                    <button class="btn btn-outline-secondary" type="button" id="generate-pass"><i class="fa-solid fa-key"></i> Generate </button>
                </div>
                <div class="form-text text-danger" id="passlength"><i class="fa-solid fa-xmark"></i> at least eight (8) characters</div>
                <div class="form-text text-danger" id="passnum"><i class="fa-solid fa-xmark"></i> one (1) number</div>
                <div class="form-text text-danger" id="passchar"><i class="fa-solid fa-xmark"></i> one (1) special character</div>
            </div>
            
            <div class="col-auto my-2">
                <label for="floatingTextarea">Permissions</label>
                <div class="form-input">
                    <textarea class="form-control" id="floatingTextarea"></textarea>
                </div>
            </div>

            <div class="row">
                <div class="col-auto">
                    <input type="checkbox" name="status" id="status" checked>
                    <label for="status" class="user-select-none"> Status </label>
                </div>
                <div class="col-auto">
                    <input type="checkbox" name="active" id="active" checked>
                    <label for="active" class="user-select-none"> Active </label>
                </div>
            </div>

        </form>
    
        <div class="row mt-4">
            <div class="col">
                <button class="btn btn-outline-secondary w-100" id="btnCancel">Cancel</button>
            </div>
            <div class="col">
                <button class="btn btn-primary w-100" id="btnSubmit">Submit</button>
            </div>
        </div>
    </div>

</section>