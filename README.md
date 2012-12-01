Kirby CMS - Stack Exchange Plugin (in progess)
==========================

What?
----
> Stack Exchange is a fast-growing network of 92 question and answer sites on diverse topics from software programming to cooking to photography and gaming.

So [Stack Exchange](http://stackexchange.com/) is a really huge community with a lot of great, creative, mind-blowing people.

And the cherry on the cake is… Stack Exchange provides a really good [API](http://api.stackexchange.com/ "Stack Exchange API")

Why?
----
So the last months I personally used Stack Exchange (in special Stack Overflow) a lot and the wish arose to show StackExchange Posts, Comments, Profiles etc. live on a Website without taking pictures of posts or copying text passages.

Furthermore I got in contact with [Kirby CMS](https://github.com/bastianallgeier/kirbycms "Kirby CMS") the last few weeks and I love to use this lightweight tool for personal usage.

So I decided to write this Stack Exchange Plugin for Kirby CMS also for my personal usage to show posts & answers from Stack Exchange (e.g. Stack Overflow Community) to discuss about web technologies etc.

How?
----
I want to write a fully flexible Stack Exchange plugin to provide everyone the full power of the API. I definitely want to use this plugin a lot in the future also for my personal blog, sites etc.

I hope you all like the idea and I hope you will give me feedback, feature, change or pull requests. ;-)

Set Up
-----
I plan to write a lot of functions for almost every API Use-Case. Indeed this is a longterm task, but I definitely want to make the life & work with this plugin as easy as possible.

You don't necessarily need to [register an Stack Exchange API Key](http://stackapps.com/apps/oauth/register), but you have fewer request permissions per day without.

You can use your key for requests by adding your API Key when you construct your stack exchange object:

`$stackExchangeObj = new stackexchange('YOUR API KEY');`

The plugin will use your api key for all further requests.

But again: The key parameter is optional. You don't need it.

Furthermore I plan to add a nice data output structure for release 1.0.

*So please let me know about your use cases, how you would use it or which features you want to have. This would be a really great help to get the right structure and features.*

Finally
-----

I hope you like it & I always love to come in touch with other developers on [twitter](http://www.twitter.com/andi1984).

Greets, 

Andreas



  

