<?php
error_reporting(E_ALL);

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "Util" . DIRECTORY_SEPARATOR . "Curler.php";
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "Util" . DIRECTORY_SEPARATOR . "Timer.php";

require_once "lib/0MQ/0MQ/Worker.php";

$wrk = new Worker("tcp://192.168.123.1:5557", 0, 2500, 10000);

$wrk->setExecuter(function ($data)
{
    $id = intval(substr($data, 0, 3));
    $zip = substr($data, 3);
    Timer::set("upload");
    $curler = Curler::create()
        ->setUrl("http://data.tiberium-alliances.com/savedata")
        ->setPostData(Curler::encodePost(
            array(
                'key' => "wohdfo97wg4iurvfdc t7yaigvrufbs",
                'world' => $id,
                'data' => $zip)
        )
    )
        ->withHeaders(false);
    $resp = $curler->post();
    $curler->close();
    print_r("Uploading $id... $resp: " . Timer::get("upload") . "\r\n\r\n");
    return $resp;
});

$wrk->work();

