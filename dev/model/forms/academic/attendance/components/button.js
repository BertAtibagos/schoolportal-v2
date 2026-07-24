export function buttonComponent(label, type = "button", classes = "btn btn-primary") {
    return (
        `<div class="col-md-4 d-flex align-items-end">
            <button type="${type}" class="${classes}" id="searchButton">${label}</button>
        </div>`
    );
}