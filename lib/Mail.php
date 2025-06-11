<?php

class Mail {
    private $to;
    private $subject;
    private $message;
    private $headers;
    private $error;

    public function __construct() {
        $this->headers = "MIME-Version: 1.0" . "\r\n";
        $this->headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    }

    public function setTo($to) {
        $this->to = $to;
        return $this;
    }

    public function setSubject($subject) {
        $this->subject = $subject;
        return $this;
    }

    public function setMessage($message) {
        $this->message = $message;
        return $this;
    }

    public function setFrom($from) {
        $this->headers .= "From: " . $from . "\r\n";
        return $this;
    }

    public function addCC($cc) {
        $this->headers .= "Cc: " . $cc . "\r\n";
        return $this;
    }

    public function addBCC($bcc) {
        $this->headers .= "Bcc: " . $bcc . "\r\n";
        return $this;
    }

    public function send() {
        if (empty($this->to) || empty($this->subject) || empty($this->message)) {
            $this->error = "Les champs destinataire, sujet et message sont obligatoires.";
            return false;
        }

        if (mail($this->to, $this->subject, $this->message, $this->headers)) {
            return true;
        } else {
            $this->error = "Échec de l'envoi de l'e-mail.";
            return false;
        }
    }

    public function getError() {
        return $this->error;
    }
}