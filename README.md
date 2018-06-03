Disqus
======

The "Disqus" extension inserts a Disqus comment thread in your templates. Before
use, you'll need to configure it by setting up a disqus account. Once you've
'created' a site in your Disqus account, you'll be able to see how to integrate
in into a website. From there, you can see the your unique Disqus sitename. In
the screenshot below, it's `your-sitename`

![disqus](https://user-images.githubusercontent.com/1833361/40667769-35bc7b00-6363-11e8-8336-2a09cd1f7334.png)

You can set this name in the `disqus.bolt.yml` configuration file, found in
`app/config/extensions/`.

```yaml
# Your Disqus shortname
disqus_name: your-sitename
```

Use it by simply placing the following in the template of a detail page, where
you want the Disqus comments to show up:

```html
    {{ disqus() }}
```

In your overview and listing pages, you can include a link to the comments,
where the 'Comment' text will be replaced with the actual amount of comments.

```html
    <a href="{{ disquslink(record.link) }}">Comment</a>
```

This is assuming `record` is the record. If not, replace it with the appropriate
variable name of the Record.
