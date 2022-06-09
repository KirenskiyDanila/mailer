<?php

require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;

$ini_array = parse_ini_file('secret.ini', true);

$pdo = new PDO('pgsql:host=' . $ini_array['host'] . ';port=' . $ini_array['port'] .
    ';dbname=' . $ini_array['db'] . ';user=' . $ini_array['user'] . ';password=' . $ini_array['pass']);

$email = $_POST['email'];
// проверка последней заявки для этой почты
$query = "SELECT *
              FROM mail 
              WHERE email = :email
              ORDER BY date DESC 
              LIMIT 1";
$params = [
    'email' => "" . $email . ""
];
$stmt = $pdo->prepare($query);
$stmt->execute($params);
if ($stmt->rowCount() != 0) {
    $row = $stmt->fetch(PDO::FETCH_LAZY);
    if ((strtotime($row->date) + 3600) > strtotime("now")) {
        // ответ, отказ из-за времени
        echo json_encode([
            // добавляем лишний час из-за таймзоны
            'time' => date('H:i:s d.m.Y', (strtotime($row->date) + 7200)),
            'timeError' => true
        ]);
        exit();

    }
}


// получаем данные из файла
$FIO = $_POST['FIO'];

$firstName = explode(" ",$FIO)[0];
$secondName = explode(" ",$FIO)[1];
$patronymic = explode(" ",$FIO)[2];
$adminUsername = $ini_array['username'];
$adminPassword = $ini_array['password'];
$secondaryEmail = $ini_array['secondaryEmail'];
$phone = $_POST['phone'];
$comment = $_POST['comment'];

$mail = new PHPMailer(true);
// отправка письма на почту
try {
    $adminEmail = $adminUsername . "@mail.ru";
    $mail->isSMTP();
    $mail->Host = 'ssl://smtp.mail.ru';
    $mail->Port = 465;
    $mail->SMTPAuth = true;
    $mail->Username = $adminUsername;
    $mail->Password = $adminPassword;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

    $mail->setFrom($adminEmail, 'Feedback');
    $mail->addAddress($secondaryEmail, 'Feedback');
    $mail->addReplyTo($adminEmail, 'Feedback');

    $mail->isHTML(true);
    $mail->Subject = 'Notification!';
    $mail->Body = 'Было оставлено сообщение в форме обратной связи.<br><b>Автор: ' . $FIO . '</b>.<br><b>Email автора: ' . $email . '</b>.<br><b>Телефон: ' . $phone . '</b>.<br><b>Сообщение: ' . $comment . '</b>.';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();

    // добавляем данные в бд
    $query = "INSERT INTO mail (name, email, phone, comment) VALUES (:name, :email, :phone, :comment)";
    $params = [
        'name' => $FIO,
        'email' => $email,
        'phone' => $phone,
        'comment' => $comment,
    ];
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    // ответ
    echo json_encode([
        'email' => $email,
        'comment' => $comment,
        'phone' => $phone,
        'firstname' => $firstName,
        'secondname' => $secondName,
        'patronymic' => $patronymic,
        'timeError' => false
    ]);
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

