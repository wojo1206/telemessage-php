<?php
    // initializing data and autoloading required files
    $tm = TeleMessage::get();

    $auth = new AuthenticationDetails();
    $auth->setUsername("john_donne");
    $auth->setPassword("12345678");

    $recp = new Recipient();
    $recp->setType("EMAIL");
    $recp->setValue("someemail@somedomain.com");

    $m = new Message();
    $m->addRecipient($recp);
    $fm = new FileMessage();
    $fm->setFilename("file.png");
    $fm->setMimetype("image/png");
    $imgPath = pathinfo(__FILE__, PATHINFO_DIRNAME) . "/file.png";
    $fm->setValue(base64_encode(file_get_contents($imgPath)));

    $m->addFilemessage($fm);

    $data = $tm->generateSend($auth, $m);
    //creating header for http post request
    $myHeader = array(
        "MIME-Version: 1.0",
        "Content-type: text/json; charset=utf-8"
    );
    //creating and initiating curl
    $ch = curl_init();
    //setting curl/http headers
    curl_setopt($ch, CURLOPT_POST,1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_URL, TeleMessage::SEND_URL);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $myHeader);
    $postResult = curl_exec($ch);
    curl_close($ch);

    if ($postResult != "") {
        $res = $tm->getResponse($postResult);
        echo "Result code: " . $res->getResultCode();
        if ($res->getResultCode() == TeleMessage::SUCCESS_SEND) {
            echo ", Message key: " . $res->getMessageKey() . ", message id: " . $res->getMessageID();
        } else {
            echo ", Result description: " . $res->getResultDescription();
        }
    }
?>