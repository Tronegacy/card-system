<?php
function create_link_string($sp7a655e) { $spee4521 = array(); foreach ($sp7a655e as $sp17f3a7 => $sp75c248) { if ($sp17f3a7 == 'sign' || $sp17f3a7 == 'sign_type' || strval($sp75c248) === '') { continue; } $spee4521[$sp17f3a7] = $sp75c248; } ksort($spee4521); reset($spee4521); $sp57c72a = ''; foreach ($spee4521 as $sp17f3a7 => $sp75c248) { $sp57c72a .= $sp17f3a7 . '=' . strval($sp75c248) . '&'; } $sp57c72a = trim($sp57c72a, '&'); if (get_magic_quotes_gpc()) { $sp57c72a = stripslashes($sp57c72a); } return $sp57c72a; } function curl_http($spd2457c, $spc0e525 = '', $spd0c59e = 'GET', $sp06401d = array()) { $spe3a26a = array(CURLOPT_TIMEOUT => 5, CURLOPT_RETURNTRANSFER => 1, CURLOPT_HEADER => 0, CURLOPT_FOLLOWLOCATION => 1, CURLOPT_SSL_VERIFYPEER => 0, CURLOPT_SSL_VERIFYHOST => 0, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_0, CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4); if ($sp06401d) { $spe3a26a[CURLOPT_HTTPHEADER] = $sp06401d; } if (is_array($spc0e525)) { $spc0e525 = http_build_query($spc0e525); } switch (strtoupper($spd0c59e)) { case 'GET': $spe3a26a[CURLOPT_URL] = $spc0e525 ? $spd2457c . '?' . $spc0e525 : $spd2457c; $spe3a26a[CURLOPT_CUSTOMREQUEST] = 'GET'; break; case 'POST': $spe3a26a[CURLOPT_URL] = $spd2457c; $spe3a26a[CURLOPT_POST] = 1; $spe3a26a[CURLOPT_POSTFIELDS] = $spc0e525; break; default: break; } $sp9f83d6 = curl_init(); curl_setopt_array($sp9f83d6, $spe3a26a); $spb72f32 = curl_exec($sp9f83d6); if ($spb72f32) { curl_close($sp9f83d6); return $spb72f32; } else { $sp9ad762 = curl_errno($sp9f83d6); curl_close($sp9f83d6); die('请求发起失败,错误码:' . $sp9ad762); } } function create_rsa_sign($sp29fcf1) { require './config.php'; $sp6bf652 or die('私钥信息尚未配置,请检查'); $spdb68db = '-----BEGIN RSA PRIVATE KEY-----
' . wordwrap($sp6bf652, 64, '
', true) . '
-----END RSA PRIVATE KEY-----'; openssl_sign($sp29fcf1, $sp964415, $spdb68db, OPENSSL_ALGO_SHA256); $sp964415 = base64_encode($sp964415); return $sp964415; } function check_sign($spc0e525, $spf1241f) { if (empty($spc0e525)) { return false; } if (!is_array($spc0e525)) { return false; } if (!isset($spc0e525['sign'])) { return false; } $sp29fcf1 = create_link_string($spc0e525); switch (strtoupper($spc0e525['sign_type'])) { case 'RSA2': $sp96fcd1 or die('尚未设置网关RSA公钥'); $sp964415 = str_replace(' ', '+', $spc0e525['sign']); $spbb6c7c = '-----BEGIN PUBLIC KEY-----
' . wordwrap($sp96fcd1, 64, '
', true) . '
-----END PUBLIC KEY-----'; $spb72f32 = (bool) openssl_verify($sp29fcf1, base64_decode($sp964415), $spbb6c7c, OPENSSL_ALGO_SHA256); return $spb72f32; break; case 'SHA256': $sp964415 = hash('sha256', $sp29fcf1 . '&key=' . $spc27515); return boolval($sp964415 == $spc0e525['sign']); break; default: case 'MD5': $sp964415 = md5($sp29fcf1 . '&key=' . $spc27515); return boolval($sp964415 == $spc0e525['sign']); break; } } function is_weixin() { $sp0249ce = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : ''; if (strpos($sp0249ce, 'MicroMessenger') !== false) { return true; } else { return false; } } function is_mobile() { if (isset($_SERVER['HTTP_VIA']) && stristr($_SERVER['HTTP_VIA'], 'wap')) { return true; } elseif (isset($_SERVER['HTTP_ACCEPT']) && strpos(strtoupper($_SERVER['HTTP_ACCEPT']), 'VND.WAP.WML')) { return true; } elseif (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) { return true; } elseif (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/(blackberry|configuration\\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $_SERVER['HTTP_USER_AGENT'])) { return true; } else { return false; } }