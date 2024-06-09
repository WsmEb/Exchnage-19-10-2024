<?php
use App\Models\Devise;

function convertedSymbolBase($response) {
    $devise = Devise::where("symbol", $response)->first();
    return $devise ? $devise->base : null;
}

// function JsConvertedSymbol($response) {
//     $devise = Devise::where("symbol", $response)->first();
//     return json_encode([$devise->base]);
// }
