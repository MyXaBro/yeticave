<?php
require_once("helpers.php");
require_once("data.php");
require_once("function.php");
require_once("init.php");
require_once("models.php");
require_once('vendor/autoload.php');

/**
 * @var array $connect;
 * @var object $lots лоты с завершёнными торгами
 */
$lots = get_lot_date_finish($connect);
$bets_win =[];

foreach($lots as $lot){
    $id = (int)$lot["id"];
    /**
     * @var object $bet последняя ставка
     */
    $bet = get_last_bet($connect, $id);

    if(!empty($bet)){
        $id_lot = $lot["id"];
        $bets_win[] = $bet;
        $res = add_winner($connect, $bet["user_id"], $id);
    }

    if(!empty($bets_win)){
        $win_users = [];
        foreach($bets_win as $bet){
            $id = intval($bet["lot_id"]);
            $data = get_user_win($connect, $id);
            $win_users[] = $data;
        }

        $recipients =[];
        foreach($bets_win as $bet){
            $id = intval($bet["user_id"]);
            $user_date = get_user_contacts($connect, $id);
            $recipients[$user_date["email"]] = $user_date["user_name"];
        }

        /**
         * @var class Swift_SmtpTransport Отправляет сообщение по протоколу smtp
         */
        $transport = new Swift_SmtpTransport("smtp.mail.ru", 465);
        $transport -> setUsername("rulez695@mail.ru");
        $transport -> setPassword("2LSBZdPMjDcY5YGDyFZq");

        $mailer = new Swift_Mailer($transport);

        $message = new Swift_Message();
        $message -> setSubject("Ваша ставка победила!");
        $message -> setFrom(["rulez699@gmail.com" => "Yeticave"]);
        $message -> setTo($recipients);

        $msg_content = include_template("email.php", ["win_users" => $win_users]);
        $message -> setBody($msg_content, "text/html");

        $result = $mailer -> send($message);

        if($result){
            print("Рассылка успешно отправлена!");
        }else{
            print("Не удалось отправить рассылку");
        }
    }
}
