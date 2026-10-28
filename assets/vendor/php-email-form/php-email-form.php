<?php

class PHP_Email_Form {
  public $to;
  public $from_name;
  public $from_email;
  public $subject;
  public $smtp = array();
  public $ajax = false;
  private $messages = array();

  public function add_message($content, $label = '', $length = 0) {
    $entry = ($label ? "$label: " : "") . $content;
    $this->messages[] = $entry;
  }

  public function send() {
    $message_body = implode("\n", $this->messages);

    $headers = "From: {$this->from_name} <{$this->from_email}>\r\n";
    $headers .= "Reply-To: {$this->from_email}\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    if (!empty($this->smtp)) {
      return $this->send_smtp($message_body);
    } else {
      if (mail($this->to, $this->subject, $message_body, $headers)) {
        return 'Mesaj gönderildi.';
      } else {
        return 'Mesaj gönderilirken bir hata oluştu.';
      }
    }
  }

  private function send_smtp($body) {
    require_once __DIR__ . '/PHPMailer/PHPMailer.php';
    require_once __DIR__ . '/PHPMailer/SMTP.php';
    require_once __DIR__ . '/PHPMailer/Exception.php';

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    try {
      $mail->isSMTP();
      $mail->Host = $this->smtp['host'];
      $mail->SMTPAuth = true;
      $mail->Username = $this->smtp['username'];
      $mail->Password = $this->smtp['password'];
      $mail->SMTPSecure = $this->smtp['encryption'] ?? 'tls';
      $mail->Port = $this->smtp['port'];

      $mail->setFrom($this->from_email, $this->from_name);
      $mail->addAddress($this->to);
      $mail->Subject = $this->subject;
      $mail->Body = $body;

      $mail->send();
      return 'Mesaj gönderildi.';
    } catch (Exception $e) {
      return "Mail gönderilemedi. Hata: {$mail->ErrorInfo}";
    }
  }
}
