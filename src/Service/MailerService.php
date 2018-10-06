<?php
/**
 * Created by PhpStorm.
 * User: Nick
 * Date: 8/25/2018
 * Time: 5:08 PM
 */

namespace App\Service;

use App\Entity\Account;

class MailerService
{
    //TODO
//    public function sendMail($name, \Swift_Mailer $mailer)
//    {
//        $message = (new \Swift_Message('New Query'))
//            ->setFrom('send@example.com')
//            ->setTo('nikolay86bg@gmail.com')
//            ->setBody(
//                $this->renderView(
//                // templates/emails/registration.html.twig
//                    'emails/new_query.html.twig',
//                    array('name' => $name)
//                ),
//                'text/html'
//            )
//        ;
//
//        $mailer->send($message);
//
//        return $this->render(...);
//    }

    /**
     * @param Account $account
     * @return bool
     */
    public function sendMail(Account $account)
    {
        $to      = 'igankor@gmail.com';
        $subject = 'New Query!';
        $message = 'New Query for Account: '.$account->getName();
        $headers = 'From: aqualife@symfony.nikolaynikolov.net' . "\r\n" .
            'Reply-To: aqualife@symfony.nikolaynikolov.net' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        mail($to, $subject, $message, $headers);

        return true;
    }


}