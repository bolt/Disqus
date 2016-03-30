<?php
// Disqus comment thread Extension for Bolt

namespace Bolt\Extension\Bolt\Disqus;

use Bolt\Asset\Snippet\Snippet;
use Bolt\Asset\Target;
use Bolt\Extension\SimpleExtension;

class DisqusExtension extends SimpleExtension
{
    protected function registerTwigFunctions()
    {
        return [
            'disqus' => 'disqus',
            'disquslink' => 'disquslink',
        ];
    }

    public function disqus($title="")
    {
        $config = $this->getConfig();
        $app = $this->getContainer();

        $html = <<< EOM
        <div id="disqus_thread"></div>
        <script type="text/javascript">
            var disqus_shortname = '%shortname%';
            %title%var disqus_url = '%url%';

            (function () {
                var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
                dsq.src = 'https://' + disqus_shortname + '.disqus.com/embed.js';
                (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
            })();
        </script>
        <noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
        <a href="http://disqus.com" class="dsq-brlink">comments powered by <span class="logo-disqus">Disqus</span></a>

EOM;
        if ($title!="") {
            $title = "var disqus_title = '" . htmlspecialchars($title, ENT_QUOTES, "UTF-8") . "';\n";
        } else {
            $title = "";
        }

        $html = str_replace("%shortname%", $config['disqus_name'], $html);
        $html = str_replace("%url%", $app['resources']->getUrl('canonicalurl'), $html);
        $html = str_replace("%title%", $title, $html);

        return new \Twig_Markup($html, 'UTF-8');
    }

    public function disquslink($link)
    {
        $config = $this->getConfig();
        $app = $this->getContainer();

        $script = <<< EOM
<script type="text/javascript">
var disqus_shortname = '%shortname%';
(function () {
var s = document.createElement('script'); s.async = true;
s.type = 'text/javascript';
s.src = 'https://' + disqus_shortname + '.disqus.com/count.js';
(document.getElementsByTagName('HEAD')[0] || document.getElementsByTagName('BODY')[0]).appendChild(s);
}());
</script>

EOM;
        $script = str_replace("%shortname%", $config['disqus_name'], $script);

        $asset = new Snippet();
        $asset->setCallback($script)
            ->setLocation(Target::END_OF_BODY);
        $app['asset.queue.snippet']->add($asset);

        $html = '%hosturl%%link%#disqus_thread';
        $html = str_replace("%hosturl%", $app['resources']->getUrl('hosturl'), $html);
        $html = str_replace("%link%", $link, $html);

        return new \Twig_Markup($html, 'UTF-8');
    }

    protected function getDefaultConfig()
    {
        return ['disqus_name' => 'boltcm'];
    }

}
