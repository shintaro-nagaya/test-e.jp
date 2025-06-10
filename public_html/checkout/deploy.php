<?php
/**
 * webhookを受け取り、コミットされたブランチを確認してデプロイする
 */
if(!isset($_POST['payload'])) exit();
$payload = json_decode($_POST['payload']);
if(!$payload || !isset($payload->ref))exit();

require_once(__DIR__. "/env.php");
if($payload->ref === PROD_REF) {
    checkout(PROD_BRUNCH);
} elseif($payload->ref === STG_REF) {
    logging(STG_REF. " checkout relay to ". STG_CHECKOUT_ENDPOINT);
    file_get_contents(STG_CHECKOUT_ENDPOINT);
}
