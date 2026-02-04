const sidebar = document.getElementById('sidebar');
const module_name = document.querySelectorAll('.module-name');
const toggleBtn = document.getElementById('toggleBtn');

document.addEventListener("DOMContentLoaded", function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'))
    const tooltips = tooltipTriggerList.map(el =>
        new bootstrap.Tooltip(el, {
            trigger: 'hover'
        })
    )

    toggleBtn.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
        if (sidebar.classList.contains('collapsed')) {
            tooltips.forEach(t => t.enable());
			module_name.forEach(t => t.style.display = "none");
        } else {
            tooltips.forEach(t => t.disable());
            module_name.forEach(t => t.style.display = "block");
        }
    });

    // start with tooltips disabled (expanded sidebar)
    tooltips.forEach(t => t.disable());
});

document.getElementById('contentContainer').addEventListener('click', ()=>{
    if (window.matchMedia("(orientation: portrait)").matches) {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.add('collapsed');
		module_name.forEach(t => t.style.display = "none");
    }
})

document.querySelectorAll('.parent').forEach( menu => {
    menu.addEventListener('click', () => {
        if (sidebar.classList.contains('collapsed')){
            toggleBtn.click();
        }
    })
})