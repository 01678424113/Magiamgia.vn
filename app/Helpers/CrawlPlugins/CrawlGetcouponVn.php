<?php

namespace App\Helpers\CrawlPlugins;

use Curl;
use Exception;
use Illuminate\Support\Facades\DB;

class CrawlGetcouponVn {

    protected $html = "";
    protected $categories = [];
    protected $contents = [];

    public function __construct($url) {
        $this->html = Curl::to($url)
                ->withOption('USERAGENT', 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)')
                ->withOption('REFERER', 'http://www.google.com/')
                ->get();
        $this->html = preg_replace("/(\\n|\\r|\\t)/", "", $this->html);
    }

    protected function handleGetCategory() {

        if (preg_match("/<nav class=\"primary-navigation.*?>(.*?)<div class=\"nav-user-action fright clearfix\">/", $this->html, $matches)) {
            if (preg_match_all("/<li id=\"menu-item-([0-9]+)\".*?><div class=\"mm-item-content\".*?>(.*?)<\/div>/", $this->html, $matches2)) {
                foreach ($matches2[1] as $key => $value) {
                    if (preg_match_all("/<a href=\"(.*?)\"><span.*?>.*?<\/span>(.*?)<\/a>/", $matches2[2][$key], $m)) {
                        foreach ($m[1] as $k => $v) {
                            if ($value == 534) {
                                if (!isset($this->categories['534'])) {
                                    $this->categories['534'] = [];
                                }
                                array_push($this->categories['534'], [
                                    'name' => $m[2][$k],
                                    'link' => $v
                                ]);

                                try {
                                    DB::table('category')->insert([
                                        'name_cat' => $m[2][$k],
                                        'url_root_cat' => $v,
                                        'status_cat' => 1,
                                        'type_cat' => 1
                                    ]);
                                } catch (Exception $exc) {
                                    echo $exc->getTraceAsString();
                                }
                            } else if ($value == 297) {
                                if (!isset($this->categories['297'])) {
                                    $this->categories['297'] = [];
                                }
                                array_push($this->categories['297'], [
                                    'name' => $m[2][$k],
                                    'link' => $v
                                ]);
                                try {
                                    DB::table('category')->insert([
                                        'name_cat' => $m[2][$k],
                                        'url_root_cat' => $v,
                                        'status_cat' => 1,
                                        'type_cat' => 0
                                    ]);
                                } catch (Exception $exc) {
                                    echo $exc->getTraceAsString();
                                }
                            }
                        }
                    }
                }
            }
        }
        dd($this->categories);
    }

    protected function handleGetContent() {
        $this->contents;
       
    }

    public function getCategory() {
        $this->handleGetCategory();
        if (!empty($this->categories)) {
            return $this->categories;
        }
        return false;
    }

    public function getContent() {
        $this->handleGetContent();
        if (!empty($this->contents)) {
            return $this->contents;
        }
        return false;
    }

    protected function handleString($str) {
        $str = html_entity_decode($str);
        $str = strip_tags($str);
        $str = preg_replace("/“/", "\"", $str);
        $str = preg_replace("/”/", "\"", $str);
        $str = preg_replace("/‘/", "'", $str);
        $str = preg_replace("/’/", "'", $str);
        while (preg_match("/\s{2}/", $str)) {
            $str = preg_replace("/\s{2}/", " ", $str);
        }
        return trim($str);
    }

