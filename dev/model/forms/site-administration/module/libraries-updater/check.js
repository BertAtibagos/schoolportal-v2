// Handle UPDATE button click
document.querySelectorAll('.update-btn').forEach(btn => {
  btn.addEventListener('click', async () => {
    const library = btn.dataset.lib;
    const type = btn.dataset.type;

    const formData = new FormData();
    formData.append('library', library);
    formData.append('type', type);

    const res = await fetch('site-administration/module/libraries-updater/update_library.php', {
      method: 'POST',
      body: formData
    });

    const data = await res.json();
    alert(data.message);
    // location.reload();
  });
});

// Handle REVERT toggle button click
document.querySelectorAll('.revert-toggle-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    const updateBtn = btn.previousElementSibling; // assumes order: Update -> Revert toggle -> Select -> Revert
    const select = btn.nextElementSibling;
    const revertBtn = select.nextElementSibling;

    const isOpen = !select.classList.contains('d-none');

    if (isOpen) {
      // Hide dropdown + revert button, show update button, reset text
      select.classList.add('d-none');
      revertBtn.classList.add('d-none');
      updateBtn.classList.remove('d-none');
      btn.querySelector('.revert-text').textContent = 'Revert Version';
    } else {
      // Show dropdown + revert button, hide update button, change text to Cancel
      select.classList.remove('d-none');
      revertBtn.classList.remove('d-none');
      updateBtn.classList.add('d-none');
      btn.querySelector('.revert-text').textContent = 'Cancel';
    }
  });
});

// Handle DROPDOWN change: hide if reset
document.querySelectorAll('.revert-select').forEach(select => {
  select.addEventListener('change', () => {
    const revertBtn = select.nextElementSibling;
    const toggleBtn = revertBtn.previousElementSibling.previousElementSibling;
    const updateBtn = toggleBtn.previousElementSibling;

    if (!select.value) {
      // Reset: hide dropdown & revert button, show update, reset text
      select.classList.add('d-none');
      revertBtn.classList.add('d-none');
      updateBtn.classList.remove('d-none');
      toggleBtn.querySelector('.revert-text').textContent = 'Revert Version';
    }
  });
});

// Handle REVERT action button click
document.querySelectorAll('.revert-btn').forEach(btn => {
  btn.addEventListener('click', async () => {
    const select = btn.previousElementSibling;
    const backup = select.value;
    const filePath = btn.dataset.path;

    if (!backup) {
      alert('Please select a backup version.');
      return;
    }

    const formData = new FormData();
    formData.append('backup', backup);
    formData.append('filePath', filePath);

    const res = await fetch('site-administration/module/libraries-updater/revert_library.php', {
      method: 'POST',
      body: formData
    });

    const data = await res.json();
    alert(data.message);
    location.reload();
  });
});