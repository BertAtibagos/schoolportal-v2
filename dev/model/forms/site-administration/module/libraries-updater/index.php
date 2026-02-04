<?php
$libs = json_decode(file_get_contents('site-administration/module/libraries-updater/libraries.json'), true);

function getBackups($filePath)
{
    $backups = [];
    $dir = dirname($filePath) . '/backups/';
    if (is_dir($dir)) {
        $baseName = basename($filePath);
        foreach (glob($dir . $baseName . '.*.bak') as $file) {
            $backups[] = basename($file);
        }
    }
    return $backups;
}
?>
<?php foreach ($libs as $lib): ?>
    <div class="mb-4">
        <h5><?php echo htmlspecialchars($lib['display_name']); ?> (v<?php echo $lib['current_version']; ?>)</h5>

        <?php foreach ($lib['files'] as $file): ?>
            <?php
            $backups = getBackups($file['path']);
            ?>
            <div class="card mb-2">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <p class="mb-0"><?php echo strtoupper($file['type']); ?> File</p>
                    </div>

                    <div class="d-flex align-items-center">
                        <button class="btn btn-primary update-btn"
                            data-lib="<?php echo $lib['name']; ?>"
                            data-type="<?php echo $file['type']; ?>">
                            Update
                        </button>

                        <button class="btn btn-warning ms-2 revert-toggle-btn">
                            <span class="revert-text">Revert Version</span>
                        </button>

                        <select class="form-select form-select-sm revert-select d-none ms-2"
                            data-path="<?php echo $file['path']; ?>">
                            <option value="">Select version...</option>
                            <?php foreach (getBackups($file['path']) as $backup): ?>
                                <option value="<?php echo $backup; ?>"><?php echo $backup; ?></option>
                            <?php endforeach; ?>
                        </select>

                        <button class="btn btn-secondary revert-btn d-none ms-2"
                            data-path="<?php echo $file['path']; ?>">
                            Revert
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>

<script src="site-administration/module/libraries-updater/check.js?t=<?php echo time(); ?>"></script>