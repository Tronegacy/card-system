<?php
namespace App\Http\Controllers\Merchant; use App\Library\Response; use App\System; use Illuminate\Http\Request; use App\Http\Controllers\Controller; use Illuminate\Support\Facades\Auth; use Illuminate\Support\Facades\DB; use Illuminate\Support\Facades\Storage; class Card extends Controller { function get(Request $spf066f3, $spaf67ea = false, $spb6ce1a = false, $speb9a35 = false) { $sp5044a7 = $this->authQuery($spf066f3, \App\Card::class)->with(array('product' => function ($sp5044a7) { $sp5044a7->select(array('id', 'name')); })); $spca736c = $spf066f3->input('search', false); $sp56dec1 = $spf066f3->input('val', false); if ($spca736c && $sp56dec1) { if ($spca736c == 'id') { $sp5044a7->where('id', $sp56dec1); } else { $sp5044a7->where($spca736c, 'like', '%' . $sp56dec1 . '%'); } } $spe478dd = (int) $spf066f3->input('category_id'); $spbb5d29 = $spf066f3->input('product_id', -1); if ($spe478dd > 0) { if ($spbb5d29 > 0) { $sp5044a7->where('product_id', $spbb5d29); } else { $sp5044a7->whereHas('product', function ($sp5044a7) use($spe478dd) { $sp5044a7->where('category_id', $spe478dd); }); } } $sp27b58d = $spf066f3->input('status'); if (strlen($sp27b58d)) { $sp5044a7->whereIn('status', explode(',', $sp27b58d)); } $spa86c4b = (int) $spf066f3->input('onlyCanSell'); if ($spa86c4b) { $sp5044a7->whereRaw('`count_all`>`count_sold`'); } $sp4f56c1 = $spf066f3->input('type'); if (strlen($sp4f56c1)) { $sp5044a7->whereIn('type', explode(',', $sp4f56c1)); } $sp6cb6b8 = $spf066f3->input('trashed') === 'true'; if ($sp6cb6b8) { $sp5044a7->onlyTrashed(); } if ($spb6ce1a === true) { if ($sp6cb6b8) { $sp5044a7->forceDelete(); } else { \App\Card::_trash($sp5044a7); } return Response::success(); } else { if ($sp6cb6b8 && $speb9a35 === true) { \App\Card::_restore($sp5044a7); return Response::success(); } else { $sp5044a7->orderByRaw('`product_id`,`type`,`status`,`id`'); if ($spaf67ea === true) { $sp0de9bb = ''; $sp5044a7->chunk(100, function ($sp54decd) use(&$sp0de9bb) { foreach ($sp54decd as $spc3a8a6) { $sp0de9bb .= $spc3a8a6->card . '
'; } }); $spc11887 = 'export_cards_' . $this->getUserIdOrFail($spf066f3) . '_' . date('YmdHis') . '.txt'; $spc69671 = array('Content-type' => 'text/plain', 'Content-Disposition' => sprintf('attachment; filename="%s"', $spc11887), 'Content-Length' => strlen($sp0de9bb)); return response()->make($sp0de9bb, 200, $spc69671); } $sp72b123 = $spf066f3->input('current_page', 1); $spcfcad4 = $spf066f3->input('per_page', 20); $sp293456 = $sp5044a7->paginate($spcfcad4, array('*'), 'page', $sp72b123); return Response::success($sp293456); } } } function export(Request $spf066f3) { return self::get($spf066f3, true); } function trash(Request $spf066f3) { $this->validate($spf066f3, array('ids' => 'required|string')); $sp1f71d9 = $spf066f3->post('ids'); $sp5044a7 = $this->authQuery($spf066f3, \App\Card::class)->whereIn('id', explode(',', $sp1f71d9)); \App\Card::_trash($sp5044a7); return Response::success(); } function restoreTrashed(Request $spf066f3) { $this->validate($spf066f3, array('ids' => 'required|string')); $sp1f71d9 = $spf066f3->post('ids'); $sp5044a7 = $this->authQuery($spf066f3, \App\Card::class)->whereIn('id', explode(',', $sp1f71d9)); \App\Card::_restore($sp5044a7); return Response::success(); } function deleteTrashed(Request $spf066f3) { $this->validate($spf066f3, array('ids' => 'required|string')); $sp1f71d9 = $spf066f3->post('ids'); $this->authQuery($spf066f3, \App\Card::class)->whereIn('id', explode(',', $sp1f71d9))->forceDelete(); return Response::success(); } function deleteAll(Request $spf066f3) { return $this->get($spf066f3, false, true); } function restoreAll(Request $spf066f3) { return $this->get($spf066f3, false, false, true); } function add(Request $spf066f3) { $spbb5d29 = (int) $spf066f3->post('product_id'); $sp54decd = $spf066f3->post('card'); $sp4f56c1 = (int) $spf066f3->post('type', \App\Card::TYPE_ONETIME); $spc564ba = $spf066f3->post('is_check') === 'true'; if (str_contains($sp54decd, '<') || str_contains($sp54decd, '>')) { return Response::fail('卡密不能包含 < 或 > 符号'); } $sp15a746 = $this->getUserIdOrFail($spf066f3); $sp273177 = $this->authQuery($spf066f3, \App\Product::class)->where('id', $spbb5d29); $sp273177->firstOrFail(array('id')); if ($sp4f56c1 === \App\Card::TYPE_REPEAT) { if ($spc564ba) { if (\App\Card::where('product_id', $spbb5d29)->where('card', $sp54decd)->exists()) { return Response::fail('该卡密已经存在，添加失败'); } } $spc3a8a6 = new \App\Card(array('user_id' => $sp15a746, 'product_id' => $spbb5d29, 'card' => $sp54decd, 'type' => \App\Card::TYPE_REPEAT, 'count_sold' => 0, 'count_all' => (int) $spf066f3->post('count_all', 1))); if ($spc3a8a6->count_all < 1 || $spc3a8a6->count_all > 10000000) { return Response::forbidden('可售总次数不能超过10000000'); } return DB::transaction(function () use($sp273177, $spc3a8a6) { $spc3a8a6->saveOrFail(); $sp2cf004 = $sp273177->lockForUpdate()->firstOrFail(); $sp2cf004->buy_max = 1; $sp2cf004->count_all += $spc3a8a6->count_all; $sp2cf004->saveOrFail(); return Response::success(); }); } else { $spa5ca46 = explode('
', $sp54decd); $sp5533b7 = count($spa5ca46); $sp62d1fc = 500; if ($sp5533b7 > $sp62d1fc) { return Response::fail('每次添加不能超过 ' . $sp62d1fc . ' 张'); } $sp74f136 = array(); if ($spc564ba) { $sp697ce2 = \App\Card::where('user_id', $sp15a746)->where('product_id', $spbb5d29)->get(array('card'))->all(); foreach ($sp697ce2 as $sp00625a) { $sp74f136[] = $sp00625a['card']; } } $spd9d278 = array(); $spe07134 = 0; for ($sp6b283c = 0; $sp6b283c < $sp5533b7; $sp6b283c++) { $spc3a8a6 = trim($spa5ca46[$sp6b283c]); if (strlen($spc3a8a6) < 1) { continue; } if (strlen($spc3a8a6) > 255) { return Response::fail('第 ' . $sp6b283c . ' 张卡密 ' . $spc3a8a6 . ' 长度错误<br>卡密最大长度为255'); } if ($spc564ba) { if (in_array($spc3a8a6, $sp74f136)) { continue; } $sp74f136[] = $spc3a8a6; } $spd9d278[] = array('user_id' => $sp15a746, 'product_id' => $spbb5d29, 'card' => $spc3a8a6, 'type' => \App\Card::TYPE_ONETIME); $spe07134++; } if ($spe07134 === 0) { return Response::success(); } return DB::transaction(function () use($sp273177, $spd9d278, $spe07134) { \App\Card::insert($spd9d278); $sp2cf004 = $sp273177->lockForUpdate()->firstOrFail(); $sp2cf004->count_all += $spe07134; $sp2cf004->saveOrFail(); return Response::success(); }); } } function edit(Request $spf066f3) { $sp3c46ab = (int) $spf066f3->post('id'); $spc3a8a6 = $this->authQuery($spf066f3, \App\Card::class)->findOrFail($sp3c46ab); if ($spc3a8a6) { $spdfa149 = $spf066f3->post('card'); $sp4f56c1 = (int) $spf066f3->post('type', \App\Card::TYPE_ONETIME); $spb3c1e9 = (int) $spf066f3->post('count_all', 1); return DB::transaction(function () use($spc3a8a6, $spdfa149, $sp4f56c1, $spb3c1e9) { $spc3a8a6 = \App\Card::where('id', $spc3a8a6->id)->lockForUpdate()->firstOrFail(); $spc3a8a6->card = $spdfa149; $spc3a8a6->type = $sp4f56c1; if ($spc3a8a6->type === \App\Card::TYPE_REPEAT) { if ($spb3c1e9 < $spc3a8a6->count_sold) { return Response::forbidden('可售总次数不能低于当前已售次数'); } if ($spb3c1e9 < 1 || $spb3c1e9 > 10000000) { return Response::forbidden('可售总次数不能超过10000000'); } $spc3a8a6->count_all = $spb3c1e9; } else { $spc3a8a6->count_all = 1; } $spc3a8a6->saveOrFail(); $sp2cf004 = $spc3a8a6->product()->lockForUpdate()->firstOrFail(); if ($spc3a8a6->type === \App\Card::TYPE_REPEAT) { $sp2cf004->buy_max = 1; } $sp2cf004->count_all -= $spc3a8a6->count_all; $sp2cf004->count_all += $spb3c1e9; $sp2cf004->saveOrFail(); return Response::success(); }); } return Response::success(); } }