<?php

include '../config.php';

$req = '
SELECT u.id, pseudo FROM user u 
LEFT JOIN scores p on p.id_user = u.id
WHERE u.is_actif = 1
GROUP BY u.id, u.pseudo
ORDER BY
    CASE 
        WHEN u.id IN (8,9,10,11) THEN 1
        WHEN COUNT(p.id) > 10 THEN 0
        ELSE 1
    END,    
    u.pseudo ASC';
//$stmt = $mysqli->prepare("SELECT id, pseudo FROM user WHERE is_actif = 1 ORDER BY pseudo");
$stmt = $mysqli->prepare($req);

$stmt->execute();
$result = $stmt->get_result();

$user_data = [];
while ($row = $result->fetch_assoc()) {
    $user_data[] = $row;
}

$stmt->close();
echo json_encode($user_data);
?>
