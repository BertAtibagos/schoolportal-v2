
/* When the user clicks on the button, 
toggle between hiding and showing the dropdown content */
$(document).ready(function () {
    $("#addBtn").click(function() {
    
        var firstName = $("#1").val();
        var lastName = $("#2").val();
        var email = $("#3").val();
        
        // if(firstName==''||lastName==''||email==''||message=='') {
        // alert("Please fill all fields.");
        // return false;
        // }
        
        $.ajax({
        type: "POST",
        url: "model/forms/manage/gradingscale/gradingtemplate-controller.php",
        data: {
            firstName: gscale_name,
            lastName: details,
            email: acadyr
        },
        cache: false,
        success: function(data) {
        
        }
        // error: function(xhr, status, error) {
        // console.error(xhr);
        // }
        });
        
        });
// Denotes total number of rows
var rowIdx = 0;
var rowIdx2 = 0;

// jQuery button click event to add a row
$('#addBtn2').on('click', function () {
    rowIdx2 = 0;
    rowIdx++;

    // Adding a row inside the tbody.
    $('#divtbl').append(`
    <table id="table${rowIdx}" class="comptbl">
        <tbody id="tbody">
        <tr id="R${rowIdx}">
            <td class="row-index" id="D${rowIdx}">
            <input type="text" id="R${rowIdx}" placeholder="Component Name ${rowIdx}" " value=""  />
            </td>

            <td class="row-index ">
            <input type="text" id="R${rowIdx}" placeholder="Code" " value=""  />
            </td>

            <td class="row-index ">
            <input type="text" id="R${rowIdx}" placeholder="Percentage %" " value=""  />
            </td>

            <td class="" >
            <button class="btn btn-primary addComp" type="button">Add</button>
            <button class="btn btn-danger remove" type="button"><i class="fa fa fa-trash"></i></button>
            </td>
        </tr>
        </tbody>
    </table>
    `);

});

$('#divtbl').on('click', '.addComp', function () {
    rowIdx2++;

    // Getting all the rows next to the row
    // containing the clicked button
    var childss = $(this).closest('table').attr('id');

    // Gets the row number from <tr> id.
    var dig = parseInt(childss.substring(5));
    var chi = $('table tr:last').index();
    $("p").text(chi);
    // Gets the row number from <tr> id.
    //var dig = parseInt(chil.substring(5));

    // Appending the current row.
    $(this).closest('table').append(`
    <tr id="R${dig}${rowIdx2}">
    <td></td>
    <td class="row-index ">
        <input type="text" id="R${dig}${rowIdx2}" placeholder=" Name ${dig} ${chi}" " value=""  />
    </td>

    <td class="row-index ">
        <input type="text" id="R${dig}${rowIdx2}" placeholder="Description" " value=""  />
    </td>

    <td class="row-index ">
        <input type="text" id="R${dig}${rowIdx2}" name = "total" placeholder="Percentage %" " value=""/>
    </td>

    <td class="">
        <button class="btn btn-warning removeComp" type="button"><i class="fa fa fa-minus" style="color: white"></i></button>
    </td>
    </tr>`);
    chi = 0;
});

// jQuery button click event to remove a row.
$('#divtbl').on('click', '.remove', function () {

    // Getting all the rows next to the row
    // containing the clicked button
    var child = $(this).closest('tr').nextAll();

    // Iterating across all the rows
    // obtained to change the index
    child.each(function () {

    // Getting <tr> id.
    var id = $(this).attr('id');

    // Getting the <p> inside the .row-index class.
    var idx = $(this).children('.row-index').children('input');

    // Gets the row number from <tr> id.
    var dig = parseInt(id.substring(1));

    // Modifying row index.
    idx.html(`Row ${dig - 1}`);

    // Modifying row id.
    $(this).attr('id', `R${dig - 1}`);
    });

    // Removing the current row.
    $(this).closest('table').remove();

    // Decreasing total number of rows by 1.
    rowIdx--;
});

$(this).on('click', '.removeComp', function () {

    // Getting all the rows next to the row
    // containing the clicked button
    var child = $(this).closest('tr').nextAll();

    // Iterating across all the rows
    // obtained to change the index
    child.each(function () {

    // Getting <tr> id.
    var id = $(this).attr('id');

    // Getting the <p> inside the .row-index class.
    var idx = $(this).children('.row-index').children('input');

    // Gets the row number from <tr> id.
    var dig = parseInt(id.substring(1));

    // Modifying row index.
    idx.html(`Row ${dig - 1}`);

    // Modifying row id.
    $(this).attr('id', `R${dig - 1}`);
    });

    // Removing the current row.
    $(this).closest('tr').remove();

    // Decreasing total number of rows by 1.
    rowIdx2--;
});

});