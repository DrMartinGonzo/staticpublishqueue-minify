<?php

namespace DrMartinGonzo\StaticPublishQueueMinify\Publisher;

use SilverStripe\StaticPublishQueue\Publisher\FilesystemPublisher;
use Wa72\HtmlPrettymin\PrettyMin;

class FilesystemPublisherMinify extends FilesystemPublisher
{

    /**
     * @param HTTPResponse $response
     * @param string       $url
     * @return bool
     */
    protected function publishPage($response, $url)
    {
        $response->setBody($this->getMinifiedHTML($response->getBody()));
        parent::publishPage($response, $url);
    }

    protected function getMinifiedHTML($originalHTML)
    {
        libxml_use_internal_errors(true); // disable error reporting on html5 tags

        $prettyMin_options = array(
            'minify_js' => false,
            'minify_css' => true,
            'remove_comments' => false,
            'remove_comments_exeptions' => ['/^\[if /'],
            'keep_whitespace_around' => [
                // keep whitespace around inline elements
                'b', 'big', 'i', 'small', 'tt',
                'abbr', 'acronym', 'cite', 'code', 'dfn', 'em', 'kbd', 'strong', 'samp', 'var',
                'a', 'bdo', 'br', 'img', 'map', 'object', 'q', 'span', 'sub', 'sup',
                'button', 'input', 'label', 'select', 'textarea'
            ],
            'keep_whitespace_in' => ['script', 'style', 'pre'],
            'remove_empty_attributes' => ['style', 'class'],
            'indent_characters' => "\t"
        );
        $prettyMin = new PrettyMin($prettyMin_options);
        $minHtml = $prettyMin->load($originalHTML)
                             ->minify()
                             ->saveHtml();

        libxml_clear_errors();
        return $minHtml;
    }
}
