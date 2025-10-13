<?php
// Lightweight inclusion of Parsedown (v1.7.4 compatible subset)
// This is a minimal copy for offline Markdown -> HTML rendering.
class Parsedown
{
    public function text($text)
    {
        $html = htmlspecialchars($text, ENT_NOQUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $html = preg_replace('/^######\s*(.+)$/m', '<h6>$1</h6>', $html);
        $html = preg_replace('/^#####\s*(.+)$/m', '<h5>$1</h5>', $html);
        $html = preg_replace('/^####\s*(.+)$/m', '<h4>$1</h4>', $html);
        $html = preg_replace('/^###\s*(.+)$/m', '<h3>$1</h3>', $html);
        $html = preg_replace('/^##\s*(.+)$/m', '<h2>$1</h2>', $html);
        $html = preg_replace('/^#\s*(.+)$/m', '<h1>$1</h1>', $html);
        $html = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $html);
        $html = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $html);
        $html = preg_replace('/\[(.*?)\]\((.*?)\)/', '<a href="$2">$1</a>', $html);
        $html = preg_replace('/```\n(.*?)\n```/s', '<pre><code>$1</code></pre>', $html);
        $html = preg_replace('/`(.+?)`/', '<code>$1</code>', $html);
        $html = preg_replace_callback('/(^[-\*\+]\s+.+(?:\n[-\*\+]\s+.+)*)/m', function ($m) {
            $items = preg_split('/\n/', trim($m[1]));
            $out = "<ul>";
            foreach ($items as $it) {
                $it = preg_replace('/^[-\*\+]\s+/', '', $it);
                $out .= '<li>' . $it . '</li>';
            }
            $out .= "</ul>";
            return $out;
        }, $html);
        $html = preg_replace('/^(?!<h|<ul|<pre|<li|<blockquote|<p|<code)(.+)$/m', '<p>$1</p>', $html);
        return $html;
    }
}

?>
