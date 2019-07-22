<?php
require_once 'WxPay.Exception.php'; require_once 'WxPay.Config.php'; require_once 'WxPay.Data.php'; class WxPayApi { public static function unifiedOrder($spb74b43, $sp144767 = 6) { $spd2457c = 'https://api.mch.weixin.qq.com/pay/unifiedorder'; if (!$spb74b43->IsOut_trade_noSet()) { throw new WxPayException('缺少统一支付接口必填参数out_trade_no！'); } else { if (!$spb74b43->IsBodySet()) { throw new WxPayException('缺少统一支付接口必填参数body！'); } else { if (!$spb74b43->IsTotal_feeSet()) { throw new WxPayException('缺少统一支付接口必填参数total_fee！'); } else { if (!$spb74b43->IsTrade_typeSet()) { throw new WxPayException('缺少统一支付接口必填参数trade_type！'); } } } } if ($spb74b43->GetTrade_type() == 'JSAPI' && !$spb74b43->IsOpenidSet()) { throw new WxPayException('统一支付接口中，缺少必填参数openid！trade_type为JSAPI时，openid为必填参数！'); } if ($spb74b43->GetTrade_type() == 'NATIVE' && !$spb74b43->IsProduct_idSet()) { throw new WxPayException('统一支付接口中，缺少必填参数product_id！trade_type为JSAPI时，product_id为必填参数！'); } $spb74b43->SetAppid(WxPayConfig::APPID); $spb74b43->SetMch_id(WxPayConfig::MCHID); if (@WxPayConfig::SUBAPPID) { $spb74b43->SetSub_appid(WxPayConfig::SUBAPPID); } if (@WxPayConfig::SUBMCHID) { $spb74b43->SetSub_mch_id(WxPayConfig::SUBMCHID); } $spb74b43->SetNonce_str(self::getNonceStr()); $spb74b43->SetSign(); $sp87287d = $spb74b43->ToXml(); $sp9e0386 = self::getMillisecond(); $spdc5091 = self::postXmlCurl($sp87287d, $spd2457c, false, $sp144767); $spb72f32 = WxPayResults::Init($spdc5091); self::reportCostTime($spd2457c, $sp9e0386, $spb72f32); return $spb72f32; } public static function orderQuery($spb74b43, $sp144767 = 6) { $spd2457c = 'https://api.mch.weixin.qq.com/pay/orderquery'; if (!$spb74b43->IsOut_trade_noSet() && !$spb74b43->IsTransaction_idSet()) { throw new WxPayException('订单查询接口中，out_trade_no、transaction_id至少填一个！'); } $spb74b43->SetAppid(WxPayConfig::APPID); $spb74b43->SetMch_id(WxPayConfig::MCHID); if (@WxPayConfig::SUBMCHID) { $spb74b43->SetSub_mch_id(WxPayConfig::SUBMCHID); } $spb74b43->SetNonce_str(self::getNonceStr()); $spb74b43->SetSign(); $sp87287d = $spb74b43->ToXml(); $sp9e0386 = self::getMillisecond(); $spdc5091 = self::postXmlCurl($sp87287d, $spd2457c, false, $sp144767); $spb72f32 = WxPayResults::Init($spdc5091); self::reportCostTime($spd2457c, $sp9e0386, $spb72f32); return $spb72f32; } public static function closeOrder($spb74b43, $sp144767 = 6) { $spd2457c = 'https://api.mch.weixin.qq.com/pay/closeorder'; if (!$spb74b43->IsOut_trade_noSet()) { throw new WxPayException('订单查询接口中，out_trade_no必填！'); } $spb74b43->SetAppid(WxPayConfig::APPID); $spb74b43->SetMch_id(WxPayConfig::MCHID); $spb74b43->SetNonce_str(self::getNonceStr()); $spb74b43->SetSign(); $sp87287d = $spb74b43->ToXml(); $sp9e0386 = self::getMillisecond(); $spdc5091 = self::postXmlCurl($sp87287d, $spd2457c, false, $sp144767); $spb72f32 = WxPayResults::Init($spdc5091); self::reportCostTime($spd2457c, $sp9e0386, $spb72f32); return $spb72f32; } public static function refund($spb74b43, $sp144767 = 6) { $spd2457c = 'https://api.mch.weixin.qq.com/secapi/pay/refund'; if (!$spb74b43->IsOut_trade_noSet() && !$spb74b43->IsTransaction_idSet()) { throw new WxPayException('退款申请接口中，out_trade_no、transaction_id至少填一个！'); } else { if (!$spb74b43->IsOut_refund_noSet()) { throw new WxPayException('退款申请接口中，缺少必填参数out_refund_no！'); } else { if (!$spb74b43->IsTotal_feeSet()) { throw new WxPayException('退款申请接口中，缺少必填参数total_fee！'); } else { if (!$spb74b43->IsRefund_feeSet()) { throw new WxPayException('退款申请接口中，缺少必填参数refund_fee！'); } else { if (!$spb74b43->IsOp_user_idSet()) { throw new WxPayException('退款申请接口中，缺少必填参数op_user_id！'); } } } } } $spb74b43->SetAppid(WxPayConfig::APPID); $spb74b43->SetMch_id(WxPayConfig::MCHID); $spb74b43->SetNonce_str(self::getNonceStr()); $spb74b43->SetSign(); $sp87287d = $spb74b43->ToXml(); $sp9e0386 = self::getMillisecond(); $spdc5091 = self::postXmlCurl($sp87287d, $spd2457c, true, $sp144767); $spb72f32 = WxPayResults::Init($spdc5091); self::reportCostTime($spd2457c, $sp9e0386, $spb72f32); return $spb72f32; } public static function refundQuery($spb74b43, $sp144767 = 6) { $spd2457c = 'https://api.mch.weixin.qq.com/pay/refundquery'; if (!$spb74b43->IsOut_refund_noSet() && !$spb74b43->IsOut_trade_noSet() && !$spb74b43->IsTransaction_idSet() && !$spb74b43->IsRefund_idSet()) { throw new WxPayException('退款查询接口中，out_refund_no、out_trade_no、transaction_id、refund_id四个参数必填一个！'); } $spb74b43->SetAppid(WxPayConfig::APPID); $spb74b43->SetMch_id(WxPayConfig::MCHID); $spb74b43->SetNonce_str(self::getNonceStr()); $spb74b43->SetSign(); $sp87287d = $spb74b43->ToXml(); $sp9e0386 = self::getMillisecond(); $spdc5091 = self::postXmlCurl($sp87287d, $spd2457c, false, $sp144767); $spb72f32 = WxPayResults::Init($spdc5091); self::reportCostTime($spd2457c, $sp9e0386, $spb72f32); return $spb72f32; } public static function downloadBill($spb74b43, $sp144767 = 6) { $spd2457c = 'https://api.mch.weixin.qq.com/pay/downloadbill'; if (!$spb74b43->IsBill_dateSet()) { throw new WxPayException('对账单接口中，缺少必填参数bill_date！'); } $spb74b43->SetAppid(WxPayConfig::APPID); $spb74b43->SetMch_id(WxPayConfig::MCHID); $spb74b43->SetNonce_str(self::getNonceStr()); $spb74b43->SetSign(); $sp87287d = $spb74b43->ToXml(); $spdc5091 = self::postXmlCurl($sp87287d, $spd2457c, false, $sp144767); if (substr($spdc5091, 0, 5) == '<xml>') { return ''; } return $spdc5091; } public static function micropay($spb74b43, $sp144767 = 10) { $spd2457c = 'https://api.mch.weixin.qq.com/pay/micropay'; if (!$spb74b43->IsBodySet()) { throw new WxPayException('提交被扫支付API接口中，缺少必填参数body！'); } else { if (!$spb74b43->IsOut_trade_noSet()) { throw new WxPayException('提交被扫支付API接口中，缺少必填参数out_trade_no！'); } else { if (!$spb74b43->IsTotal_feeSet()) { throw new WxPayException('提交被扫支付API接口中，缺少必填参数total_fee！'); } else { if (!$spb74b43->IsAuth_codeSet()) { throw new WxPayException('提交被扫支付API接口中，缺少必填参数auth_code！'); } } } } $spb74b43->SetSpbill_create_ip($_SERVER['REMOTE_ADDR']); $spb74b43->SetAppid(WxPayConfig::APPID); $spb74b43->SetMch_id(WxPayConfig::MCHID); $spb74b43->SetNonce_str(self::getNonceStr()); $spb74b43->SetSign(); $sp87287d = $spb74b43->ToXml(); $sp9e0386 = self::getMillisecond(); $spdc5091 = self::postXmlCurl($sp87287d, $spd2457c, false, $sp144767); $spb72f32 = WxPayResults::Init($spdc5091); self::reportCostTime($spd2457c, $sp9e0386, $spb72f32); return $spb72f32; } public static function reverse($spb74b43, $sp144767 = 6) { $spd2457c = 'https://api.mch.weixin.qq.com/secapi/pay/reverse'; if (!$spb74b43->IsOut_trade_noSet() && !$spb74b43->IsTransaction_idSet()) { throw new WxPayException('撤销订单API接口中，参数out_trade_no和transaction_id必须填写一个！'); } $spb74b43->SetAppid(WxPayConfig::APPID); $spb74b43->SetMch_id(WxPayConfig::MCHID); $spb74b43->SetNonce_str(self::getNonceStr()); $spb74b43->SetSign(); $sp87287d = $spb74b43->ToXml(); $sp9e0386 = self::getMillisecond(); $spdc5091 = self::postXmlCurl($sp87287d, $spd2457c, true, $sp144767); $spb72f32 = WxPayResults::Init($spdc5091); self::reportCostTime($spd2457c, $sp9e0386, $spb72f32); return $spb72f32; } public static function report($spb74b43, $sp144767 = 1) { $spd2457c = 'https://api.mch.weixin.qq.com/payitil/report'; if (!$spb74b43->IsInterface_urlSet()) { throw new WxPayException('接口URL，缺少必填参数interface_url！'); } if (!$spb74b43->IsReturn_codeSet()) { throw new WxPayException('返回状态码，缺少必填参数return_code！'); } if (!$spb74b43->IsResult_codeSet()) { throw new WxPayException('业务结果，缺少必填参数result_code！'); } if (!$spb74b43->IsUser_ipSet()) { throw new WxPayException('访问接口IP，缺少必填参数user_ip！'); } if (!$spb74b43->IsExecute_time_Set()) { throw new WxPayException('接口耗时，缺少必填参数execute_time_！'); } $spb74b43->SetAppid(WxPayConfig::APPID); $spb74b43->SetMch_id(WxPayConfig::MCHID); $spb74b43->SetUser_ip($_SERVER['REMOTE_ADDR']); $spb74b43->SetTime(date('YmdHis')); $spb74b43->SetNonce_str(self::getNonceStr()); $spb74b43->SetSign(); $sp87287d = $spb74b43->ToXml(); $sp9e0386 = self::getMillisecond(); $spdc5091 = self::postXmlCurl($sp87287d, $spd2457c, false, $sp144767); return $spdc5091; } public static function bizpayurl($spb74b43, $sp144767 = 6) { if (!$spb74b43->IsProduct_idSet()) { throw new WxPayException('生成二维码，缺少必填参数product_id！'); } $spb74b43->SetAppid(WxPayConfig::APPID); $spb74b43->SetMch_id(WxPayConfig::MCHID); $spb74b43->SetTime_stamp(time()); $spb74b43->SetNonce_str(self::getNonceStr()); $spb74b43->SetSign(); return $spb74b43->GetValues(); } public static function shorturl($spb74b43, $sp144767 = 6) { $spd2457c = 'https://api.mch.weixin.qq.com/tools/shorturl'; if (!$spb74b43->IsLong_urlSet()) { throw new WxPayException('需要转换的URL，签名用原串，传输需URL encode！'); } $spb74b43->SetAppid(WxPayConfig::APPID); $spb74b43->SetMch_id(WxPayConfig::MCHID); $spb74b43->SetNonce_str(self::getNonceStr()); $spb74b43->SetSign(); $sp87287d = $spb74b43->ToXml(); $sp9e0386 = self::getMillisecond(); $spdc5091 = self::postXmlCurl($sp87287d, $spd2457c, false, $sp144767); $spb72f32 = WxPayResults::Init($spdc5091); self::reportCostTime($spd2457c, $sp9e0386, $spb72f32); return $spb72f32; } public static function notify($sp84097e, &$sp417133) { $sp87287d = file_get_contents('php://input'); try { $spb72f32 = WxPayResults::Init($sp87287d); } catch (WxPayException $sp3f4aab) { $sp417133 = $sp3f4aab->errorMessage(); return false; } return call_user_func($sp84097e, $spb72f32); } public static function getNonceStr($sp8dab90 = 32) { $sp1650b8 = 'abcdefghijklmnopqrstuvwxyz0123456789'; $sp14800d = ''; for ($sp6b283c = 0; $sp6b283c < $sp8dab90; $sp6b283c++) { $sp14800d .= substr($sp1650b8, mt_rand(0, strlen($sp1650b8) - 1), 1); } return $sp14800d; } public static function replyNotify($sp87287d) { echo $sp87287d; } private static function reportCostTime($spd2457c, $sp9e0386, $sp6fd648) { if (WxPayConfig::REPORT_LEVENL == 0) { return; } if (WxPayConfig::REPORT_LEVENL == 1 && array_key_exists('return_code', $sp6fd648) && $sp6fd648['return_code'] == 'SUCCESS' && array_key_exists('result_code', $sp6fd648) && $sp6fd648['result_code'] == 'SUCCESS') { return; } $spef4af2 = self::getMillisecond(); $spa0189f = new WxPayReport(); $spa0189f->SetInterface_url($spd2457c); $spa0189f->SetExecute_time_($spef4af2 - $sp9e0386); if (array_key_exists('return_code', $sp6fd648)) { $spa0189f->SetReturn_code($sp6fd648['return_code']); } if (array_key_exists('return_msg', $sp6fd648)) { $spa0189f->SetReturn_msg($sp6fd648['return_msg']); } if (array_key_exists('result_code', $sp6fd648)) { $spa0189f->SetResult_code($sp6fd648['result_code']); } if (array_key_exists('err_code', $sp6fd648)) { $spa0189f->SetErr_code($sp6fd648['err_code']); } if (array_key_exists('err_code_des', $sp6fd648)) { $spa0189f->SetErr_code_des($sp6fd648['err_code_des']); } if (array_key_exists('out_trade_no', $sp6fd648)) { $spa0189f->SetOut_trade_no($sp6fd648['out_trade_no']); } if (array_key_exists('device_info', $sp6fd648)) { $spa0189f->SetDevice_info($sp6fd648['device_info']); } try { self::report($spa0189f); } catch (WxPayException $sp3f4aab) { } } private static function postXmlCurl($sp87287d, $spd2457c, $spede4ee = false, $sp2bbc73 = 30) { $sp9f83d6 = curl_init(); curl_setopt($sp9f83d6, CURLOPT_TIMEOUT, $sp2bbc73); if (WxPayConfig::CURL_PROXY_HOST != '0.0.0.0' && WxPayConfig::CURL_PROXY_PORT != 0) { curl_setopt($sp9f83d6, CURLOPT_PROXY, WxPayConfig::CURL_PROXY_HOST); curl_setopt($sp9f83d6, CURLOPT_PROXYPORT, WxPayConfig::CURL_PROXY_PORT); } curl_setopt($sp9f83d6, CURLOPT_URL, $spd2457c); curl_setopt($sp9f83d6, CURLOPT_SSL_VERIFYPEER, TRUE); curl_setopt($sp9f83d6, CURLOPT_SSL_VERIFYHOST, 2); curl_setopt($sp9f83d6, CURLOPT_HEADER, FALSE); curl_setopt($sp9f83d6, CURLOPT_RETURNTRANSFER, TRUE); if ($spede4ee == true) { curl_setopt($sp9f83d6, CURLOPT_SSLCERTTYPE, 'PEM'); curl_setopt($sp9f83d6, CURLOPT_SSLCERT, WxPayConfig::SSLCERT_PATH); curl_setopt($sp9f83d6, CURLOPT_SSLKEYTYPE, 'PEM'); curl_setopt($sp9f83d6, CURLOPT_SSLKEY, WxPayConfig::SSLKEY_PATH); } else { curl_setopt($sp9f83d6, CURLOPT_SSL_VERIFYPEER, false); } curl_setopt($sp9f83d6, CURLOPT_POST, TRUE); curl_setopt($sp9f83d6, CURLOPT_POSTFIELDS, $sp87287d); $sp6fd648 = curl_exec($sp9f83d6); if ($sp6fd648) { curl_close($sp9f83d6); return $sp6fd648; } else { $sp9ad762 = curl_errno($sp9f83d6); \WxLog::error('WxPat.Api.postXmlCurl Error: ' . curl_error($sp9f83d6)); curl_close($sp9f83d6); throw new WxPayException("curl出错，错误码: {$sp9ad762}"); } } private static function getMillisecond() { $spe66dbc = explode(' ', microtime()); $spe66dbc = $spe66dbc[1] . $spe66dbc[0] * 1000; $sp24794b = explode('.', $spe66dbc); $spe66dbc = $sp24794b[0]; return $spe66dbc; } }