<?php
namespace App\Mail; use Illuminate\Bus\Queueable; use Illuminate\Mail\Mailable; use Illuminate\Queue\SerializesModels; use Illuminate\Contracts\Queue\ShouldQueue; class ProductCountWarn extends Mailable { use Queueable, SerializesModels; public $product = null; public $product_count = null; public function __construct($sp2cf004, $spbbe5dc) { $this->product = $sp2cf004; $this->product_count = $spbbe5dc; } public function build() { return $this->subject('您的商品库存不足-' . config('app.name'))->view('emails.product_count_warn'); } }