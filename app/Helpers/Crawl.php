<?php

namespace App\Helpers;

use Curl;
use DB;
use App\Models\SourceArticle;
use App\Helpers\CrawlPlugins\RawHtml;
use App\Helpers\CrawlPlugins\CrawlGetcouponVn;

class Crawl {

    public static function getDataCategory($url) {
        $crawl = false;
        $parse_url = parse_url($url);
        $domain = $parse_url['host'];
        $crawl = new CrawlGetcouponVn($url);
//        switch ($domain) {
//            case 'getcoupon.vn':
//                $crawl = new CrawlGetcouponVn($url);
//                break;
//        }
        if ($crawl) {
            return $crawl->getCategory();
        }

        return false;
    }

    public static function getDataContent($url) {
        $crawl = false;
        $parse_url = parse_url($url);
        $domain = $parse_url['host'];
        $crawl = new CrawlGetcouponVn($url);
        dd($crawl);
        if ($crawl) {
            return $crawl->getContent();
        }
        return false;
    }

    public static function handleHtml($html) {
        $raw = new RawHtml($html);
        return $raw->get();
    }

}
