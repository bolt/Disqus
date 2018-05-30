<?php

namespace Bolt\Extension\Bolt\Disqus;

use Bolt\Asset\Snippet\Snippet;
use Bolt\Asset\Target;
use Bolt\Collection\Bag;
use Bolt\Extension\SimpleExtension;
use Bolt\Version;
use Symfony\Component\HttpFoundation\Request;
use Twig\Markup;

/**
 * Disqus comment thread Extension for Bolt
 *
 * @author Xiao Hu Tai <xiao@twokings.nl>
 * @author Bob den Otter <bob@twokings.nl>
 * @author Gawain Lynch <gawain.lynch@gmail.com>
 */
class DisqusExtension extends SimpleExtension
{
    protected function registerTwigFunctions()
    {
        return [
            'disqus'     => 'disqus',
            'disquslink' => 'disquslink',
        ];
    }

    /**
     * @return Markup
     */
    public function disqus()
    {
        $config = $this->getConfig();
        $app = $this->getContainer();
        $request = Request::createFromGlobals();

        if (!$config->get('disqus_name')) {
            return new \Twig_Markup("<p>Please set the 'Disqus Short name' in <code>app/config/extensions/disqus.bolt.yml</code>.</p>", 'UTF-8');
        }

        $html = <<< EOM
        <div id="disqus_thread"></div>
        <script>
        var disqus_config = function () {
            this.page.url = '%url%';
            this.page.identifier = '%id%';
        };
        (function() {
            var d = document, s = d.createElement('script');
            s.src = 'https://%shortname%.disqus.com/embed.js';
            s.setAttribute('data-timestamp', +new Date());
            (d.head || d.body).appendChild(s);
        })();
        </script>
        <noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
EOM;

        $id = $request->server->get('REQUEST_URI');

        if ((version_compare(Version::forComposer(), 3.2, '>='))) {
            $canonical = $app['canonical']->getUrl();
        } else {
            $canonical = $app['resources']->getUrl('canonicalurl');
        }

        $html = str_replace('%shortname%', $config->get('disqus_name'), $html);
        $html = str_replace('%url%', $canonical, $html);
        $html = str_replace('%id%', $id, $html);

        return new Markup($html, 'UTF-8');
    }

    /**
     * @return Markup
     */
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
        $script = str_replace("%shortname%", $config->get('disqus_name'), $script);

        $asset = new Snippet();
        $asset->setCallback($script)
            ->setLocation(Target::END_OF_BODY);
        $app['asset.queue.snippet']->add($asset);

        $html = '%hosturl%%link%#disqus_thread';
        $html = str_replace('%hosturl%', $app['resources']->getUrl('hosturl'), $html);
        $html = str_replace('%link%', $link, $html);

        return new Markup($html, 'UTF-8');
    }

    protected function getDefaultConfig()
    {
        return ['disqus_name' => 'boltcm'];
    }

    protected function getConfig()
    {
        return $this->config = new Bag(parent::getConfig());
    }

}
