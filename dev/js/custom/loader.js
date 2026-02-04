export function toggleLoader(action) {
    const elements = document.querySelectorAll('input, select, button, textarea, a');
    const isDisable = action === 'disable';

    elements.forEach(el => {
        if (el.id !== 'disableBtn') {
            if ('disabled' in el) {
                el.disabled = isDisable;
            }

            el.style.pointerEvents = isDisable ? 'none' : 'auto';
            el.style.opacity = isDisable ? '0.6' : '1';
        }
    });

    // Toggle full-page loader
    const loader = document.getElementById('divLoader');
    if (loader) {
        loader.style.display = isDisable ? 'flex' : 'none';
    }
}
