<?php

/**
 * function for HTMLpurifier 
 * with <!--XH_ml2:News03-->.... and #..# and {{{...}}}
 */

function XH_sanitize_html_content($dirty_html)
{
    // Quick returns
    if ($dirty_html === null || $dirty_html === '') {
        return $dirty_html;
    }

    if (!class_exists('HTMLPurifier')) {
        if (function_exists('msg')) {
            msg('HTMLPurifier not loaded');
        }
        return $dirty_html;
    }

    // Protect Cmsimple tags AND CRITICAL HTML COMMENTS
    $placeholders = array();
    $counter = 0;
    $protected = preg_replace_callback(
        // THIS LINE also finds and protects <!--...--> comments
        '/(#.*?#|\{\{\{.*?\}\}\}|<!--.*?-->)/suU',
        function ($m) use (&$placeholders, &$counter) {
            $token = '###XH_PLACEHOLDER_' . $counter . '###';
            $placeholders[$token] = $m[0];
            $counter++;
            return $token;
        },
        $dirty_html
    );

    // Configure HTMLPurifier for CMSimple_XH
    try {
        $config = HTMLPurifier_Config::createDefault(); 
		// $config = HTMLPurifier_HTML5Config::createDefault(); xemlock
        $config->set('Core.Encoding', 'UTF-8');
        $config->set('HTML.Doctype', 'HTML 4.01 Transitional');

        $cacheDir = defined('CMSIMPLE_BASE') ? rtrim(CMSIMPLE_BASE, '/') . '/content/cache/htmlpurifier' : sys_get_temp_dir() . '/htmlpurifier_cache';
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0755, true);
        }
        if (is_dir($cacheDir) && is_writable($cacheDir)) {
            $config->set('Cache.SerializerPath', $cacheDir);
        }

        // --- Settings from last time (still important) ---
        $config->set('AutoFormat.AutoParagraph', false);
        $config->set('AutoFormat.RemoveEmpty', false);
        
        $config->set('HTML.Allowed',
            'div[style|class],p[style|class],span[style|class],strong,b,em,i,u,s,strike,sub,sup,br,'
            . 'h1[style|class],h2[style|class],h3[style|class],h4[style|class],h5[style|class],h6[style|class],'
            . 'ul[style|class],ol[style|class],li[style|class],'
            . 'a[href|title|target|rel],'
            . 'img[src|alt|title|width|height|style|class],'
            . 'blockquote[cite|class],pre,code,'
            . 'table[class|width|height|cellspacing|cellpadding],thead,tbody,tfoot,tr,th[scope|style],td[colspan|rowspan|style]'
        );
        
        $config->set('CSS.AllowedProperties', 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align,width,height,margin,margin-left,margin-right,float');
        $config->set('Attr.AllowedFrameTargets', array('_blank', '_self', '_parent', '_top'));

        $purifier = new HTMLPurifier($config);
        $clean = $purifier->purify($protected);

    } catch (Exception $e) {
        // On error, log it and return the original to prevent content loss.
        @file_put_contents($cacheDir . '/purifier_exceptions.log', "--- " . date('Y-m-d H:i:s') . " ---\n" . $e->getMessage() . "\n\n", FILE_APPEND);
        return $dirty_html;
    }

    // Restore placeholders (which now includes the comments)
    if (!empty($placeholders)) {
        $clean = strtr($clean, $placeholders);
    }

    return $clean;
}