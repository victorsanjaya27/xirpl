<?php
class PHP_Email_Form {
    public $to = '';
    public $from_name = '';
    public $from_email = '';
    public $subject = '';
    public $smtp = array();
    public $messages = array();
    public $ajax = false;

    public function add_message($content, $label, $priority = 0) {
        $this->messages[] = array(
            'content' => $content,
            'label' => $label,
            'priority' => $priority
        );
    }

    public function send() {
        if (empty($this->to)) {
            return 'Error: Email tujuan tidak diatur!';
        }

        if (!filter_var($this->from_email, FILTER_VALIDATE_EMAIL)) {
            return 'Error: Email pengirim tidak valid!';
        }

        $message_content = "";
        foreach ($this->messages as $message) {
            $message_content .= $message['label'] . ": " . $message['content'] . "\n";
        }

        $headers = "From: " . $this->from_name . " <" . $this->from_email . ">\r\n";
        $headers .= "Reply-To: " . $this->from_email . "\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        if (!empty($this->smtp)) {
            return $this->send_smtp($message_content);
        } else {
            if (mail($this->to, $this->subject, $message_content, $headers)) {
                return 'OK';
            } else {
                return 'Error: Gagal mengirim email!';
            }
        }
    }

    private function send_smtp($message_content) {
        require 'PHPMailer/PHPMailer.php';
        require 'PHPMailer/SMTP.php';
        require 'PHPMailer/Exception.php';

        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->isSMTP();
        $mail->Host = $this->smtp['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $this->smtp['username'];
        $mail->Password = $this->smtp['password'];
        $mail->SMTPSecure = 'tls';
        $mail->Port = $this->smtp['port'];

        $mail->setFrom($this->from_email, $this->from_name);
        $mail->addAddress($this->to);
        $mail->Subject = $this->subject;
        $mail->Body = $message_content;

        if ($mail->send()) {
            return 'OK';
        } else {
            return 'Error: ' . $mail->ErrorInfo;
        }
    }
}
?>
