<?php

namespace MyApp\utils;

use \Dotenv\Dotenv;
use MyApp\utils\UserSession;
use \PHPMailer\PHPMailer\PHPMailer;
use \PHPMailer\PHPMailer\SMTP;
use \PHPMailer\PHPMailer\Exception;

require __DIR__ . "/../../vendor/autoload.php";

session_start();
class Mailer
{
    public static function sendConfirmationEmail(string $recipientEmail, string $hash, string $redirectTo): void
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . "/../../");
        $dotenv->safeLoad();
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV["SMTP_USERNAME"];
            $mail->Password = $_ENV["SMTP_PASSWORD"];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->setFrom($_ENV["SMTP_FROM_EMAIL"], $_ENV["SMTP_FROM_NAME"]);
            $mail->addAddress($recipientEmail);
            $mail->isHTML(true);
            $mail->Subject = 'Account Confirmation Required';
            $mail->Body = '
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Confirmation Email</title>
            </head>

            <body style="font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f8fafc; color: #333;">
                <table align="center" border="0" cellpadding="0" cellspacing="0"
                    style="max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    <tr>
                        <td
                            style="padding: 20px; text-align: center; background-color: #1d4ed8; color: #ffffff; border-top-left-radius: 8px; border-top-right-radius: 8px;">
                            <h1 style="margin: 0; font-size: 24px;">Confirm Your Email</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 20px;">
                            <p style="margin: 0 0 10px;">Hi there,</p>
                            <p style="margin: 0 0 20px;">Thank you for signing up! Please confirm your email address by clicking the
                                button below:</p>
                            <div style="text-align: center; margin: 20px 0;">
                                <a href="http://127.0.0.1/liveconnect/public/verifyEmail.php?email=' . $recipientEmail . '&hash=' . $hash . '"
                                    style="text-decoration: none; background-color: #3b82f6; color: #ffffff; padding: 10px 20px; border-radius: 5px; font-size: 16px; display: inline-block;">
                                    Confirm Email
                                </a>
                            </div>
                            <p style="margin: 20px 0 0; font-size: 14px; color: #6b7280;">If you didn\'t sign up for this account,
                                you can safely ignore this email.</p>
                        </td>
                    </tr>
                    <tr>
                        <td
                            style="padding: 10px; text-align: center; background-color: #f1f5f9; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px; font-size: 12px; color: #9ca3af;">
                            &copy; 2024 File-To. All rights reserved.
                        </td>
                    </tr>
                </table>
            </body>

            </html>
            ';
            $mail->AltBody = 'Thank you for signing up! Please confirm your email address by clicking this link: http://127.0.0.1/liveconnect/public/verifyEmail.php?email=' . $recipientEmail . '&hash=' . $hash;
            if ($mail->send()) {
                $_SESSION["emailSent"] = true;
                UserSession::redirectTo('emailSent.php');
            }
            $_SESSION["error_message"] = "The confirmation email could not be sent. Server Error: {$mail->ErrorInfo}";
        } catch (Exception $e) {
            throw new Exception("Message could not be sent. Mailer Error: {$mail->ErrorInfo}", (int) $e->getCode());
        }
    }
}
