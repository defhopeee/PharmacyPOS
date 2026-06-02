<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class HomeController extends Controller
{
    public function landing()
    {
        return view('landing');
    }

    public function sitemap(): Response
    {
        $urls = [
            ['loc' => url('/'), 'priority' => '1.0', 'freq' => 'weekly'],
            ['loc' => route('login'), 'priority' => '0.8', 'freq' => 'monthly'],
        ];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
        foreach ($urls as $u) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$u['loc']}</loc>\n";
            $xml .= "    <changefreq>{$u['freq']}</changefreq>\n";
            $xml .= "    <priority>{$u['priority']}</priority>\n";
            $xml .= "  </url>\n";
        }
        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
