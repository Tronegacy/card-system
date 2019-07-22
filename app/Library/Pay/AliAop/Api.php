<?php
namespace App\Library\Pay\AliAop; use App\Library\Pay\ApiInterface; use Illuminate\Support\Facades\Log; class Api implements ApiInterface { private $url_notify = ''; private $url_return = ''; private $aop = null; public function __construct($sp3c46ab) { $this->url_notify = SYS_URL_API . '/pay/notify/' . $sp3c46ab; $this->url_return = SYS_URL . '/pay/return/' . $sp3c46ab; } private function aop($sp9d4382) { if ($this->aop === null) { $spcaeab0 = \Alipay\Key\AlipayKeyPair::create('-----BEGIN RSA PRIVATE KEY-----
' . wordwrap($sp9d4382['merchant_private_key'], 64, '
', true) . '
-----END RSA PRIVATE KEY-----', '-----BEGIN PUBLIC KEY-----
' . wordwrap($sp9d4382['alipay_public_key'], 64, '
', true) . '
-----END PUBLIC KEY-----'); $this->aop = new \Alipay\AopClient($sp9d4382['app_id'], $spcaeab0); } return $this->aop; } function goPay($sp9d4382, $sp2e47fc, $spd4e90d, $spd0789a, $sp076ec7) { $spf59c91 = sprintf('%.2f', $sp076ec7 / 100); if ($sp9d4382['payway'] === 'f2f') { $spf066f3 = \Alipay\AlipayRequestFactory::create('alipay.trade.precreate', array('notify_url' => $this->url_notify, 'biz_content' => array('out_trade_no' => $sp2e47fc, 'total_amount' => $spf59c91, 'subject' => $spd4e90d))); $spb72f32 = $this->aop($sp9d4382)->execute($spf066f3)->getData(); header('location: /qrcode/pay/' . $sp2e47fc . '/aliqr?url=' . urlencode($spb72f32['qr_code'])); } elseif ($sp9d4382['payway'] === 'pc') { $spf066f3 = \Alipay\AlipayRequestFactory::create('alipay.trade.page.pay', array('return_url' => $this->url_return, 'notify_url' => $this->url_notify, 'biz_content' => array('out_trade_no' => $sp2e47fc, 'product_code' => 'FAST_INSTANT_TRADE_PAY', 'total_amount' => $spf59c91, 'subject' => $spd4e90d))); $spb72f32 = $this->aop($sp9d4382)->pageExecuteUrl($spf066f3); header('location: ' . $spb72f32); } elseif ($sp9d4382['payway'] === 'mobile') { $spf066f3 = \Alipay\AlipayRequestFactory::create('alipay.trade.wap.pay', array('return_url' => $this->url_return, 'notify_url' => $this->url_notify, 'biz_content' => array('out_trade_no' => $sp2e47fc, 'product_code' => 'QUICK_WAP_WAY', 'total_amount' => $spf59c91, 'subject' => $spd4e90d))); $spb72f32 = $this->aop($sp9d4382)->pageExecuteUrl($spf066f3); header('location: ' . $spb72f32); } die; } function verify($sp9d4382, $sp9a4d97) { $sp7b2182 = isset($sp9d4382['isNotify']) && $sp9d4382['isNotify']; if ($sp7b2182) { if ($this->aop($sp9d4382)->verify($_POST)) { if ($_POST['trade_status'] === 'TRADE_SUCCESS') { $sp565d18 = $_POST['trade_no']; $spc686cf = (int) round($_POST['total_amount'] * 100); $sp9a4d97($_POST['out_trade_no'], $spc686cf, $sp565d18); } } else { Log::error('Pay.AliAop.goPay.verify Error: ' . json_encode($_POST)); } echo 'success'; die; } if (!empty($sp9d4382['out_trade_no'])) { $sp2e47fc = $sp9d4382['out_trade_no']; $spf066f3 = \Alipay\AlipayRequestFactory::create('alipay.trade.query', array('notify_url' => $this->url_notify, 'biz_content' => array('out_trade_no' => $sp2e47fc))); try { $spb72f32 = $this->aop($sp9d4382)->execute($spf066f3)->getData(); } catch (\Throwable $sp3f4aab) { return false; } if ($spb72f32['trade_status'] === 'TRADE_SUCCESS') { $sp565d18 = $spb72f32['trade_no']; $spc686cf = (int) round($spb72f32['total_amount'] * 100); $sp9a4d97($spb72f32['out_trade_no'], $spc686cf, $sp565d18); return true; } } else { if (!isset($_GET['out_trade_no']) || !isset($_GET['total_amount'])) { return false; } $sp4ded60 = $this->aop($sp9d4382)->verify($_GET); if (!$sp4ded60) { Log::error('Pay.AliAop.verify Error: 支付宝签名校验失败', array('$_GET' => $_GET)); return false; } $sp565d18 = $_GET['trade_no']; $spc686cf = (int) round($_GET['total_amount'] * 100); $sp9a4d97($_GET['out_trade_no'], $spc686cf, $sp565d18); return true; } return false; } }