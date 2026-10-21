<?php
// Sends the contact form to the Alpha Dépannage mailbox via the OVH hosting's mail().
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false]);
    exit;
}

$to = 'Alphadepannage@hotmail.com';

$name    = trim($_POST['name'] ?? '');
$phone   = trim($_POST['phone'] ?? '');
$email   = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

// Strip line breaks from header-bound fields to block header injection.
$name  = str_replace(["\r", "\n"], ' ', $name);
$phone = str_replace(["\r", "\n"], ' ', $phone);

if ($name === '' || $message === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['ok' => false]);
    exit;
}

$subject = '=?UTF-8?B?' . base64_encode('Nouveau message du site – ' . $name) . '?=';

$body  = "Nom : $name\n";
$body .= "Téléphone : " . ($phone !== '' ? $phone : '-') . "\n";
$body .= "Email : $email\n\n";
$body .= "Message :\n$message\n";

$headers  = "From: Alpha Dépannage <noreply@alphadepannagevoiture.be>\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

if (mail($to, $subject, $body, $headers)) {
    echo json_encode(['ok' => true]);
} else {
    http_response_code(500);
    echo json_encode(['ok' => false]);
}
