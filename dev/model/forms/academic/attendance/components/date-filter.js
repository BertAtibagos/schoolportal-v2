export function dateFilterComponent(){
    return(
        `<div class="col-md-4">
            <label class="form-label">Date Search</label>

            <div class="row g-2">
                <div class="col-6">
                    <input type="date" class="form-control" id="dateFilterStart">
                </div>

                <div class="col-6">
                    <input type="date" class="form-control" id="dateFilterEnd">
                </div>
            </div>
        </div>`
    );
}