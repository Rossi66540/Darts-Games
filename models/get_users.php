<?php

include '../config.php';

$stmt = $mysqli->prepare("SELECT id, pseudo FROM user WHERE is_actif = 1 ORDER BY pseudo");
$stmt->execute();
$result = $stmt->get_result();

$user_data = [];
while ($row = $result->fetch_assoc()) {
    $user_data[] = $row;
}

$stmt->close();
echo json_encode($user_data);
?>
