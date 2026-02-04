<?php
header('Content-Type: application/json');

function revertFile($filePath, $backupFile) {
  $dir = dirname($filePath) . '/backups/';
  $backupPath = $dir . $backupFile;

  if (file_exists($backupPath)) {
    // Backup current file before revert
    rename($filePath, $dir . basename($filePath) . '.' . time() . '.bak');
    rename($backupPath, $filePath);
    return true;
  }
  return false;
}

$backupFile = $_POST['backup'];
$filePath = $_POST['filePath'];

if (revertFile($filePath, $backupFile)) {
  echo json_encode(['status' => 'success', 'message' => 'Reverted successfully']);
} else {
  echo json_encode(['status' => 'error', 'message' => 'Revert failed']);
}
?>