    protected function handleContent($content) {
        $content = preg_replace("/&nbsp;/", " ", $content);
        $content = preg_replace("/style=(\"|\').*?(\"|\')/", "", $content);
        $content = preg_replace("/class=(\"|\').*?(\"|\')/", "", $content);
        $content = preg_replace("/type=(\"|\').*?(\"|\')/", "", $content);
        $content = preg_replace("/id=(\"|\').*?(\"|\')/", "", $content);
        $content = preg_replace("/width=(\"|\').*?(\"|\')/", "", $content);
        $content = preg_replace("/height=(\"|\').*?(\"|\')/", "", $content);
        $content = preg_replace("/height=(\"|\').*?(\"|\')/", "", $content);
        $content = preg_replace("/<\/?span.*?>/", "", $content);
        $content = preg_replace("/<h(\d{1}).*?>/", "<p>", $content);
        $content = preg_replace("/<\/h(\d{1})>/", "</p>", $content);
        $content = preg_replace("/<table.*?>(.*?)<\/table>/", "$1", $content);
        $content = preg_replace("/<tbody.*?>(.*?)<\/tbody>/", "$1", $content);
        $content = preg_replace("/<tr.*?>(.*?)<\/tr>/", "$1", $content);
        $content = preg_replace("/<td.*?>(.*?)<\/td>/", "$1", $content);
        $content = preg_replace("/<img.*?src=(\"|\')(.*?)(\"|\').*?>/", "<p><img src=\"$2\" /></p><p>", $content);
        $content = preg_replace("/<a.*?>(.*?)<\/a>/", "$1", $content);
        $content = preg_replace("/<html.*?>.*?<\/html>/", "", $content);
        $content = preg_replace("/<ins.*?>.*?<\/ins>/", "", $content);
        $content = preg_replace("/<iframe.*?>.*?<\/iframe>/", "", $content);
        $content = preg_replace("/<script.*?>.*?<\/script>/", "", $content);
        $content = preg_replace("/<style.*?>.*?<\/style>/", "", $content);
        $content = preg_replace("/<label.*?>.*?<\/label>/", "", $content);
        $content = preg_replace("/<input.*?>/", "", $content);
        $content = preg_replace("/<!--.*?-->/", "", $content);
        $content = preg_replace("/<(b|h)r.*?>/", "", $content);
        $content = preg_replace("/<div.*?>/", "<p>", $content);
        $content = preg_replace("/<\/div>/", "</p>", $content);
        $content = preg_replace("/<p.*?>/", "</p><p>", $content);
        $content = preg_replace("/<em.*?>/", "<em>", $content);
        $content = preg_replace("/<strong.*?>/", "<strong>", $content);
        $content = preg_replace("/<(em|p|i|strong|b|u)\s*?>(&nbsp;|\s)?<\/(em|p|i|strong|b|u)>/", "", $content);
        $content = preg_replace("/<figure.*?>(.*?)<\/figure>/", "<p>$1</p>", $content);
        $content = preg_replace("/<figcaption.*?>(.*?)<\/figcaption>/", "</p><p>$1</p>", $content);
        $content = preg_replace("/<em><p><em>/", "<p><em>", $content);
        $content = preg_replace("/<\/em><\/p><\/em>/", "</em></p>", $content);
        $content = preg_replace("/<ul.*?>/", "<p>", $content);
        $content = preg_replace("/<\/ul>/", "</p>", $content);
        $content = preg_replace("/<li.*?>/", "<p>", $content);
        $content = preg_replace("/<\/li>/", "</p>", $content);
        $content = '<p>' . $content;
        $content = html_entity_decode($content);
        $content = preg_replace("/\s(\.|\,|\?|\!)/", "$1 ", $content);
        while (preg_match("/\s{2}/", $content)) {
            $content = preg_replace("/\s{2}/", " ", $content);
        }
        $content = preg_replace("/>\s+</", "><", $content);
        while (preg_match("/<em><em>/", $content)) {
            $content = preg_replace("/<em><em>/", "<em>", $content);
        }
        while (preg_match("/<\/em><\/em>/", $content)) {
            $content = preg_replace("/<\/em><\/em>/", "</em>", $content);
        }
        while (preg_match("/<strong><strong>/", $content)) {
            $content = preg_replace("/<strong><strong>/", "<strong>", $content);
        }
        while (preg_match("/<\/strong><\/strong>/", $content)) {
            $content = preg_replace("/<\/strong><\/strong>/", "</strong>", $content);
        }
        while (preg_match("/<p><p>/", $content)) {
            $content = preg_replace("/<p><p>/", "<p>", $content);
        }
        while (preg_match("/<\/p><\/p>/", $content)) {
            $content = preg_replace("/<\/p><\/p>/", "</p>", $content);
        }
        $content = preg_replace("/<(em|p|i|strong|b|u)><\/(em|p|i|strong|b|u)>/", "", $content);

        $content = preg_replace("/“/", "\"", $content);
        $content = preg_replace("/”/", "\"", $content);
        $content = preg_replace("/‘/", "'", $content);
        $content = preg_replace("/’/", "'", $content);

        return trim($content);
    }

}
